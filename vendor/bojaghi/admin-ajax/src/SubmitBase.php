<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\AdminAjax;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Container as ContinyContainer;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Helper\Helper;
use Chwnam\ThreadsToPosts\Vendor\Psr\Container\ContainerExceptionInterface;
use Chwnam\ThreadsToPosts\Vendor\Psr\Container\ContainerInterface;

abstract class SubmitBase implements Module
{
    public const ALL_GRANTED = 'all-granted';
    public const ONLY_NOPRIV = 'only-nopriv';
    public const ONLY_PRIV   = 'only-priv';

    protected array               $config;
    protected ?ContainerInterface $container;
    protected array               $items;

    public function __construct(string|array $config = '', ?ContainerInterface $container = null)
    {
        $this->items     = [];
        $this->container = $container;

        [$assoc, $items] = Helper::separateArray(Helper::loadConfig($config));
        $this->config = wp_parse_args($assoc, ['checkContentType' => true]);

        // Check 'Content-Type' header, parse JSON content and push it into POST and REQUEST arrays.
        if ($this->config['checkContentType'] && !has_action('wp_loaded', [SubmitBase::class, 'parseStdinJson'])) {
            add_action('wp_loaded', [SubmitBase::class, 'parseStdinJson']);
        }

        $this->addItems($items);
    }

    /**
     * Add submission items.
     *
     * @param array $items        Array of items. Each item should be a maximum 5 length array or a string.
     *                            If the item is string, it is converted into an array.
     *                            Item array may be:
     *                            - 0th: action. e.g. wp_ajax_${action}
     *                            - 1st: callback. See README.md. Defaults to the same string as action.
     *                            - 2nd: priv type. ALL_GRANTED, ONLY_PRIV, ONLY_NOPRIV. Defaults to ONLY_PRIV.
     *                            - 3rd: nonce key. If provided, NONCE value is taken from $_REQUEST, and is verified.
     *                            You should set the NONCE value using action name, and set the value by this key.
     *                            Defaults to empty string (do not check).
     *                            - 4th: priority for add_action(). Defaults to 10.
     *
     * @return void
     */
    protected function addItems(array $items): void
    {
        foreach ($items as $item) {
            $item     = (array)$item;
            $action   = $item[0] ?? '';
            $callback = $item[1] ?? '';
            $privType = $item[2] ?? '';
            $nonceKey = $item[3] ?? '';
            $priority = $item[4] ?? null;

            if (empty($action)) {
                continue;
            }

            if (empty($callback)) {
                $callback = $action;
            }

            if (empty($privType)) {
                $privType = self::ONLY_PRIV;
            }

            if (empty($nonceKey)) {
                $nonceKey = '';
            }

            if (empty($priority)) {
                $priority = 10;
            }

            if (static::ONLY_PRIV === $privType || static::ALL_GRANTED === $privType) {
                add_action($this->getPrivAction($action), [$this, 'dispatch'], $priority);
            }

            if (static::ONLY_NOPRIV === $privType || static::ALL_GRANTED === $privType) {
                add_action($this->getNoPrivAction($action), [$this, 'dispatch'], $priority);
            }

            $this->items[$action] = [$callback, $nonceKey];
        }
    }

    abstract public function getPrivAction($tag): string;

    abstract public function getNoPrivAction($tag): string;

    public function dispatch(): void
    {
        $action = wp_unslash($_REQUEST['action'] ?? '');

        // Skip if not listed.
        if (!isset($this->items[$action])) {
            return;
        }

        [$callback, $nonceKey] = $this->items[$action];

        // NONCE check, if possible.
        if ($nonceKey && isset($_REQUEST[$nonceKey]) && !wp_verify_nonce($_REQUEST[$nonceKey], $action)) {
            wp_die('Nonce verification failed', 'NONCE failure', ['response' => 403]);
        }

        // Real invocation.
        $callback = $this->parseCallback($callback);
        if ($callback) {
            $supportsContiny = $this->container &&
                in_array(ContinyContainer::class, class_implements($this->container), true);
            if ($supportsContiny) {
                $this->container->call($callback);
            } else {
                call_user_func($callback);
            }
        }
        // For unit testing, do not exit here.
    }

    public function parseCallback(string|array|callable $callback): callable|null
    {
        if (is_callable($callback)) {
            return $callback;
        }

        if (is_string($callback)) {
            $split = explode('@', $callback, 2);
        } else {
            // array.
            $split = $callback;
        }

        if (2 === count($split)) {
            // 'foo@bar' style.
            $cls    = $split[0];
            $method = $split[1];

            if (
                (class_exists($cls) && method_exists($cls, $method)) || // Fully-qualified class name or instance.
                ($this->container?->has($cls) && is_string($cls))       // Identifier that the container knows.
            ) {
                if (is_callable([$cls, $method])) {
                    // Static methods.
                    return [$cls, $method];
                }
                // Common methods, the class needs to be instantiated,
                // Or $cls may be an alias for the container.
                try {
                    $instance = $this->container?->get($cls);
                    if (is_callable([$instance, $method])) {
                        return [$instance, $method];
                    }
                } catch (ContainerExceptionInterface $e) {
                }
            }
        } elseif (1 === count($split)) {
            // 'foo' style.
            // It may be class name, container alias.
            try {
                $instance = $this->container?->get($split[0]);
                // $instance may be an instance of class.
                // $instance also may be a method or a function, aliased for the container.
                if (is_callable($instance)) {
                    return $instance;
                }
            } catch (ContainerExceptionInterface $e) {
            }
        }

        return null;
    }

    /**
     * Parses JSON input from php://input if the request is a POST with 'Content-Type' as 'application/json'.
     * Decoded JSON parameters are sanitized using wp_unslash() and merged into $_POST and $_REQUEST.
     *
     * @return void
     */
    public static function parseStdinJson(): void
    {
        if ('application/json' === ($_SERVER['CONTENT_TYPE'] ?? '') && 'POST' === ($_SERVER['REQUEST_METHOD'] ?? '')) {
            $source = apply_filters('Bojaghi\\AdminAjax\\SubmitBase::parseStdin/source', 'php://input', $_POST['action'] ?? '');
            $input  = file_get_contents($source);
            $params = json_decode($input, true);

            if (JSON_ERROR_NONE === json_last_error() && is_array($params) && !empty($params)) {
                foreach ($params as $key => $value) {
                    // Unpacking of associative array is supported in PHP >= 8.1
                    $_REQUEST[$key] = $_POST[$key] = wp_unslash($value);
                }
            }
        }
    }
}

<?php
declare(strict_types=1);

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Template;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Helper\Helper;

final class Template
{
    /**
     * List of start() method call arguments.
     * It should be empty before template() ends.
     *
     * @var array
     */
    private array $buffers;

    /**
     * Context variables as associative array.
     *
     * @var array
     */
    private array $context;

    /**
     * Parent template name.
     *
     * @var string
     */
    private string $extends;

    /**
     * Supported file extensions.
     *
     * @var array
     */
    private array $extensions;

    /**
     * Supported file infix, e.g. my-template.infix.php
     *
     * @var string
     */
    private string $infix;

    /**
     * Absolute paths for retrieving template path
     *
     * @var array
     */
    private array $scopes;

    /**
     * Array of start(), end() call results.
     *
     * @var array
     */
    private array $store;

    public function __construct(array|string $args = '')
    {
        $args = Helper::loadConfig($args);
        $args = wp_parse_args(
            $args,
            [
                'extensions' => ['html', 'php'],
                'infix'      => '',
                'scopes'     => [],
            ],
        );

        $this->extensions = array_filter(array_unique(array_map(
            fn($e) => ltrim($e, '.'),
            (array)$args['extensions'],
        )));
        if (empty($this->extensions)) {
            throw new \InvalidArgumentException('Extensions cannot be empty.');
        }

        $infix       = '.' . trim($args['infix'], '.');
        $this->infix = '.' === $infix ? '' : $infix;

        $this->scopes = array_filter(array_unique(array_map(
            'untrailingslashit',
            (array)$args['scopes'],
        )));
        if (empty($this->scopes)) {
            throw new \InvalidArgumentException('Template scopes cannot be empty.');
        }

        $this->reset();
    }

    public function assign(string $key, mixed $value): self
    {
        $this->store[$key] = $value;

        return $this;
    }

    public function end(): void
    {
        $content = ob_get_clean();
        $name    = (string)array_pop($this->buffers);

        $this->store[$name] = $content;
    }

    public function extends(string $parentTemplate): self
    {
        $this->extends = $parentTemplate;

        return $this;
    }

    public function fetch(string $key, mixed $default = ''): mixed
    {
        return $this->store[$key] ?? $default;
    }

    public function get(string $key, mixed $default = ''): mixed
    {
        return $this->context[$key] ?? $default;
    }

    public function start(string $name): void
    {
        ob_start();

        /** @noinspection PhpArrayPushWithOneElementInspection */
        array_push($this->buffers, $name);
    }

    /**
     * @param string $tmplName Template name to render
     * @param array  $context  Context variables
     *
     * @return string
     */
    public function template(string $tmplName, array $context = []): string
    {
        $output = '';

        // Context
        if ($context) {
            $this->context = array_merge($this->context, $context);
        }
        unset($context);

        // 1st pass
        $tmplPath = $this->getTmplPath(trim($tmplName, '\\/'));
        if ($tmplPath) {
            ob_start();
            include $tmplPath;
            $output = $output . ob_get_clean();
        }

        // 2nd pass
        $parentPath = $this->getTmplPath(trim($this->extends, '\\/'));
        if ($parentPath) {
            ob_start();
            include $parentPath;
            $output = ob_get_clean() . $output;
        }

        // Buffer check
        if (!empty($this->buffers)) {
            throw new \BadMethodCallException('start(), and end() method pairs do not match.');
        }

        $this->reset();

        return $output;
    }

    public function fragment(string $fragmentName): string
    {
        $output     = '';
        $parentPath = $this->getTmplPath(trim($fragmentName, '\\/'));

        if ($parentPath) {
            ob_start();
            include $parentPath;
            $output = ob_get_clean() . $output;
        }

        return $output;
    }

    private function getTmplPath(string $tmplName): string
    {
        $tmplPath = '';

        foreach ($this->scopes as $scope) {
            foreach ($this->extensions as $ext) {
                $path = "$scope/$tmplName$this->infix.$ext";
                if (self::isValidTmpl($path)) {
                    $tmplPath = $path;
                    break 2;
                }
            }
        }

        return $tmplPath;
    }

    private function reset(): void
    {
        $this->buffers = [];
        $this->context = [];
        $this->extends = '';
        $this->store   = [];
    }

    private static function isValidTmpl(string $tmpl): bool
    {
        return $tmpl && file_exists($tmpl) && is_file($tmpl) && is_readable($tmpl);
    }
}

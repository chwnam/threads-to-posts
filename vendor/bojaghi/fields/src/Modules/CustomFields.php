<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Modules;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Meta\Meta;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Meta\MetaFactory;

class CustomFields implements Module
{
    /**
     * @var array{
     *     post: array<string, string>,
     *     term: array<string, string>,
     *     user: array<string, string>,
     * }
     *
     * Key: meta_key
     * Val: object_subtype
     */
    private array $map;

    public function __construct(string $configPath)
    {
        $this->map = [
            'post' => [],
            'term' => [],
            'user' => [],
        ];

        $this->loadConfig($configPath);
    }

    private function loadConfig(string $configPath): void
    {
        if (!file_exists($configPath) || !is_readable($configPath)) {
            return;
        }

        /**
         * @var array $config
         *
         * @example [
         *     'post' => [
         *         'meta_key' => [ ... ],
         *         '_foo'     => [ ... ],
         *     ],
         *     'user' => [
         *          'foo' => [ ... ],
         *     ],
         *     'term' => [
         *         'meta_key_foo' => [ ... ],
         *     ],
         * ]
         */
        $config = include($configPath);

        if (!is_array($config) || empty($config)) {
            return;
        }

        foreach ($config as $objectType => $items) {
            foreach ($items as $key => $item) {
                /**
                 * @var array $item
                 * @see MetaFactory::parseArgs()
                 */
                if (MetaFactory::add($key, $objectType, $item)) {
                    $this->map[$objectType][$key] = $item['object_subtype'] ?? '';
                };
            }
        }
    }

    /**
     * Alias of getField('post', $key)
     *
     * @param string $key
     *
     * @return Meta|null
     */
    public function getPostMeta(string $key): Meta|null
    {
        return $this->getField('post', $key);
    }

    /**
     * Get Meta object
     *
     * @param string $objectType
     * @param string $key
     *
     * @return Meta|null
     */
    public function getField(string $objectType, string $key): Meta|null
    {
        if (isset($this->map[$objectType][$key])) {
            $objectSubtype = $this->map[$objectType][$key];
            return MetaFactory::get($key, $objectType, $objectSubtype);
        }

        return null;
    }

    /**
     * Alias of getField('term', $key)
     *
     * @param string $key
     *
     * @return Meta|null
     */
    public function getTermMeta(string $key): Meta|null
    {
        return $this->getField('term', $key);
    }

    /**
     * Alias of getField('user', $key)
     *
     * @param string $key
     *
     * @return Meta|null
     */
    public function getUserMeta(string $key): Meta|null
    {
        return $this->getField('user', $key);
    }
}

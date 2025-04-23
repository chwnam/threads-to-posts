<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Modules;

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Contract\Module;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Option\Option;
use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Option\OptionFactory;

class Options implements Module
{
    /**
     * @var array<string, true> $map
     *
     * Key: option name
     * Val: true
     */
    private array $map;

    public function __construct(string $configPath)
    {
        $this->map = [];

        $this->loadConfig($configPath);
    }

    public function __get(string $name)
    {
        return $this->getOption($name);
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
         *     'option_name' => [ ... ],
         *     'foo'         => [ ... ],
         *     'bar'         => [ ... ],
         * ]
         */
        $config = include($configPath);

        if (!is_array($config) || empty($config)) {
            return;
        }

        foreach ($config as $key => $item) {
            /**
             * @var array $item
             * @see OptionFactory::parseArgs()
             */
            OptionFactory::add($key, $item);
            $this->map[$key] = true;
        }
    }

    /**
     * Get Meta object
     *
     * @param string $key
     *
     * @return Option|null
     */
    public function getOption(string $key): Option|null
    {
        if (isset($this->map[$key])) {
            return OptionFactory::get($key);
        }

        return null;
    }
}

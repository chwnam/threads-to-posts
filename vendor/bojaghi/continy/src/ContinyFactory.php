<?php

declare(strict_types=1);

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Continy;

class ContinyFactory
{
    /**
     * @throws \Bojaghi\Continy\ContinyException
     */
    public static function create(array|string $setup): Continy
    {
        if (is_string($setup)) {
            if (empty($setup) || ! file_exists($setup) || ! is_readable($setup)) {
                throw new ContinyException(
                    'When $setup is a string, it should be an existing file path. ' .
                    "File '$setup' cannot be found.",
                );
            }
            $setup = (array)include $setup;
        }

        // Try to guess mainFile, but it is discouraged.
        if (empty($setup['main_file'])) {
            $trace = debug_backtrace();
            $top   = array_shift($trace);
            if ($top) {
                $setup['main_file'] = $top['file'];
            }
        }

        // Explicit version string is recommended.
        if (empty($setup['version'])) {
            $setup['version'] = '0.0.0';
        }

        return new Continy($setup);
    }
}

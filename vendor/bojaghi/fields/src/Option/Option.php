<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Option;

use Chwnam\ThreadsToPosts\Vendor\JetBrains\PhpStorm\ArrayShape;

/**
 * @property-read string        $type
 * @property-read string        $group
 * @property-read string        $label
 * @property-read string        $description
 * @property-read callable|null $sanitize_callback
 * @property-read array|bool    $show_in_reset
 * @property-read mixed         $default
 * @property-read bool          $autoload
 * @property-read callable|null $get_filter
 */
class Option
{
    public function __construct(
        protected string $name,
        #[ArrayShape(OptionFactory::ARGS_SHAPE)]
        protected array  $args,
    )
    {
    }

    public function __get(string $name)
    {
        return $this->args[$name] ?? null;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function add(mixed $value): bool
    {
        return add_option($this->name, $value, '', $this->autoload);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return delete_option($this->name);
    }

    /**
     * @param bool $defaultValue
     *
     * @return mixed
     */
    public function get(): mixed
    {
        $value = get_option($this->name);

        if (is_callable($this->get_filter)) {
            $value = call_user_func_array($this->get_filter, [$value, $this->name, $this]);
        }

        return $value;
    }

    public function getKey(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function update(mixed $value): bool
    {
        return update_option($this->name, $value, $this->autoload);
    }
}

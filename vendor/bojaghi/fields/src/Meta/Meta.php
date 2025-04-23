<?php

namespace Chwnam\ThreadsToPosts\Vendor\Bojaghi\Fields\Meta;

use Chwnam\ThreadsToPosts\Vendor\JetBrains\PhpStorm\ArrayShape;
use WP_Post;
use WP_Term;
use WP_User;


/**
 * @property-read string        $object_subtype
 * @property-read string        $type
 * @property-read bool          $single
 * @property-read mixed         $default
 * @property-read callable|null $sanitize_callback
 * @property-read callable|null $auth_callback
 * @property-read array|bool    $show_in_reset
 * @property-read bool          $revisions_enabled
 * @property-read callable|null $get_filter
 */
class Meta
{
    public function __construct(
        protected string                                       $key,
        protected string                                       $objectType,
        #[ArrayShape(MetaFactory::ARGS_SHAPE)] protected array $args,
    )
    {
    }

    public function __get(string $name)
    {
        return $this->args[$name] ?? null;
    }

    /**
     * @param object|string|int $objectId
     * @param mixed             $value
     * @param bool              $unique
     *
     * @return int|false
     */
    public function add(object|string|int $objectId, mixed $value, bool $unique = false): int|false
    {
        return add_metadata($this->objectType, static::safeId($objectId), $this->key, $value, $unique);
    }

    /**
     * @param mixed $objectId
     *
     * @return int
     */
    protected static function safeId(mixed $objectId): int
    {
        if (is_object($objectId)) {
            if ($objectId instanceof WP_Post || $objectId instanceof WP_User) {
                return $objectId->ID;
            } elseif ($objectId instanceof WP_Term) {
                return $objectId->term_id;
            }

            if (isset($objectId->ID)) {
                return $objectId->ID;
            } elseif (isset($objectId->id)) {
                return $objectId->id;
            }
        } elseif (is_string($objectId) && is_numeric($objectId)) {
            return (int)$objectId;
        } elseif (is_int($objectId)) {
            return $objectId;
        }

        return 0;
    }

    /**
     * @param object|string|int $objectId
     * @param mixed             $value
     *
     * @return bool
     */
    public function delete(object|string|int $objectId, mixed $value = ''): bool
    {
        return delete_metadata($this->objectType, static::safeId($objectId), $this->key, $value);
    }

    /**
     * @param object|string|int $objectId
     *
     * @return mixed
     */
    public function get(object|string|int $objectId): mixed
    {
        $value = get_metadata($this->objectType, static::safeId($objectId), $this->key, $this->single);

        if (is_callable($this->get_filter)) {
            $value = call_user_func_array($this->get_filter, [$value, $objectId, $this]);
        }

        return $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * @param object|string|int $objectId
     * @param mixed             $value
     * @param mixed             $prevValue
     *
     * @return bool|int
     */
    public function update(
        object|string|int $objectId,
        mixed             $value,
        mixed             $prevValue = '',
    ): bool|int
    {
        return update_metadata($this->objectType, static::safeId($objectId), $this->key, $value, $prevValue);
    }
}

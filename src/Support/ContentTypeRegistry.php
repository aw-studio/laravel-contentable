<?php

namespace AwStudio\Contentable\Support;

use AwStudio\Contentable\Contracts\ContentType;

class ContentTypeRegistry
{
    protected static array $types = [];

    public static function register(string $typeClass): void
    {
        $type = $typeClass::type();
        static::$types[$type] = $typeClass;
    }

    public function all(): array
    {
        return $this->types;
    }

    public static function resolve(string $type): ?string
    {
        return static::$types[$type] ?? null;
    }

    public static function schema(string $type): ?array
    {
        $class = static::resolve($type);

        if (! $class) {
            return null;
        }

        /** @var ContentType $class */
        return [
            'type' => $class::type(),
            'fields' => $class::fields(),
            'rules' => $class::rules(),
            'default' => $class::default(),
        ];
    }
}

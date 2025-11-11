<?php

namespace AwStudio\Contentable;

class ContentRegistry
{
    protected static array $types = [];

    public static function register(string $typeClass): void
    {
        $type = $typeClass::type();
        static::$types[$type] = $typeClass;
    }

    public static function all(): array
    {
        return static::$types;
    }

    public static function resolve(string $type): ?string
    {
        return static::$types[$type] ?? null;
    }
}

<?php

namespace AwStudio\Contentable\Contracts;

interface ContentType
{
    public static function type(): string;

    public static function rules(): array;

    public static function default(): array;
}

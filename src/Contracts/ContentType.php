<?php

namespace AwStudio\Contentable\Contracts;

interface ContentType
{
    public static function type(): string;

    public static function rules(): array;

    public static function default(): array;

    /**
     * Field metadata sent to the editor.
     */
    public static function fields(): array;
}

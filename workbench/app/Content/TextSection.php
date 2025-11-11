<?php

namespace Workbench\App\Content;

use AwStudio\Contentable\Contracts\ContentType;

class TextSection implements ContentType
{
    public static function type(): string
    {
        return 'text-section';
    }

    public static function rules(): array
    {
        return ['headline' => 'required'];
    }

    public static function default(): array
    {
        return ['headline' => ''];
    }
}

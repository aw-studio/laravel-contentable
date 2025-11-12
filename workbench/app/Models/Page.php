<?php

namespace Workbench\App\Models;

use AwStudio\Contentable\Traits\HasContent;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /** @use HasFactory<\Workbench\Database\Factories\PageFactory> */
    use HasContent, HasFactory, HasUlids;

    protected $fillable = [
        'slug',
        'published_at',
        'is_active',
    ];

    protected array $blocks = [
        'content' => [
            \Workbench\App\Content\TextSection::class,
        ],
    ];

    public static function newFactory()
    {
        return \Workbench\Database\Factories\PageFactory::new();
    }
}

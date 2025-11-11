<?php

namespace Workbench\App\Models;

use AwStudio\Contentable\Traits\Contentable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /** @use HasFactory<\Workbench\Database\Factories\PageFactory> */
    use Contentable, HasFactory, HasUlids;

    protected $fillable = [
        'slug',
        'published_at',
        'is_active',
    ];

    protected array $contentFields = [
        'content' => [
            \Workbench\App\Content\TextSection::class,
        ],
    ];

    public static function newFactory()
    {
        return \Workbench\Database\Factories\PageFactory::new();
    }
}

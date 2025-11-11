<?php

namespace AwStudio\Contentable\Traits;

use AwStudio\Contentable\Models\Content;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Contentable
{
    public function contents(): MorphMany
    {
        return $this->morphMany(Content::class, 'contentable')->orderBy('order');
    }

    // Optional helpers to filter by key
    public function contentByKey(string $key)
    {
        return $this->contents()->where('key', $key)->get();
    }
}

<?php

namespace AwStudio\Contentable\Traits;

use AwStudio\Contentable\Models\Content;

trait HasContent
{
    /**
     * MorphMany relationship to Content model.
     */
    public function content()
    {
        return $this->morphMany(Content::class, 'contentable')
            ->orderBy('order');
    }

    /**
     * Get allowed content type classes for a given key.
     */
    public function allowedContentTypes(string $key): array
    {
        return $this->contentFields[$key] ?? [];
    }

    /**
     * Get content blocks for a specific key.
     */
    public function contentFor(string $key)
    {
        return $this->content()->where('key', $key)->orderBy('order');
    }

    public function groupedContent(): array
    {
        return $this->content->groupBy('key')->map(function ($items) {
            return $items->sortBy('order')->values();
        })->toArray();
    }
}

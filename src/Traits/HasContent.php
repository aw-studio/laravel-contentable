<?php

namespace AwStudio\Contentable\Traits;

use AwStudio\Contentable\Models\Content;
use AwStudio\Contentable\Support\ContentTypeRegistry;

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
        return $this->blocks[$key] ?? [];
    }

    public function getContent(string $key)
    {
        $this->loadMissing('content');

        return $this->content->where('key', $key)->sortBy('order')->values();
    }

    /**
     * Get content blocks for a specific key.
     */
    public function contentFor(string $key)
    {
        return $this->content()->where('key', $key)->orderBy('order');
    }

    public function getBlocks(): array
    {
        return $this->content->groupBy('key')->map(function ($items) {
            return $items->sortBy('order')->values();
        })->toArray();
    }

    public function getBlockDefinitions(): array
    {
        $registry = app(ContentTypeRegistry::class);
        $fields = [];

        foreach ($this->blocks as $key => $classes) {
            $fields[$key] = [];

            foreach ($classes as $class) {
                $typeClass = $registry->resolve($class::type());

                if ($typeClass) {
                    $fields[$key][$typeClass::type()] = $typeClass::fields();
                }
            }
        }

        return $fields;
    }
}

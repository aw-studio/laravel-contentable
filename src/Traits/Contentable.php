<?php

namespace AwStudio\Contentable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Contentable
{
    public function content(): MorphMany
    {
        return $this->morphMany(\AwStudio\Contentable\Models\Content::class, 'contentable')
            ->orderBy('order');
    }

    public function getContent(string $key)
    {
        return $this->content()->where('key', $key)->orderBy('order');
    }

    public function allowedContentTypes(string $key): array
    {
        return $this->contentFields[$key] ?? [];
    }
}

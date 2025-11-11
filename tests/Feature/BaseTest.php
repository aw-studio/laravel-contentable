<?php

use AwStudio\Contentable\Models\Content;
use Workbench\App\Models\Page;

it('can create a content block for a contentable model', function () {
    // Create a sample page
    $page = Page::factory()->create();

    // Add a content block
    $content = $page->contents()->create([
        'key' => 'hero_text',
        'content' => ['value' => 'Welcome to my site!'],
        'order' => 0,
    ]);

    // Assertions
    expect($content)->toBeInstanceOf(Content::class)
        ->and($content->key)->toBe('hero_text')
        ->and($content->content['value'])->toBe('Welcome to my site!')
        ->and($content->contentable->id)->toBe($page->id);

    // Ensure it is saved in the database
    $this->assertDatabaseHas('content', [
        'id' => $content->id,
        'contentable_type' => Page::class,
        'contentable_id' => $page->id,
        'key' => 'hero_text',
    ]);
});

<?php

// Helper to build route payloads

use AwStudio\Contentable\Support\ContentTypeRegistry;
use Workbench\App\Models\Page;

function contentPayload($page, $key = 'hero_text', $content = ['value' => 'Test Content'], $order = 0, $type = 'Text')
{
    return [
        'contentable_type' => Page::class,
        'contentable_id' => $page->id,
        'key' => $key,
        'content' => $content,
        'order' => $order,
        'type' => $type,
    ];
}

it('can add a content block via store endpoint', function () {
    $page = Page::factory()->create();

    // Make sure the type is registered
    ContentTypeRegistry::register(\Workbench\App\Content\TextSection::class);

    $payload = [
        'contentable_type' => Page::class,
        'contentable_id' => $page->id,
        'key' => 'content',
        'type' => \Workbench\App\Content\TextSection::type(),
        'content' => [
            'headline' => 'Welcome!',
            'text' => 'Lorem ipsum',
        ],
        'order' => 0,
    ];

    $response = $this->postJson('/content', $payload);

    $response->assertCreated()
        ->assertJsonFragment(['key' => 'content']);

    $this->assertDatabaseHas('content', [
        'key' => 'content',
        'contentable_id' => $page->id,
        'contentable_type' => Page::class,
        'content' => json_encode(['headline' => 'Welcome!', 'text' => 'Lorem ipsum']),
    ]);
});

it('can update a content block', function () {
    $page = Page::factory()->create();
    $content = $page->content()->create(['key' => 'hero_text', 'content' => ['value' => 'Old'], 'order' => 0, 'type' => 'Text']);

    $response = $this->putJson("/content/{$content->id}", [
        'content' => ['value' => 'Updated'],
        'order' => 1,
    ]);

    $response->assertOk()
        ->assertJsonFragment(['content' => ['value' => 'Updated']]);

    $this->assertDatabaseHas('content', [
        'id' => $content->id,
        'content' => json_encode(['value' => 'Updated']),
        'order' => 1,
    ]);
});

it('can delete a content block', function () {
    $page = Page::factory()->create();
    $content = $page->content()->create(['key' => 'hero_text', 'content' => ['value' => 'DeleteMe'], 'order' => 0, 'type' => 'Text']);

    $response = $this->deleteJson("/content/{$content->id}");

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Content deleted']);

    $this->assertDatabaseMissing('content', ['id' => $content->id]);
});

it('can reorder multiple content blocks', function () {
    $page = Page::factory()->create();
    $block1 = $page->content()->create(['key' => 'text', 'content' => ['value' => 'A'], 'order' => 0, 'type' => 'Text']);
    $block2 = $page->content()->create(['key' => 'text', 'content' => ['value' => 'B'], 'order' => 1, 'type' => 'Text']);
    $block3 = $page->content()->create(['key' => 'text', 'content' => ['value' => 'C'], 'order' => 2, 'type' => 'Text']);

    $payload = [
        'contentable_type' => Page::class,
        'contentable_id' => $page->id,
        'order' => [$block3->id, $block1->id, $block2->id],
    ];

    $response = $this->postJson('/content/reorder', $payload);
    $response->assertOk()->assertJsonFragment(['message' => 'Content reordered']);

    $orders = $page->content()->pluck('order', 'id')->toArray();

    expect($orders[$block3->id])->toBe(0);
    expect($orders[$block1->id])->toBe(1);
    expect($orders[$block2->id])->toBe(2);
});

<?php

// Helper to build route payloads

use Workbench\App\Models\Page;

function contentPayload($page, $key = 'hero_text', $content = ['value' => 'Test Content'], $order = 0)
{
    return [
        'contentable_type' => Page::class,
        'contentable_id' => $page->id,
        'key' => $key,
        'content' => $content,
        'order' => $order,
    ];
}

it('can list content blocks for a page', function () {
    $page = Page::factory()->create();
    $page->contents()->create(['key' => 'hero_text', 'content' => ['value' => 'Hello']]);

    $response = $this->getJson('/content?contentable_type='.Page::class.'&contentable_id='.$page->id);

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['key' => 'hero_text', 'content' => ['value' => 'Hello']]);
});

it('can add a content block via store endpoint', function () {
    $page = Page::factory()->create();
    $payload = contentPayload($page);

    $response = $this->postJson('/content', $payload);

    $response->assertCreated()
        ->assertJsonFragment(['key' => 'hero_text']);

    $this->assertDatabaseHas('content', [
        'key' => 'hero_text',
        'contentable_id' => $page->id,
        'contentable_type' => Page::class,
    ]);
});

it('can update a content block', function () {
    $page = Page::factory()->create();
    $content = $page->contents()->create(['key' => 'hero_text', 'content' => ['value' => 'Old'], 'order' => 0]);

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
    $content = $page->contents()->create(['key' => 'hero_text', 'content' => ['value' => 'DeleteMe']]);

    $response = $this->deleteJson("/content/{$content->id}");

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Content deleted']);

    $this->assertDatabaseMissing('content', ['id' => $content->id]);
});

it('can reorder multiple content blocks', function () {
    $page = Page::factory()->create();
    $block1 = $page->contents()->create(['key' => 'text', 'content' => ['value' => 'A'], 'order' => 0]);
    $block2 = $page->contents()->create(['key' => 'text', 'content' => ['value' => 'B'], 'order' => 1]);
    $block3 = $page->contents()->create(['key' => 'text', 'content' => ['value' => 'C'], 'order' => 2]);

    $payload = [
        'contentable_type' => Page::class,
        'contentable_id' => $page->id,
        'order' => [$block3->id, $block1->id, $block2->id],
    ];

    $response = $this->postJson('/content/reorder', $payload);
    $response->assertOk()->assertJsonFragment(['message' => 'Content reordered']);

    $orders = $page->contents()->pluck('order', 'id')->toArray();

    expect($orders[$block3->id])->toBe(0);
    expect($orders[$block1->id])->toBe(1);
    expect($orders[$block2->id])->toBe(2);
});

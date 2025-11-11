<?php

use AwStudio\Contentable\Support\ContentTypeRegistry;
use Illuminate\Testing\Fluent\AssertableJson;
use Workbench\App\Content\TextSection;

beforeEach(function () {
    // Ensure the registry has our test type
    $this->registry = app(ContentTypeRegistry::class);
    $this->registry->register(TextSection::class);
});

it('returns schema for a registered content type', function () {
    $response = $this->getJson('/content/schema/text-section');

    // dd($response->json());
    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json->where('type', 'text-section')
            ->has('fields.headline.label')
            ->has('fields.headline.editor')
            ->etc()
        );
});

it('returns 404 for unknown content type', function () {
    $response = $this->getJson('/content/schema/unknown-type');

    $response->assertStatus(404)
        ->assertJson([
            'error' => 'Unknown content type',
        ]);
});

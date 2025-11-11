<?php

namespace AwStudio\Contentable\Http\Controllers;

use AwStudio\Contentable\Models\Content;
use AwStudio\Contentable\Support\ContentTypeRegistry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContentController extends Controller
{
    /**
     * Add a new content block for a contentable model.
     */
    public function store(Request $request)
    {
        // Base validation
        $request->validate([
            'contentable_type' => 'required|string',
            'contentable_id' => 'required|string',
            'key' => 'required|string',
            'type' => 'required|string',
            'content' => 'required|array',
            'order' => 'sometimes|integer',
        ]);

        $modelClass = $request->input('contentable_type');
        $modelId = $request->input('contentable_id');
        $key = $request->input('key');
        $type = $request->input('type');

        // Resolve content type class
        $registry = app(ContentTypeRegistry::class);
        $typeClass = $registry->resolve($type);

        if (! $typeClass) {
            return response()->json(['error' => 'Invalid content type'], 422);
        }

        // Validate nested content fields
        $contentRules = collect($typeClass::rules())
            ->mapWithKeys(fn ($rule, $field) => ["content.$field" => $rule])
            ->toArray();

        $request->validate($contentRules);

        // Load the parent model
        $model = $modelClass::find($modelId);
        if (! $model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        // Check if the type is allowed for this key
        $allowedClasses = $model->allowedContentTypes($key);
        $allowedTypes = array_map(fn ($c) => $c::type(), $allowedClasses);

        if (! in_array($type, $allowedTypes, true)) {
            return response()->json([
                'error' => "Block type [$type] is not allowed for key [$key]",
            ], 422);
        }

        // Create content block
        $content = $model->content()->create([
            'key' => $key,
            'type' => $type,
            'content' => $request->input('content'),
            'order' => $request->input('order', 0),
        ]);

        return response()->json($content, 201);
    }

    /**
     * Update a content block by its ID.
     */
    public function update(Request $request, $id)
    {
        $content = Content::find($id);

        if (! $content) {
            return response()->json(['error' => 'Content not found'], 404);
        }

        // Resolve class for this content block's type
        $typeClass = ContentTypeRegistry::resolve($content->type);

        // Build rules only if the content object supports validation
        $rules = [];

        if ($request->has('content')) {
            if ($typeClass) {
                $rules['content'] = 'required|array';

                $nestedRules = collect($typeClass::rules())
                    ->mapWithKeys(fn ($rule, $field) => ["content.$field" => $rule])
                    ->toArray();

                $rules = array_merge($rules, $nestedRules);
            } else {
                // Fallback: content must be an array
                $rules['content'] = 'required|array';
            }
        }

        if ($request->has('order')) {
            $rules['order'] = 'integer';
        }

        // Validate request based on dynamically built rules
        $request->validate($rules);

        // Update content fields (only what is provided)
        $content->update([
            'content' => $request->input('content', $content->content),
            'order' => $request->input('order', $content->order),
        ]);

        return response()->json($content);
    }

    /**
     * Delete a content block by its ID.
     */
    public function destroy($id)
    {
        $content = Content::find($id);
        if (! $content) {
            return response()->json(['error' => 'Content not found'], 404);
        }

        $content->delete();

        return response()->json(['message' => 'Content deleted']);
    }

    /**
     * Reorder multiple content blocks for a contentable model.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'contentable_type' => 'required|string',
            'contentable_id' => 'required|string',
            'order' => 'required|array',
        ]);

        $modelClass = $request->input('contentable_type');
        $modelId = $request->input('contentable_id');

        if (! class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid contentable_type'], 422);
        }

        $model = $modelClass::find($modelId);
        if (! $model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        foreach ($request->input('order') as $index => $id) {
            $block = Content::find($id);
            if ($block
                && $block->contentable_id == $model->id
                && $block->contentable_type == $modelClass
            ) {
                $block->update(['order' => $index]);
            }
        }

        return response()->json(['message' => 'Content reordered']);
    }
}

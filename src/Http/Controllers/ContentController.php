<?php

namespace AwStudio\Contentable\Http\Controllers;

use AwStudio\Contentable\ContentRegistry;
use AwStudio\Contentable\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContentController extends Controller
{
    /**
     * Add a new content block for a contentable model.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contentable_type' => 'required|string',
            'contentable_id' => 'required|string',
            'key' => 'required|string',
            'content' => 'required|array',
            'order' => 'sometimes|integer',
            'type' => 'required|string',
        ]);

        $modelClass = $request->input('contentable_type');
        $modelId = $request->input('contentable_id');
        $key = $request->input('key');
        $type = $request->input('type');

        if (! class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid contentable_type'], 422);
        }

        $model = $modelClass::find($modelId);
        if (! $model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        // Check allowed types for the key
        $allowedClasses = $model->allowedContentTypes($key);
        $allowedTypes = array_map(fn ($class) => $class::type(), $allowedClasses);

        if (! in_array($type, $allowedTypes)) {
            return response()->json(['error' => "Block type [$type] is not allowed for key [$key]"], 422);
        }

        // Resolve content type class
        $typeClass = ContentRegistry::resolve($type);
        if (! $typeClass) {
            return response()->json(['error' => "Content type [$type] not registered"], 422);
        }

        // Nested validation inside 'content'
        $rules = collect($typeClass::rules())
            ->mapWithKeys(fn ($rule, $field) => ["content.$field" => $rule])
            ->toArray();

        $request->validate(array_merge(['content' => 'required|array'], $rules));

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

        // Resolve the type class for validation
        $typeClass = ContentRegistry::resolve($content->type);

        if ($typeClass) {
            $rules = collect($typeClass::rules())
                ->mapWithKeys(fn ($rule, $field) => ["content.$field" => $rule])
                ->toArray();

            $request->validate(array_merge(['content' => 'required|array'], $rules));
        } else {
            $request->validate(['content' => 'required|array']);
        }

        $content->update([
            'content' => $request->input('content'),
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

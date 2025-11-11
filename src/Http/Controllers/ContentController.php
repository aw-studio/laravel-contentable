<?php

namespace AwStudio\Contentable\Http\Controllers;

use AwStudio\Contentable\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContentController extends Controller
{
    /**
     * List content blocks for a contentable model.
     *
     * Optional: filter by key.
     */
    public function index(Request $request)
    {
        $request->validate([
            'contentable_type' => 'required|string',
            'contentable_id' => 'required|string',
            'key' => 'sometimes|string',
        ]);

        $modelClass = $request->input('contentable_type');

        if (! class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid contentable_type'], 422);
        }

        $model = $modelClass::find($request->input('contentable_id'));
        if (! $model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $query = $model->contents()->orderBy('order');

        if ($key = $request->input('key')) {
            $query->where('key', $key);
        }

        $contents = $query->get();

        return response()->json($contents);
    }

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
        ]);

        $modelClass = $request->input('contentable_type');

        if (! class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid contentable_type'], 422);
        }

        $model = $modelClass::find($request->input('contentable_id'));
        if (! $model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $content = $model->contents()->create([
            'key' => $request->input('key'),
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
        $request->validate([
            'content' => 'required|array',
            'order' => 'sometimes|integer',
        ]);

        $content = Content::find($id);
        if (! $content) {
            return response()->json(['error' => 'Content not found'], 404);
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
     *
     * Accepts an array of IDs in the desired order:
     * [
     *   3, 1, 5, 2
     * ]
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'contentable_type' => 'required|string',
            'contentable_id' => 'required|string',
            'order' => 'required|array',
        ]);

        $modelClass = $request->input('contentable_type');

        if (! class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid contentable_type'], 422);
        }

        $model = $modelClass::find($request->input('contentable_id'));
        if (! $model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        foreach ($request->input('order') as $index => $id) {
            $content = Content::find($id);
            if ($content && $content->contentable_id == $model->id && $content->contentable_type == $modelClass) {
                $content->update(['order' => $index]);
            }
        }

        return response()->json(['message' => 'Content reordered']);
    }
}

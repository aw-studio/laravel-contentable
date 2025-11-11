<?php

namespace AwStudio\Contentable\Http\Controllers;

use AwStudio\Contentable\Support\ContentTypeRegistry;
use Illuminate\Routing\Controller;

class ContentSchemaController extends Controller
{
    public function show(string $type, ContentTypeRegistry $registry)
    {
        $schema = $registry->schema($type);

        if (! $schema) {
            return response()->json(['error' => 'Unknown content type'], 404);
        }

        return response()->json($schema);
    }
}

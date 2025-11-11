<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Specify the middleware to be applied to the content routes.
    | This can be an array of middleware names.
    | Example: ['api', 'auth', 'can:edit-content']
    |
    */

    'middleware' => ['api'],

    'prefix' => 'content',

    /*
     |--------------------------------------------------------------------------
     | Content Type Class Paths
     |--------------------------------------------------------------------------
     |
     */
    'paths' => [
        app_path('Content'), // default location for content type classes
    ],

    'default_order_column' => 'order',
    'content_model' => AwStudio\Contentable\Models\Content::class,
];

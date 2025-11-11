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

    'default_order_column' => 'order',
    'content_model' => AwStudio\Contentable\Models\Content::class,
];

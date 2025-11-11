<?php

use AwStudio\Contentable\Http\Controllers\ContentController;
use AwStudio\Contentable\Http\Controllers\ContentSchemaController;
use Illuminate\Support\Facades\Route;

$middleware = config('contentable.middleware', ['web']);
$prefix = config('contentable.prefix', 'content');

Route::prefix($prefix)->middleware($middleware)->group(function () {
    Route::post('/', [ContentController::class, 'store']);
    Route::put('/{id}', [ContentController::class, 'update']);
    Route::delete('/{id}', [ContentController::class, 'destroy']);
    Route::post('/reorder', [ContentController::class, 'reorder']);

    Route::get('/schema/{type}', [ContentSchemaController::class, 'show']);
});

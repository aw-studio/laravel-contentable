<?php

use AwStudio\Contentable\Http\Controllers\ContentController;
use Illuminate\Support\Facades\Route;

$middleware = config('contentable.middleware', ['web']);

Route::prefix('content')->middleware($middleware)->group(function () {
    Route::get('/', [ContentController::class, 'index']);
    Route::post('/', [ContentController::class, 'store']);
    Route::put('/{id}', [ContentController::class, 'update']);
    Route::delete('/{id}', [ContentController::class, 'destroy']);
    Route::post('/reorder', [ContentController::class, 'reorder']);
});

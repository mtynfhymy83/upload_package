<?php


use Illuminate\Support\Facades\Route;
use Matin\Media\Http\Controllers\MediaController;

Route::prefix('api/media')->group(function () {

    Route::post('temp-upload', [MediaController::class, 'tempUpload']);
    Route::post('commit-upload', [MediaController::class, 'commitUpload']);
    Route::get('{model_type}/{model_id}', [MediaController::class, 'index']);
    Route::post('delete', [MediaController::class, 'destroyByPath']);

});

<?php


use Illuminate\Support\Facades\Route;
use Matin\Media\Http\Controllers\MediaController;

Route::prefix('api/media')->group(function () {

    Route::post('temp-upload', [MediaController::class, 'tempUpload']);

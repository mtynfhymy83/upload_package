<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary Disk
    |--------------------------------------------------------------------------
    |
    | Disk used for temporary file storage before final upload.
    |
    */
    'temp_disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Final Disk
    |--------------------------------------------------------------------------
    |
    | Disk used for permanent file storage (e.g., S3, Liara).
    |
    */
    'final_disk' => 'liara',
    'use_queue' => false,
//    'models' => [
//    'product' => \App\Models\Product::class,
//    'user' => \App\Models\User::class,]
];

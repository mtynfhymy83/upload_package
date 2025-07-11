<?php


namespace Matin\Media\Services;

use Illuminate\Support\Facades\Storage;
use Matin\Media\Models\Media;

class MediaService
{
    public function storeTempFile($file): string
    {
        return $file->store('local', 'public');
    }

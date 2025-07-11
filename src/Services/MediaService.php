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
    public function moveFileToPermanentStorage(string $tempPath, string $modelType, int $modelId, string $originalName, string $collection = 'default'): ?string
    {
        try {
            $tempDisk = config('media.temp_disk', 'public');
            $finalDisk = config('media.final_disk', 'liara');

            // 1. Check if file exists in temp storage
            if (!Storage::disk($tempDisk)->exists($tempPath)) {
                throw new \Exception("File not found in temporary storage at path: {$tempPath}");
            }

            // 2. Read file contents
            $fileContents = Storage::disk($tempDisk)->get($tempPath);
            if ($fileContents === false) {
                throw new \Exception("Failed to read file contents from temporary storage");
            }

            // 3. Generate new file name
            $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
            $fileName = 'media/' . uniqid() . ($extension ? '.' . $extension : '');

            // 4. Save to permanent storage
            $uploaded = Storage::disk($finalDisk)->put($fileName, $fileContents);
            if (!$uploaded) {
                throw new \Exception("Failed to upload file to permanent storage");

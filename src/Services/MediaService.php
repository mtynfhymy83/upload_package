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
            }
            if (!str_contains($modelType, '\\')) {
                $modelType = 'App\\Models\\' . ucfirst($modelType);
            }

            // 5. Find the model
            $model = app($modelType)::find($modelId);
            if (!$model) {
                throw new \Exception("Model not found with type: {$modelType} and ID: {$modelId}");
            }

            // 6. Get file metadata
            $mimeType = Storage::disk($finalDisk)->mimeType($fileName);
            $size = Storage::disk($finalDisk)->size($fileName);

            if (!$mimeType || !$size) {
                throw new \Exception("Failed to get file metadata (mimeType/size)");
            }
// 8. Get file URL
            $fileUrl = Storage::disk($finalDisk)->url($fileName);
            if (!$fileUrl) {
                throw new \Exception("Failed to generate file URL");
            }
            // 7. Create media record
            $media = new Media([
                'model_type' => $modelType,
                'model_id' => $modelId,
                'collection' => $collection,
                'file_name' => $originalName,
                'mime_type' => $mimeType,
                'size' => $size,
                'disk' => $finalDisk,
                'path' => $fileUrl,
            ]);

            if (!$model->media()->save($media)) {
                throw new \Exception("Failed to save media record to database");
            }



            // 9. Delete temp file (only if everything else succeeded)
            Storage::disk($tempDisk)->delete($tempPath);

            return $fileUrl;

        } catch (\Exception $e) {
            // Log the error with all relevant details
            \Log::error('Failed to move file to permanent storage', [
                'error' => $e->getMessage(),
                'tempPath' => $tempPath,
                'modelType' => $modelType,
                'modelId' => $modelId,
                'originalName' => $originalName,
                'collection' => $collection,
            ]);

            return null;
        }
    }
    /**
     * حذف عکس از لیارا و دیتابیس
     */
    public function deleteByPath(string $path): bool
    {
        $media = Media::where('path', $path)->first();

        if (!$media) {
            \Log::warning("رکوردی با این مسیر پیدا نشد: " . $path);
            return false;
        }

        try {
            // حذف فایل از دیسک لیارا
            Storage::disk($media->disk)->delete($media->path);

            // حذف رکورد از دیتابیس
            return $media->delete();
        } catch (\Exception $e) {
            \Log::error('خطا هنگام حذف فایل: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * دریافت تصاویر یک مدل خاص (با کالکشن اختیاری)
     */
    public function getMedia(string $modelType, int $modelId)
    {
        if (!str_contains($modelType, '\\')) {
            $modelType = 'App\\Models\\' . ucfirst($modelType);
        }
        $query = Media::query()
            ->where('model_type', $modelType)
            ->where('model_id', $modelId);



        return $query->get();
    }


}

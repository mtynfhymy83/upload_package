<?php


namespace Matin\Media\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Matin\Media\Models\Media;

trait HasMedia
{
    /**
     * ارتباط polymorphic با مدل Media
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * اضافه‌کردن فایل مستقیم به مدل (نه از طریق آپلود موقت)
     */
    public function addMedia(UploadedFile $file, string $collection = 'default'): Media
    {
        $disk = config('media.final_disk', 'public');
        $path = $file->store('media/' . $collection, $disk);

        return $this->media()->create([
            'collection' => $collection,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => $disk,
            'path' => $path,
        ]);
    }

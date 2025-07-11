<?php

namespace Matin\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Matin\Media\Services\MediaService;

class MediaController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * ذخیره فایل به صورت موقت و بازگشت مسیر و URL موقت
     */
    public function tempUpload(Request $request)
    {
        $validated = $request->validate([
            'upload' => 'required|file',
        ]);

        // بررسی اینکه آیا فایل ارسال شده است یا خیر
        if ($request->hasFile('upload')) {
            // ذخیره فایل در فضای موقت
            $path = $this->mediaService->storeTempFile($request->file('upload'));
            $temporaryUrl = url('storage/' . $path);

            // بازگشت اطلاعات فایل
            return response()->json([
                'uploaded' => 1,
                'url' => $temporaryUrl,
                'path' => $path,
            ]);
        }

        return response()->json([
            'error' => 'فایل یافت نشد.',
        ], 400);
    }

    /**
     * انتقال فایل از دیسک موقت به دیسک نهایی و ذخیره در دیتابیس
     */
    public function commitUpload(Request $request)
    {
        $validated = $request->validate([
            'path' => 'required|string',
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'original_name' => 'string',
            'collection' => 'sometimes|string',
        ]);
//        $filePath = $validated['path'];

        $url = $this->mediaService->moveFileToPermanentStorage(
            $temp_path=$validated['path'],
            $modelType=$validated['model_type'],
            $modelId=$validated['model_id'],
            $originalName=$validated['original_name'],
            $collection=$validated['collection'] ?? 'default'
        );


        if ($url) {
            return response()->json([
                'url' => $url,
                'message' => 'فایل با موفقیت آپلود شد.',
            ]);
        }

        return response()->json([
            'message' => 'انتقال فایل با شکست مواجه شد.',
        ], 500);
    }
    public function index(string $model_type, int $model_id)
    {
//        $collection = $request->query('collection');

        $mediaList = $this->mediaService->getMedia(
            $model_type,
            $model_id,

        );

        // فقط path، model_type، model_id و collection را برمی‌گرداند
        return response()->json($mediaList->map(function ($media) {
            return [
                'path' => $media->path,
                'model_type' => $media->model_type,
                'model_id' => $media->model_id,
                'collection' => $media->collection,
            ];
        }));
    }




    /**
     * حذف یک فایل از فضای نهایی و دیتابیس
     */
    public function destroyByPath(Request $request)
    {
        $validated = $request->validate([
            'path' => 'required|string',
        ]);

        $ok = $this->mediaService->deleteByPath($validated['path']);

        return $ok
            ? response()->json(['message' => 'فایل و رکورد حذف شد.'], 200)
            : response()->json(['message' => 'حذف فایل با شکست مواجه شد.'], 500);
    }

}

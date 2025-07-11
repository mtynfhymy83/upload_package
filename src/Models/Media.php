<?php


namespace Matin\Media\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'model_type',
        'model_id',
        'collection',
        'original_name',
        'mime_type',
        'size',
        'disk',
        'path',
    ];

    /**
     * ارتباط polymorphic به مدل‌های مختلف
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * دسترسی سریع به آدرس کامل فایل در دیسک نهایی
     */
    public function getUrlAttribute(): string
    {
        return \Storage::disk($this->disk)->url($this->path);
    }
}

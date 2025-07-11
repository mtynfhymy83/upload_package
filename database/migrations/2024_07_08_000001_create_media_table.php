<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // کلاس مدل (مثلاً App\Models\Product)
            $table->unsignedBigInteger('model_id'); // آیدی مدل
            $table->string('collection')->nullable(); // دسته‌بندی فایل‌ها (مثل thumbnail)
            $table->string('original_name')->nullable(); // نام اصلی فایل
            $table->string('mime_type'); // نوع فایل (image/png)
            $table->unsignedBigInteger('size'); // سایز فایل (بایت)
            $table->string('disk')->default('public'); // دیسک ذخیره‌سازی (s3, liara, local)
            $table->string('path'); // مسیر فایل در دیسک
            $table->timestamps();
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};

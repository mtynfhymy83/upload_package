# upload_package
# 🎞️ پکیج مدیریت رسانه به‌صورت Polymorphic در لاراول

پکیج `matin/laravel-media` یک پکیج قدرتمند برای مدیریت فایل‌های رسانه‌ای (تصاویر) در پروژه‌های لاراول است که به‌صورت **Polymorphic** به مدل‌ها متصل می‌شود و امکانات زیر را در اختیار توسعه‌دهنده قرار می‌دهد:

---

## ✨ امکانات کلیدی

- آپلود موقت فایل‌ها در `storage`
- انتقال فایل‌ها به فضای نهایی (مثل S3 یا Liara)
- اتصال فایل‌ها به مدل‌های مختلف به‌صورت Polymorphic
- ثبت اطلاعات فایل در دیتابیس
- حذف فایل از دیسک نهایی (مثلاً لیارا)
- دریافت تصاویر یک مدل خاص
- ساختار تمیز با استفاده از **Trait**، **Service** و **Job**
- پشتیبانی از اجرای اختیاری صف (Queue)

---

## 📦 نصب

### 1. نصب پکیج

در فایل `composer.json` پروژه خود:

```bash
composer require matin/laravel-media
```

### 2. نصب پیش‌نیاز S3 (الزامی برای آپلود به لیارا یا آمازون S3):

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### 3. انتشار فایل کانفیگ:

```bash
php artisan vendor:publish --tag=media-config
```

### 4. اجرای مایگریشن:

```bash
php artisan migrate
```

---

## ⚙️ تنظیمات کانفیگ

در فایل `config/media.php`:

```php
return [
    'temp_disk' => 'public',     // دیسک موقت برای ذخیره اولیه فایل‌ها
    'final_disk' => 'liara',     // دیسک نهایی برای ذخیره‌سازی دائمی
    'use_queue' => false         // فعال‌سازی یا غیرفعال‌سازی صف
];
```

> اگر از صف استفاده می‌کنید، دستور `php artisan queue:work` باید اجرا شود (مثلاً با Supervisor در سرور).

---

## 🚀 نحوه استفاده از API

### 1️⃣ آپلود موقت فایل

```http
POST /api/media/temp-upload
```

**پارامتر ورودی:**

| فیلد | نوع | توضیح |
|------|-----|--------|
| upload | file | فایل برای آپلود موقت |

**پاسخ:**
```json
{
  "uploaded": 1,
  "url": "http://your-site/storage/local/abc.jpg",
  "path": "local/abc.jpg"
}
```

---

### 2️⃣ انتقال به دیسک نهایی و اتصال به مدل

```http
POST /api/media/commit-upload
```

**پارامتر ورودی:**

| فیلد | نوع | توضیح |
|------|-----|--------|
| path | string | مسیر فایل موقت |
| model_type | string | نام مدل (مثلاً: product) |
| model_id | integer | شناسه مدل |
| original_name | string | نام اصلی فایل |
| collection | string | (اختیاری) نام کالکشن |

**پاسخ موفق:**
```json
{
  "url": "https://your-liara-bucket.s3.ir-thr-at1.arvanstorage.com/xyz.jpg",
  "message": "فایل با موفقیت منتقل شد."
}
```

---

### 3️⃣ دریافت فایل‌های مرتبط با یک مدل

```http
GET /api/media/model_type/model_id
```

**پاسخ:**

```json
[
  {
    "path": "media/xyz.jpg",
    "model_type": "App\Models\Product",
    "model_id": 1,
    "collection": "default"
  }
]
```

---

### 4️⃣ حذف یک فایل از فضای نهایی

```http
post /api/media/delete
```

**پارامتر:**
```json
{
  "path": "media/xyz.jpg"
}
```

**پاسخ موفق:**
```json
{
  "message": "فایل حذف شد."
}
```

## 👨‍💻 توسعه‌دهنده

- **نام:** متین فهیمی
- **لایسنس:** MIT
- **نسخه:** 1.0.0
- **گیت‌هاب:** [https://github.com/matin/laravel-media](https://github.com/matin/laravel-media)

📬 اگر سوال یا پیشنهادی دارید، خوشحال می‌شوم که در گیت‌هاب مطرح کنید یا درخواست Pull Request ارسال کنید.
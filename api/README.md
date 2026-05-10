# PHP Backend & Admin Panel

این پوشه شامل API های PHP و پنل مدیریت برای پروژه گلزار تربت است.

## پیش‌نیازها

- PHP 7.4 یا بالاتر
- MySQL 5.7 یا بالاتر
- Apache/Nginx با mod_rewrite فعال

## نصب و راه‌اندازی

### ۱. ایجاد دیتابیس

فایل `schema.sql` را در MySQL اجرا کنید:

```bash
mysql -u root -p < schema.sql
```

یا از phpMyAdmin استفاده کنید و محتوای فایل را اجرا کنید.

### ۲. تنظیمات دیتابیس

فایل `config.php` را ویرایش کنید و اطلاعات اتصال به دیتابیس را تنظیم کنید:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'golzar_torbat');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### ۳. مجوز پوشه آپلود

```bash
mkdir -p ../uploads/martyrs ../uploads/sliders
chmod 755 ../uploads/martyrs ../uploads/sliders
```

## API Endpoints

### شهدا (martyrs.php)

| متد | پارامتر | توضیحات |
|-----|---------|---------|
| GET | - | دریافت لیست شهدا با فیلتر و صفحه‌بندی |
| GET | id=X | دریافت اطلاعات یک شهید |
| POST | JSON body | افزودن شهید جدید (نیاز به احراز هویت) |
| PUT | JSON body | بروزرسانی شهید (نیاز به احراز هویت) |
| DELETE | id=X | حذف شهید (نیاز به احراز هویت) |

**پارامترهای جستجو:**
- `search` - جستجو در نام
- `nationalId` - کد ملی
- `codeIsar` - کد ایثار
- `birthYear`, `birthMonth`, `birthDay` - تاریخ تولد
- `martyrdomYear`, `martyrdomMonth`, `martyrdomDay` - تاریخ شهادت
- `birthPlace`, `burialPlace` - محل تولد/گلزار
- `gender`, `nationality`, `religion` - جنسیت/ملیت/مذهب
- `page`, `limit` - صفحه‌بندی

### احراز هویت (auth.php)

| Action | توضیحات |
|--------|---------|
| login | ورود با username و password |
| logout | خروج |
| check | بررسی وضعیت احراز هویت |

### آثار ارسالی (submissions.php)

| متد | توضیحات |
|-----|---------|
| GET | دریافت لیست آثار ارسالی |
| POST | ارسال اثر جدید |
| PUT | تایید/رد اثر (نیاز به احراز هویت) |
| DELETE | حذف اثر (نیاز به احراز هویت) |

### اسلایدر (sliders.php)

| متد | توضیحات |
|-----|---------|
| GET | دریافت لیست اسلایدرها |
| POST | افزودن اسلایدر (نیاز به احراز هویت) |
| PUT | تغییر ترتیب (نیاز به احراز هویت) |
| DELETE | حذف اسلایدر (نیاز به احراز هویت) |

### فیلترها (filters.php)

| متد | توضیحات |
|-----|---------|
| GET | دریافت مقادیر یکتا برای dropdown های فیلتر |

## پنل مدیریت

برای دسترسی به پنل مدیریت:

```
http://your-domain.com/api/admin.php
```

**اطلاعات ورود پیش‌فرض:**
- نام کاربری: `admin`
- رمز عبور: `admin123`

> **مهم:** پس از اولین ورود، رمز عبور را تغییر دهید!

### تغییر رمز عبور

رمز عبور جدید را با استفاده از PHP hash کنید:

```php
echo password_hash('new_password', PASSWORD_DEFAULT);
```

سپس در دیتابیس آپدیت کنید:

```sql
UPDATE admin_users SET password_hash = 'hashed_password' WHERE username = 'admin';
```

## امکانات پنل مدیریت

### مدیریت شهدا
- مشاهده لیست شهدا با جستجو و صفحه‌بندی
- ویرایش **تمام** مشخصات شهید (۳۰ فیلد)
- آپلود تصویر پروفایل
- حذف شهید

### بررسی آثار ارسالی
- مشاهده آثار ارسال شده توسط کاربران
- تایید یا رد آثار
- حذف آثار

### اسلایدر و گالری
- افزودن تصویر جدید به اسلایدر
- حذف تصاویر اسلایدر
- مرتب‌سازی اسلایدرها

## مثال استفاده از API

### جستجوی شهدا

```javascript
fetch('/api/martyrs.php?search=علی&birthYear=1340&page=1&limit=24')
  .then(res => res.json())
  .then(data => {
    console.log(data.data); // لیست شهدا
    console.log(data.pagination); // اطلاعات صفحه‌بندی
  });
```

### بروزرسانی شهید

```javascript
fetch('/api/martyrs.php', {
  method: 'PUT',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    id: '123',
    firstName: 'علی',
    lastName: 'احمدی',
    // ... سایر فیلدها
  })
});
```

## نکات امنیتی

1. **رمز عبور پیش‌فرض را تغییر دهید**
2. از HTTPS استفاده کنید
3. دسترسی به پوشه `api` را محدود کنید
4. پشتیبان‌گیری منظم از دیتابیس داشته باشید
5. فایل `config.php` را از دسترسی مستقیم محافظت کنید

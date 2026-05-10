# راهنمای راه‌اندازی بک‌اند PHP و MySQL

## پیش‌نیازها

1. **PHP 7.4+** با افزونه‌های زیر:
   - PDO
   - pdo_mysql
   - json

2. **MySQL 5.7+** یا **MariaDB 10.3+**

3. **وب‌سرور**: Apache یا Nginx

---

## مراحل راه‌اندازی

### 1. ایجاد دیتابیس

ابتدا وارد MySQL شوید و فایل schema.sql را اجرا کنید:

```bash
mysql -u root -p < api/schema.sql
```

یا از phpMyAdmin استفاده کنید و محتوای فایل `api/schema.sql` را import کنید.

---

### 2. تنظیم فایل config.php

فایل `api/config.php` را باز کنید و تنظیمات دیتابیس را ویرایش کنید:

```php
define('DB_HOST', 'localhost');      // آدرس سرور MySQL
define('DB_NAME', 'golzar_torbat');  // نام دیتابیس
define('DB_USER', 'root');           // نام کاربری MySQL
define('DB_PASS', 'your_password');  // رمز عبور MySQL
```

---

### 3. Import کردن داده‌های موجود

برای import کردن داده‌ها از فایل db.txt به MySQL:

```bash
cd api
php import-data.php ../src/app/imports/db.txt
```

---

### 4. تنظیم وب‌سرور

#### برای Apache:

یک فایل `.htaccess` در پوشه `api` ایجاد کنید:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ $1.php [L,QSA]

# CORS Headers
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"
```

#### برای Nginx:

```nginx
location /api/ {
    try_files $uri $uri/ $uri.php?$query_string;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## API Endpoints

### شهدا (Martyrs)

| Method | Endpoint | توضیحات |
|--------|----------|---------|
| GET | `/api/martyrs.php` | دریافت لیست شهدا با فیلتر و صفحه‌بندی |
| GET | `/api/martyrs.php?id=1` | دریافت یک شهید با ID |
| POST | `/api/martyrs.php` | افزودن شهید جدید (نیاز به احراز هویت) |
| PUT | `/api/martyrs.php` | ویرایش شهید (نیاز به احراز هویت) |
| DELETE | `/api/martyrs.php?id=1` | حذف شهید (نیاز به احراز هویت) |

#### پارامترهای جستجو:
- `search` - جستجو در نام
- `nationalId` - کد ملی
- `codeIsar` - کد ایثار
- `veteranStatus` - وضعیت ایثارگری
- `birthYear`, `birthMonth`, `birthDay` - تاریخ تولد
- `martyrdomYear`, `martyrdomMonth`, `martyrdomDay` - تاریخ شهادت
- `birthPlace`, `burialPlace`, `fileLocation` - مکان‌ها
- `gender`, `nationality`, `religion` - مشخصات
- `page`, `limit` - صفحه‌بندی

### احراز هویت (Auth)

| Method | Endpoint | Body | توضیحات |
|--------|----------|------|---------|
| POST | `/api/auth.php` | `{"action": "login", "username": "...", "password": "..."}` | ورود |
| POST | `/api/auth.php` | `{"action": "logout"}` | خروج |
| POST | `/api/auth.php` | `{"action": "check"}` | بررسی وضعیت ورود |

### اسلایدر (Sliders)

| Method | Endpoint | توضیحات |
|--------|----------|---------|
| GET | `/api/sliders.php` | دریافت لیست اسلایدرها |
| POST | `/api/sliders.php` | افزودن اسلایدر (نیاز به احراز هویت) |
| PUT | `/api/sliders.php` | مرتب‌سازی اسلایدرها (نیاز به احراز هویت) |
| DELETE | `/api/sliders.php?id=1` | حذف اسلایدر (نیاز به احراز هویت) |

### آثار ارسالی (Submissions)

| Method | Endpoint | توضیحات |
|--------|----------|---------|
| GET | `/api/submissions.php` | دریافت لیست آثار |
| POST | `/api/submissions.php` | ارسال اثر جدید |
| PUT | `/api/submissions.php` | تایید/رد اثر (نیاز به احراز هویت) |
| DELETE | `/api/submissions.php?id=1` | حذف اثر (نیاز به احراز هویت) |

### مقادیر یکتا (Unique Values)

| Method | Endpoint | توضیحات |
|--------|----------|---------|
| GET | `/api/unique-values.php` | دریافت همه مقادیر یکتا |
| GET | `/api/unique-values.php?field=birthPlace` | دریافت مقادیر یکتای یک فیلد |

---

## اطلاعات ورود پیش‌فرض

- **نام کاربری:** `admin`
- **رمز عبور:** `admin123`

**مهم:** لطفاً بعد از راه‌اندازی، رمز عبور را تغییر دهید!

برای تغییر رمز عبور، از این دستور SQL استفاده کنید:

```sql
UPDATE admin_users 
SET password_hash = '$2y$10$YOUR_NEW_HASH_HERE' 
WHERE username = 'admin';
```

برای ایجاد hash جدید:

```php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
```

---

## نکات امنیتی

1. رمز عبور پیش‌فرض را تغییر دهید
2. فایل `config.php` را از دسترس عمومی محافظت کنید
3. از HTTPS استفاده کنید
4. CORS را در production محدود کنید

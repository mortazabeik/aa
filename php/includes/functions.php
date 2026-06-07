<?php
require_once __DIR__ . '/config.php';

/**
 * توابع کمکی مشترک
 */

// خروجی امن HTML
function e($v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

// اجرای کوئری و گرفتن یک ردیف
function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

// اجرای کوئری و گرفتن همه ردیف‌ها
function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// ساخت آدرس با در نظر گرفتن BASE_URL
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

// آدرس asset
function asset(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

// نگاشت ستون‌های دیتابیس به کلیدهای فرم/نمایش (camelCase)
function martyr_field_map(): array
{
    return [
        'code_isar'          => 'کد ایثار',
        'national_id'        => 'کد ملی',
        'veteran_status'     => 'ایثارگری',
        'first_name'         => 'نام',
        'last_name'          => 'نام خانوادگی',
        'father_name'        => 'نام پدر',
        'gender'             => 'جنسیت',
        'nationality'        => 'ملیت',
        'religion'           => 'مذهب',
        'birth_date'         => 'تاریخ تولد',
        'martyrdom_date'     => 'تاریخ شهادت',
        'age'                => 'سن در زمان شهادت',
        'birth_place'        => 'محل تولد',
        'file_location'      => 'محل پرونده',
        'burial_place'       => 'محل گلزار',
        'education'          => 'تحصیلات',
        'occupation'         => 'شغل قبل از شهادت',
        'marital_status'     => 'وضعیت تاهل',
        'serving_unit'       => 'یگان خدمت',
        'membership_type'    => 'نوع عضویت',
        'event_stream'       => 'جریان رخداد',
        'operation_zone'     => 'منطقه عملیاتی',
        'enemy'              => 'دشمن',
        'military_operation' => 'عملیات نظامی',
    ];
}

// تجزیه تاریخ شمسی به سال/ماه/روز
function split_date(?string $date): array
{
    if ($date && strpos($date, '/') !== false) {
        $p = explode('/', $date);
        if (count($p) === 3) {
            return [trim($p[0]), trim($p[1]), trim($p[2])];
        }
    }
    return ['', '', ''];
}

// تبدیل ارقام فارسی/عربی به انگلیسی برای جستجو
function to_en_digits(string $s): string
{
    $fa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    $ar = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
    $en = ['0','1','2','3','4','5','6','7','8','9'];
    return str_replace($ar, $en, str_replace($fa, $en, $s));
}

// شروع نشست
function ensure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// آیا کاربر مدیر وارد شده است؟
function is_admin(): bool
{
    ensure_session();
    return !empty($_SESSION['admin_id']);
}

// محافظت از صفحات مدیریت
function require_admin(): void
{
    if (!is_admin()) {
        header('Location: ' . url('admin/login.php'));
        exit;
    }
}

// مقادیر یکتای یک ستون (برای فیلترهای کمبوباکس)
function unique_values(string $column): array
{
    $allowed = array_keys(martyr_field_map());
    $allowed[] = 'serving_unit';
    if (!in_array($column, $allowed, true)) {
        return [];
    }
    $sql = "SELECT DISTINCT `$column` AS v FROM martyrs
            WHERE `$column` IS NOT NULL AND `$column` <> '' ORDER BY v ASC";
    return array_column(db()->query($sql)->fetchAll(), 'v');
}

// نام کامل
function full_name(array $m): string
{
    return trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''));
}

// نمایش عکس شهید یا نماد گل در صورت نبود
function martyr_photo(array $m, string $alt = ''): string
{
    $img = $m['profile_image'] ?? '';
    if ($img !== '') {
        return '<img src="' . url($img) . '" alt="' . e($alt) . '" loading="lazy">';
    }
    ob_start();
    include __DIR__ . '/../partials/rose.php';
    return '<div class="rose-fallback">' . ob_get_clean() . '</div>';
}

// رندر کارت شهید برای لیست/جستجو
function render_martyr_card(array $m): string
{
    $name = full_name($m);
    $html  = '<div class="glass glass-hover martyr-card">';
    $html .= '<div class="photo">' . martyr_photo($m, $name);
    $html .= '<div class="overlay"><h3>' . e($name) . '</h3>';
    $html .= '<span class="place">' . e($m['birth_place'] !== '' ? $m['birth_place'] : 'نامشخص') . '</span></div></div>';
    $html .= '<div class="body"><div class="dates">';
    $html .= '<div class="row bordered"><span class="label">ولادت:</span><span class="val">' . e($m['birth_date'] !== '' ? $m['birth_date'] : '-') . '</span></div>';
    $html .= '<div class="row"><span class="label">شهادت:</span><span class="val rose">' . e($m['martyrdom_date'] !== '' ? $m['martyrdom_date'] : '-') . '</span></div>';
    $html .= '</div><a class="btn btn-ghost btn-block" href="' . url('martyr.php?id=' . (int)$m['id']) . '">مشاهده پروفایل</a>';
    $html .= '</div></div>';
    return $html;
}

<?php
/**
 * نصب و راه‌اندازی اولیه
 * این فایل را یک‌بار در مرورگر باز کنید: http://your-site/install.php
 * - جداول دیتابیس را می‌سازد
 * - کاربر مدیر پیش‌فرض (admin / admin123) را ایجاد می‌کند
 * - داده‌های اولیه شهدا را از data/db.txt وارد می‌کند
 * - اسلایدرهای پیش‌فرض را اضافه می‌کند
 *
 * پس از اجرای موفق، این فایل را حذف کنید.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: text/html; charset=utf-8');
echo '<html dir="rtl"><head><meta charset="utf-8"><title>نصب گلزار تربت</title>';
echo '<style>body{font-family:Tahoma,sans-serif;background:#0f172a;color:#e2e8f0;padding:40px;line-height:2}
.ok{color:#34d399}.err{color:#f87171}.box{max-width:700px;margin:auto;background:#1e293b;padding:30px;border-radius:16px}
a{color:#60a5fa}</style></head><body><div class="box"><h1>نصب سامانه گلزار تربت</h1>';

function step(string $msg, bool $ok = true): void
{
    echo '<p class="' . ($ok ? 'ok' : 'err') . '">' . ($ok ? '✓ ' : '✗ ') . e($msg) . '</p>';
}

try {
    // ساخت دیتابیس در صورت نبود
    $pdoRoot = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    step('دیتابیس آماده شد: ' . DB_NAME);

    $pdo = db();

    // اجرای schema
    $schema = file_get_contents(__DIR__ . '/sql/schema.sql');
    // حذف خطوط CREATE DATABASE / USE چون قبلا انجام شده
    $schema = preg_replace('/CREATE DATABASE.*?;/is', '', $schema);
    $schema = preg_replace('/USE\s+\w+\s*;/i', '', $schema);
    foreach (array_filter(array_map('trim', explode(';', $schema))) as $stmt) {
        if ($stmt !== '') {
            $pdo->exec($stmt);
        }
    }
    step('جداول ساخته شدند');

    // کاربر مدیر پیش‌فرض
    $exists = $pdo->query("SELECT COUNT(*) FROM admins WHERE username='admin'")->fetchColumn();
    if (!$exists) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES ('admin', ?)");
        $stmt->execute([$hash]);
        step('کاربر مدیر ایجاد شد (نام کاربری: admin ، رمز: admin123)');
    } else {
        step('کاربر مدیر از قبل وجود دارد');
    }

    // وارد کردن داده شهدا
    $count = (int)$pdo->query("SELECT COUNT(*) FROM martyrs")->fetchColumn();
    if ($count === 0) {
        $imported = import_martyrs($pdo, __DIR__ . '/data/db.txt');
        step("$imported شهید وارد دیتابیس شد");
    } else {
        step("جدول شهدا از قبل دارای $count رکورد است (واردسازی رد شد)");
    }

    // اسلایدرهای پیش‌فرض
    $sc = (int)$pdo->query("SELECT COUNT(*) FROM sliders")->fetchColumn();
    if ($sc === 0) {
        $stmt = $pdo->prepare("INSERT INTO sliders (url, sort_order) VALUES (?, ?)");
        foreach (['assets/slider/1.jpg', 'assets/slider/2.jpg', 'assets/slider/3.jpg'] as $i => $u) {
            $stmt->execute([$u, $i]);
        }
        step('اسلایدرهای پیش‌فرض اضافه شدند');
    }

    echo '<h2 class="ok">نصب با موفقیت انجام شد</h2>';
    echo '<p><strong>مهم:</strong> برای امنیت، فایل install.php را حذف کنید.</p>';
    echo '<p><a href="' . url('index.php') . '">ورود به سایت</a> | <a href="' . url('admin/login.php') . '">پنل مدیریت</a></p>';
} catch (Throwable $ex) {
    step('خطا: ' . $ex->getMessage(), false);
}

echo '</div></body></html>';


/**
 * واردسازی داده‌ها از فایل CSV (db.txt)
 */
function import_martyrs(PDO $pdo, string $path): int
{
    $fh = fopen($path, 'r');
    if (!$fh) {
        throw new RuntimeException('فایل داده پیدا نشد: ' . $path);
    }

    // رد کردن سطر سرستون
    fgetcsv($fh);

    $cols = [
        'code_isar','national_id','veteran_status','first_name','last_name','father_name',
        'gender','nationality','religion','birth_date','martyrdom_date','age',
        'birth_place','file_location','burial_place','education','occupation','marital_status',
        'serving_unit','membership_type','event_stream','operation_zone','enemy','military_operation'
    ];

    $sql = "INSERT INTO martyrs
        (code_isar,national_id,veteran_status,first_name,last_name,father_name,
         gender,nationality,religion,birth_date,birth_year,birth_month,birth_day,
         martyrdom_date,martyrdom_year,martyrdom_month,martyrdom_day,age,
         birth_place,file_location,burial_place,education,occupation,marital_status,
         serving_unit,membership_type,event_stream,operation_zone,enemy,military_operation)
        VALUES
        (:code_isar,:national_id,:veteran_status,:first_name,:last_name,:father_name,
         :gender,:nationality,:religion,:birth_date,:birth_year,:birth_month,:birth_day,
         :martyrdom_date,:martyrdom_year,:martyrdom_month,:martyrdom_day,:age,
         :birth_place,:file_location,:burial_place,:education,:occupation,:marital_status,
         :serving_unit,:membership_type,:event_stream,:operation_zone,:enemy,:military_operation)";
    $stmt = $pdo->prepare($sql);

    $pdo->beginTransaction();
    $n = 0;
    while (($row = fgetcsv($fh)) !== false) {
        if (count(array_filter($row, fn($x) => trim((string)$x) !== '')) === 0) {
            continue;
        }
        $row = array_pad($row, 24, '');
        $data = [];
        foreach ($cols as $i => $c) {
            $data[$c] = trim((string)($row[$i] ?? ''));
        }

        [$by, $bm, $bd] = split_date($data['birth_date']);
        [$my, $mm, $md] = split_date($data['martyrdom_date']);
        $ageVal = ($data['age'] !== '' && is_numeric($data['age'])) ? (int)floatval($data['age']) : null;

        $stmt->execute([
            'code_isar' => $data['code_isar'],
            'national_id' => $data['national_id'],
            'veteran_status' => $data['veteran_status'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'father_name' => $data['father_name'],
            'gender' => $data['gender'],
            'nationality' => $data['nationality'],
            'religion' => $data['religion'],
            'birth_date' => $data['birth_date'],
            'birth_year' => $by, 'birth_month' => $bm, 'birth_day' => $bd,
            'martyrdom_date' => $data['martyrdom_date'],
            'martyrdom_year' => $my, 'martyrdom_month' => $mm, 'martyrdom_day' => $md,
            'age' => $ageVal,
            'birth_place' => $data['birth_place'],
            'file_location' => $data['file_location'],
            'burial_place' => $data['burial_place'],
            'education' => $data['education'],
            'occupation' => $data['occupation'],
            'marital_status' => $data['marital_status'],
            'serving_unit' => $data['serving_unit'],
            'membership_type' => $data['membership_type'],
            'event_stream' => $data['event_stream'],
            'operation_zone' => $data['operation_zone'],
            'enemy' => $data['enemy'],
            'military_operation' => $data['military_operation'],
        ]);
        $n++;
    }
    $pdo->commit();
    fclose($fh);
    return $n;
}

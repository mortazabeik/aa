<?php
/**
 * تنظیمات اتصال به دیتابیس MySQL
 * مقادیر زیر را مطابق هاست خود تغییر دهید.
 */

// --- اطلاعات اتصال دیتابیس ---
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'golzar_torbat');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// --- مسیر پایه سایت (در صورت قرارگیری در زیرپوشه تغییر دهید) ---
// مثال: اگر سایت در http://example.com/golzar/ است، مقدار '/golzar' بگذارید
define('BASE_URL', getenv('BASE_URL') ?: '');

// --- تنظیمات آپلود ---
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024); // 50MB

date_default_timezone_set('Asia/Tehran');

/**
 * اتصال PDO به دیتابیس (singleton)
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            http_response_code(500);
            die('خطا در اتصال به دیتابیس: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}

-- ============================================================
--  گلزار تربت - ساختار دیتابیس MySQL
--  Charset: utf8mb4 برای پشتیبانی کامل از فارسی
-- ============================================================

CREATE DATABASE IF NOT EXISTS golzar_torbat
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE golzar_torbat;

-- ---------- جدول شهدا ----------
CREATE TABLE IF NOT EXISTS martyrs (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  code_isar       VARCHAR(50)  DEFAULT '',
  national_id     VARCHAR(50)  DEFAULT '',
  veteran_status  VARCHAR(100) DEFAULT '',
  first_name      VARCHAR(100) DEFAULT '',
  last_name       VARCHAR(100) DEFAULT '',
  father_name     VARCHAR(100) DEFAULT '',
  gender          VARCHAR(30)  DEFAULT '',
  nationality     VARCHAR(50)  DEFAULT '',
  religion        VARCHAR(80)  DEFAULT '',
  birth_date      VARCHAR(20)  DEFAULT '',
  birth_year      VARCHAR(8)   DEFAULT '',
  birth_month     VARCHAR(4)   DEFAULT '',
  birth_day       VARCHAR(4)   DEFAULT '',
  martyrdom_date  VARCHAR(20)  DEFAULT '',
  martyrdom_year  VARCHAR(8)   DEFAULT '',
  martyrdom_month VARCHAR(4)   DEFAULT '',
  martyrdom_day   VARCHAR(4)   DEFAULT '',
  age             INT          NULL,
  birth_place     VARCHAR(255) DEFAULT '',
  file_location   VARCHAR(255) DEFAULT '',
  burial_place    VARCHAR(255) DEFAULT '',
  education       VARCHAR(120) DEFAULT '',
  occupation      VARCHAR(120) DEFAULT '',
  marital_status  VARCHAR(50)  DEFAULT '',
  serving_unit    VARCHAR(150) DEFAULT '',
  membership_type VARCHAR(120) DEFAULT '',
  event_stream    VARCHAR(120) DEFAULT '',
  operation_zone  VARCHAR(150) DEFAULT '',
  enemy           VARCHAR(120) DEFAULT '',
  military_operation VARCHAR(150) DEFAULT '',
  profile_image   VARCHAR(255) DEFAULT '',
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_name (first_name, last_name),
  INDEX idx_national (national_id),
  INDEX idx_isar (code_isar),
  INDEX idx_birth_place (birth_place),
  INDEX idx_burial (burial_place),
  INDEX idx_martyrdom (martyrdom_year, martyrdom_month, martyrdom_day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- جدول اسلایدر ----------
CREATE TABLE IF NOT EXISTS sliders (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  url         VARCHAR(255) NOT NULL,
  sort_order  INT DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- جدول آثار ارسالی ----------
CREATE TABLE IF NOT EXISTS submissions (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(150) DEFAULT '',
  contact       VARCHAR(150) DEFAULT '',
  martyr_name   VARCHAR(150) DEFAULT '',
  martyr_id     INT NULL,
  description   TEXT,
  file_url      VARCHAR(255) DEFAULT '',
  type          VARCHAR(30)  DEFAULT 'image',
  status        ENUM('pending','approved','rejected') DEFAULT 'pending',
  submitted_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- جدول کاربران مدیر ----------
CREATE TABLE IF NOT EXISTS admins (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  username  VARCHAR(60) UNIQUE NOT NULL,
  password  VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- کاربر مدیر پیش‌فرض (admin / admin123) توسط اسکریپت install.php ساخته می‌شود
-- تا هش رمز عبور با password_hash و به‌صورت امن تولید شود.

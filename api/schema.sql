-- Database Schema for Golzar Torbat
-- Run this SQL to create the database and tables

CREATE DATABASE IF NOT EXISTS golzar_torbat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE golzar_torbat;

-- Martyrs Table
CREATE TABLE IF NOT EXISTS martyrs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_isar VARCHAR(50) DEFAULT '',
    national_id VARCHAR(20) DEFAULT '',
    veteran_status VARCHAR(100) DEFAULT '',
    first_name VARCHAR(100) NOT NULL DEFAULT '',
    last_name VARCHAR(100) NOT NULL DEFAULT '',
    father_name VARCHAR(100) DEFAULT '',
    gender VARCHAR(20) DEFAULT '',
    nationality VARCHAR(50) DEFAULT '',
    religion VARCHAR(50) DEFAULT '',
    birth_date VARCHAR(20) DEFAULT '',
    birth_year VARCHAR(10) DEFAULT '',
    birth_month VARCHAR(5) DEFAULT '',
    birth_day VARCHAR(5) DEFAULT '',
    martyrdom_date VARCHAR(20) DEFAULT '',
    martyrdom_year VARCHAR(10) DEFAULT '',
    martyrdom_month VARCHAR(5) DEFAULT '',
    martyrdom_day VARCHAR(5) DEFAULT '',
    age INT DEFAULT NULL,
    birth_place VARCHAR(200) DEFAULT '',
    file_location VARCHAR(200) DEFAULT '',
    burial_place VARCHAR(200) DEFAULT '',
    education VARCHAR(100) DEFAULT '',
    occupation VARCHAR(100) DEFAULT '',
    marital_status VARCHAR(50) DEFAULT '',
    serving_unit VARCHAR(200) DEFAULT '',
    membership_type VARCHAR(100) DEFAULT '',
    event_stream VARCHAR(200) DEFAULT '',
    operation_zone VARCHAR(200) DEFAULT '',
    enemy VARCHAR(100) DEFAULT '',
    military_operation VARCHAR(200) DEFAULT '',
    profile_image TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_national_id (national_id),
    INDEX idx_code_isar (code_isar),
    INDEX idx_name (first_name, last_name),
    INDEX idx_birth_year (birth_year),
    INDEX idx_martyrdom_year (martyrdom_year),
    FULLTEXT idx_fulltext (first_name, last_name, birth_place, burial_place)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Submissions Table
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    martyr_id INT NOT NULL,
    file_url TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_martyr_id (martyr_id),
    INDEX idx_status (status),
    FOREIGN KEY (martyr_id) REFERENCES martyrs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sliders Table
CREATE TABLE IF NOT EXISTS sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password_hash) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username = username;

-- Insert default sliders
INSERT INTO sliders (url, sort_order) VALUES 
('slider/1.jpg', 0),
('slider/2.jpg', 1),
('slider/3.jpg', 2)
ON DUPLICATE KEY UPDATE url = url;

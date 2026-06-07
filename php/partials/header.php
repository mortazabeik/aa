<?php
/**
 * هدر مشترک سایت
 * متغیر $page برای مشخص‌کردن صفحه فعال و $title برای عنوان استفاده می‌شود.
 */
require_once __DIR__ . '/../includes/functions.php';
$page = $page ?? '';
$title = $title ?? 'گلزار تربت';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#0f172a">
  <meta name="description" content="سامانه گلزار تربت - زنده نگه داشتن یاد و خاطره شهیدان گرانقدر تربت حیدریه و شهرستان‌های اطراف">
  <title><?= e($title) ?></title>
  <link rel="icon" href="<?= asset('logo.png') ?>">
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
  <script>
    window.BASE_URL = '<?= BASE_URL ?>';
    window.API_MARTYR = '<?= url('admin/martyr_get.php') ?>';
    window.ROSE_IMG = '<?= asset('rose.svg') ?>';
  </script>
  <script src="<?= asset('js/app.js') ?>"></script>
</head>
<body>
  <div class="bg-decoration">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
  </div>

  <header class="site-header">
    <div class="navbar glass">
      <a href="<?= url('index.php') ?>" class="flex items-center" style="padding:0.375rem 0.75rem;">
        <img src="<?= asset('logo.png') ?>" class="logo" alt="لوگوی گلزار تربت">
      </a>

      <nav class="nav-links">
        <a href="<?= url('index.php') ?>" class="nav-link <?= $page === 'home' ? 'active' : '' ?>">
          <span data-icon="home" data-size="18"></span> صفحه اصلی
        </a>
        <a href="<?= url('search.php') ?>" class="nav-link <?= $page === 'search' ? 'active' : '' ?>">
          <span data-icon="search" data-size="18"></span> جستجو
        </a>
        <a href="<?= url('submit.php') ?>" class="nav-link <?= $page === 'submit' ? 'active' : '' ?>">
          <span data-icon="plus" data-size="18"></span> ارسال آثار
        </a>
      </nav>

      <div class="flex items-center gap-2">
        <div class="theme-toggle theme-desktop">
          <button data-theme-btn="light" title="روشن"><span data-icon="sun" data-size="18"></span></button>
          <button data-theme-btn="system" title="سیستم"><span data-icon="monitor" data-size="18"></span></button>
          <button data-theme-btn="dark" title="تاریک"><span data-icon="moon" data-size="18"></span></button>
        </div>
        <button class="theme-cycle" data-theme-cycle title="تغییر تم">
          <span data-icon="moon" data-size="18"></span>
        </button>
      </div>
    </div>
  </header>

  <main class="main-content">
    <div class="container">

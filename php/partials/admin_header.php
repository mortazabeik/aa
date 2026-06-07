<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$admin_section = $admin_section ?? '';
$page_title = $page_title ?? 'پنل مدیریت';
include __DIR__ . '/../partials/header.php';
?>
<main class="container admin-wrap">
  <aside class="glass admin-sidebar">
    <div class="admin-user">
      <span data-icon="user" data-size="20"></span>
      <span><?= e($_SESSION['admin_user'] ?? 'مدیر') ?></span>
    </div>
    <nav class="admin-nav">
      <a href="<?= url('admin/index.php') ?>" class="<?= $admin_section === 'martyrs' ? 'active' : '' ?>">
        <span data-icon="users" data-size="18"></span><span>شهدا</span>
      </a>
      <a href="<?= url('admin/submissions.php') ?>" class="<?= $admin_section === 'submissions' ? 'active' : '' ?>">
        <span data-icon="inbox" data-size="18"></span><span>آثار ارسالی</span>
      </a>
      <a href="<?= url('admin/sliders.php') ?>" class="<?= $admin_section === 'sliders' ? 'active' : '' ?>">
        <span data-icon="image" data-size="18"></span><span>اسلایدر</span>
      </a>
      <a href="<?= url('admin/logout.php') ?>" class="logout">
        <span data-icon="logout" data-size="18"></span><span>خروج</span>
      </a>
    </nav>
  </aside>
  <section class="admin-content">

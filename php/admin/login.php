<?php
require_once __DIR__ . '/../includes/functions.php';
ensure_session();

if (is_admin()) {
    header('Location: ' . url('admin/index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_user'] = $admin['username'];
        header('Location: ' . url('admin/index.php'));
        exit;
    }
    $error = 'نام کاربری یا رمز عبور اشتباه است.';
}

$page_title = 'ورود مدیریت';
include __DIR__ . '/../partials/header.php';
?>
<main class="container" style="max-width:440px">
  <div class="glass" style="padding:2rem;margin-top:3rem">
    <h1 class="page-title" style="text-align:center;margin-bottom:0.5rem">ورود به پنل مدیریت</h1>
    <p class="page-subtitle" style="text-align:center;margin-bottom:1.5rem">برای ادامه وارد شوید</p>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" class="form">
      <div class="field">
        <label>نام کاربری</label>
        <input type="text" name="username" required autofocus>
      </div>
      <div class="field">
        <label>رمز عبور</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">ورود</button>
    </form>
  </div>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>

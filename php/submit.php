<?php
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName    = trim($_POST['full_name'] ?? '');
    $contact     = trim($_POST['contact'] ?? '');
    $martyrName  = trim($_POST['martyr_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($fullName === '')   $errors[] = 'نام و نام خانوادگی الزامی است';
    if ($contact === '')    $errors[] = 'ایمیل یا شماره تماس الزامی است';
    if ($martyrName === '') $errors[] = 'نام شهید الزامی است';

    // آپلود فایل
    $fileUrl = '';
    $type = 'image';
    if (!empty($_FILES['file']['name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['file']['tmp_name'];
        $size = $_FILES['file']['size'];
        if ($size > MAX_UPLOAD_SIZE) {
            $errors[] = 'حجم فایل بیش از حد مجاز است (حداکثر ۵۰ مگابایت)';
        } else {
            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image',
                        'webp' => 'image', 'mp4' => 'video', 'mov' => 'video', 'pdf' => 'document'];
            if (!isset($allowed[$ext])) {
                $errors[] = 'فرمت فایل مجاز نیست';
            } else {
                $type = $allowed[$ext];
                $dir = UPLOAD_DIR . '/submissions';
                if (!is_dir($dir)) @mkdir($dir, 0775, true);
                $newName = 'sub_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($tmp, $dir . '/' . $newName)) {
                    $fileUrl = 'uploads/submissions/' . $newName;
                } else {
                    $errors[] = 'خطا در آپلود فایل';
                }
            }
        }
    }

    if (empty($errors)) {
        // یافتن شهید مرتبط بر اساس نام (اختیاری)
        $martyrId = null;
        $find = db()->prepare("SELECT id FROM martyrs WHERE CONCAT(first_name,' ',last_name) LIKE ? LIMIT 1");
        $find->execute(['%' . $martyrName . '%']);
        $found = $find->fetchColumn();
        if ($found) $martyrId = (int)$found;

        $stmt = db()->prepare(
            "INSERT INTO submissions (full_name, contact, martyr_name, martyr_id, description, file_url, type, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"
        );
        $stmt->execute([$fullName, $contact, $martyrName, $martyrId, $description, $fileUrl, $type]);
        $success = true;
    }
}

$page = 'submit';
$title = 'گلزار تربت - ارسال آثار';
include __DIR__ . '/partials/header.php';
?>

<div style="max-width:48rem;margin:0 auto;">
  <div class="text-center mb-6">
    <h1 class="page-title" style="margin-bottom:1rem;">ارسال آثار و مستندات</h1>
    <p class="text-muted">
      تصاویر، ویدیوها و خاطرات خود از شهدای گرانقدر را با ما به اشتراک بگذارید تا در تکمیل این گنجینه سهیم باشید.
    </p>
  </div>

  <div class="glass p-8">
    <?php if ($success): ?>
      <div class="text-center" style="padding:3rem 0;">
        <div style="width:5rem;height:5rem;background:rgba(5,150,105,0.12);border-radius:9999px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:var(--accent);">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <h3 style="font-size:1.5rem;font-weight:700;margin-bottom:0.5rem;">با موفقیت ارسال شد</h3>
        <p class="text-muted">از همکاری شما سپاسگزاریم. مستندات شما پس از بررسی توسط تیم ما، در سایت منتشر خواهد شد.</p>
        <a href="<?= url('submit.php') ?>" class="btn btn-ghost mt-6">ارسال اثر جدید</a>
      </div>
    <?php else: ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= url('submit.php') ?>" enctype="multipart/form-data" class="flex flex-col gap-4">
        <div class="modal-grid">
          <div>
            <label class="field-label">نام و نام خانوادگی شما</label>
            <input type="text" name="full_name" value="<?= e($_POST['full_name'] ?? '') ?>" class="input input-lg" placeholder="علی محمدی">
          </div>
          <div>
            <label class="field-label">ایمیل یا شماره تماس</label>
            <input type="text" name="contact" value="<?= e($_POST['contact'] ?? '') ?>" class="input input-lg" placeholder="جهت پیگیری‌های بعدی">
          </div>
        </div>

        <div>
          <label class="field-label">نام شهید (مرتبط با مستندات)</label>
          <input type="text" name="martyr_name" value="<?= e($_POST['martyr_name'] ?? '') ?>" class="input input-lg" placeholder="مثال: شهید همت">
        </div>

        <div>
          <label class="field-label">آپلود فایل (تصویر، ویدیو یا سند)</label>
          <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;border:2px dashed var(--input-border);border-radius:1rem;background:var(--input-bg);cursor:pointer;text-align:center;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text-muted);margin-bottom:0.5rem;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span class="text-muted" style="font-size:0.875rem;">انتخاب فایل یا کشیدن و رها کردن</span>
            <span class="text-muted" style="font-size:0.75rem;margin-top:0.5rem;">PNG, JPG, MP4, PDF تا ۵۰ مگابایت</span>
            <input type="file" name="file" accept="image/*,video/*,.pdf" style="display:none;"
                   onchange="this.parentNode.querySelector('span').textContent = this.files[0] ? this.files[0].name : 'انتخاب فایل';">
          </label>
        </div>

        <div>
          <label class="field-label">توضیحات و خاطره</label>
          <textarea name="description" rows="4" class="input" placeholder="اگر توضیحی درباره فایل ارسالی یا خاطره‌ای دارید بنویسید..."><?= e($_POST['description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block input-lg">ارسال مستندات</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

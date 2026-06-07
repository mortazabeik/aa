<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// حذف شهید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM martyrs WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: ' . url('admin/index.php?deleted=1' . (isset($_POST['q']) ? '&q=' . urlencode($_POST['q']) : '') . '&page=' . (int)($_POST['page'] ?? 1)));
    exit;
}

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 12;
$offset = ($page - 1) * $per;

$where = '';
$params = [];
if ($q !== '') {
    $where = "WHERE first_name LIKE ? OR last_name LIKE ? OR national_id LIKE ? OR code_isar LIKE ?";
    $like = '%' . $q . '%';
    $params = [$like, $like, $like, $like];
}

$total = (int)db()->prepare_fetch("SELECT COUNT(*) c FROM martyrs $where", $params)['c'];
$pages = max(1, (int)ceil($total / $per));

$stmt = db()->prepare("SELECT * FROM martyrs $where ORDER BY id DESC LIMIT $per OFFSET $offset");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$page_title = 'مدیریت شهدا';
$admin_section = 'martyrs';
include __DIR__ . '/../partials/admin_header.php';
?>
<div class="admin-head">
  <h1 class="page-title">مدیریت شهدا</h1>
  <a href="<?= url('admin/edit.php') ?>" class="btn btn-primary">
    <span data-icon="plus" data-size="18"></span> افزودن شهید
  </a>
</div>

<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">اطلاعات با موفقیت ذخیره شد.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">شهید حذف شد.</div><?php endif; ?>

<form method="get" class="glass admin-search">
  <input type="text" name="q" value="<?= e($q) ?>" placeholder="جستجو بر اساس نام، کد ملی یا کد ایثار...">
  <button type="submit" class="btn btn-primary"><span data-icon="search" data-size="18"></span></button>
</form>

<div class="admin-table-wrap glass">
  <table class="admin-table">
    <thead>
      <tr><th>#</th><th>نام و نام خانوادگی</th><th>کد ملی</th><th>تاریخ شهادت</th><th>عملیات</th></tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="5" class="empty">موردی یافت نشد.</td></tr>
      <?php else: foreach ($rows as $m): ?>
        <tr>
          <td><?= (int)$m['id'] ?></td>
          <td><?= e(full_name($m)) ?></td>
          <td><?= e($m['national_id'] !== '' ? $m['national_id'] : '-') ?></td>
          <td><?= e($m['martyrdom_date'] !== '' ? $m['martyrdom_date'] : '-') ?></td>
          <td class="actions">
            <a href="<?= url('martyr.php?id=' . (int)$m['id']) ?>" target="_blank" class="icon-btn" title="مشاهده"><span data-icon="eye" data-size="16"></span></a>
            <a href="<?= url('admin/edit.php?id=' . (int)$m['id']) ?>" class="icon-btn" title="ویرایش"><span data-icon="edit" data-size="16"></span></a>
            <form method="post" onsubmit="return confirm('آیا از حذف این شهید مطمئن هستید؟')" style="display:inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
              <input type="hidden" name="q" value="<?= e($q) ?>">
              <input type="hidden" name="page" value="<?= $page ?>">
              <button type="submit" class="icon-btn danger" title="حذف"><span data-icon="trash" data-size="16"></span></button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?php if ($pages > 1): ?>
<div class="pagination">
  <?php for ($i = 1; $i <= $pages; $i++):
      $qs = http_build_query(array_filter(['q' => $q, 'page' => $i])); ?>
    <a href="?<?= $qs ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../partials/admin_footer.php'; ?>

<?php
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM martyrs WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if (!$m) {
    header('Location: ' . url('search.php'));
    exit;
}

$name = full_name($m);
$page = 'search';
$title = 'گلزار تربت - شهید ' . $name;

// تابع نمایش هر آیتم اطلاعات
function info_item(string $label, ?string $value, string $icon = 'info'): string
{
    if ($value === null || trim($value) === '') return '';
    $icons = [
        'award'   => '<polyline points="8 21 12 17 16 21"/><circle cx="12" cy="8" r="6"/>',
        'calendar'=> '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'heart'   => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>',
        'map'     => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'user'    => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'brief'   => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'info'    => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
    ];
    $svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . ($icons[$icon] ?? $icons['info']) . '</svg>';
    return '<div class="info-item"><div class="ic">' . $svg . '</div><div><p class="lbl">' . e($label) . '</p><p class="v">' . e($value) . '</p></div></div>';
}

include __DIR__ . '/partials/header.php';
?>

<a href="<?= url('search.php') ?>" class="flex items-center gap-2 text-muted mb-6" style="width:fit-content;">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 5 12 12 19"/></svg>
  بازگشت به نتایج
</a>

<div class="glass detail-card">
  <div class="detail-photo">
    <?= martyr_photo($m, $name) ?>
  </div>
  <div class="detail-body">
    <div class="mb-6">
      <h1 style="font-size:2rem;font-weight:700;margin-bottom:0.5rem;"><?= e($name) ?></h1>
      <?php if ($m['father_name'] !== ''): ?>
        <p class="text-muted" style="font-size:1.125rem;">فرزند: <?= e($m['father_name']) ?></p>
      <?php endif; ?>
    </div>

    <div class="info-grid">
      <?= info_item('کد ایثار', $m['code_isar'], 'award') ?>
      <?= info_item('ایثارگری', $m['veteran_status'], 'info') ?>
      <?= info_item('تاریخ ولادت', $m['birth_date'], 'calendar') ?>
      <?= info_item('تاریخ شهادت', $m['martyrdom_date'], 'heart') ?>
      <?= info_item('سن در زمان شهادت', $m['age'] ? $m['age'] . ' سال' : '', 'info') ?>
      <?= info_item('محل تولد', $m['birth_place'], 'map') ?>
      <?= info_item('محل گلزار', $m['burial_place'], 'map') ?>
      <?= info_item('محل پرونده', $m['file_location'], 'map') ?>
    </div>

    <div class="info-grid section-block">
      <?= info_item('جنسیت', $m['gender'], 'user') ?>
      <?= info_item('ملیت', $m['nationality'], 'info') ?>
      <?= info_item('مذهب', $m['religion'], 'info') ?>
      <?= info_item('تحصیلات', $m['education'], 'info') ?>
      <?= info_item('شغل قبل از شهادت', $m['occupation'], 'brief') ?>
      <?= info_item('وضعیت تاهل', $m['marital_status'], 'user') ?>
    </div>

    <div class="info-grid section-block">
      <?= info_item('یگان خدمت', $m['serving_unit'], 'award') ?>
      <?= info_item('نوع عضویت', $m['membership_type'], 'info') ?>
      <?= info_item('جریان رخداد', $m['event_stream'], 'heart') ?>
      <?= info_item('منطقه عملیاتی', $m['operation_zone'], 'map') ?>
      <?= info_item('دشمن', $m['enemy'], 'info') ?>
      <?= info_item('عملیات نظامی', $m['military_operation'], 'info') ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

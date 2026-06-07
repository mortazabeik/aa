<?php
require_once __DIR__ . '/includes/functions.php';

$pdo = db();

/**
 * تمام فیلترها از طریق GET خوانده می‌شوند.
 * چون مقادیر در آدرس (query string) ذخیره می‌شوند، با رفرش صفحه پاک نمی‌شوند
 * و شماره صفحه فعلی نیز در آدرس حفظ می‌شود (به ۱ برنمی‌گردد).
 */
function gp(string $key): string
{
    return isset($_GET[$key]) ? trim(to_en_digits((string)$_GET[$key])) : '';
}

$filters = [
    'q'              => gp('q'),
    'national_id'    => gp('national_id'),
    'code_isar'      => gp('code_isar'),
    'veteran_status' => gp('veteran_status'),
    'age'            => gp('age'),
    'gender'         => gp('gender'),
    'nationality'    => gp('nationality'),
    'religion'       => gp('religion'),
    'birth_day'      => gp('birth_day'),
    'birth_month'    => gp('birth_month'),
    'birth_year'     => gp('birth_year'),
    'martyrdom_day'   => gp('martyrdom_day'),
    'martyrdom_month' => gp('martyrdom_month'),
    'martyrdom_year'  => gp('martyrdom_year'),
    'birth_place'    => gp('birth_place'),
    'burial_place'   => gp('burial_place'),
    'file_location'  => gp('file_location'),
    'occupation'     => gp('occupation'),
    'serving_unit'   => gp('serving_unit'),
];

// ساخت کوئری پویا با پارامترهای bind شده (جلوگیری از SQL Injection)
$where = [];
$params = [];

if ($filters['q'] !== '') {
    $where[] = "(CONCAT(first_name,' ',last_name) LIKE :q OR first_name LIKE :q OR last_name LIKE :q)";
    $params['q'] = '%' . $filters['q'] . '%';
}
// فیلترهای LIKE (جستجوی جزئی)
$likeMap = [
    'national_id' => 'national_id', 'code_isar' => 'code_isar', 'veteran_status' => 'veteran_status',
    'birth_place' => 'birth_place', 'burial_place' => 'burial_place', 'file_location' => 'file_location',
    'occupation' => 'occupation', 'serving_unit' => 'serving_unit',
];
foreach ($likeMap as $key => $col) {
    if ($filters[$key] !== '') {
        $where[] = "`$col` LIKE :$key";
        $params[$key] = '%' . $filters[$key] . '%';
    }
}
// فیلترهای تطابق دقیق
$exactMap = ['gender' => 'gender', 'nationality' => 'nationality', 'religion' => 'religion'];
foreach ($exactMap as $key => $col) {
    if ($filters[$key] !== '') {
        $where[] = "`$col` = :$key";
        $params[$key] = $filters[$key];
    }
}
// سن
if ($filters['age'] !== '' && is_numeric($filters['age'])) {
    $where[] = "age = :age";
    $params['age'] = (int)$filters['age'];
}
// تاریخ تولد و شهادت (مقایسه عددی)
$dateNumMap = [
    'birth_day' => 'birth_day', 'birth_month' => 'birth_month', 'birth_year' => 'birth_year',
    'martyrdom_day' => 'martyrdom_day', 'martyrdom_month' => 'martyrdom_month', 'martyrdom_year' => 'martyrdom_year',
];
foreach ($dateNumMap as $key => $col) {
    if ($filters[$key] !== '' && is_numeric($filters[$key])) {
        $where[] = "CAST(`$col` AS UNSIGNED) = :$key";
        $params[$key] = (int)$filters[$key];
    }
}

$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// تعداد کل
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM martyrs $whereSql");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

// صفحه‌بندی
$perPage = 24;
$totalPages = max(1, (int)ceil($total / $perPage));
$currentPage = max(1, (int)gp('page'));
if ($currentPage > $totalPages) $currentPage = $totalPages;
$offset = ($currentPage - 1) * $perPage;

// دریافت نتایج صفحه فعلی
$sql = "SELECT * FROM martyrs $whereSql ORDER BY last_name ASC, first_name ASC LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue(':' . $k, $v);
}
$stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll();

// مقادیر یکتا برای کمبوباکس‌ها
$uVeteran = unique_values('veteran_status');
$uBirth   = unique_values('birth_place');
$uBurial  = unique_values('burial_place');
$uFile    = unique_values('file_location');
$uGender  = unique_values('gender');
$uNat     = unique_values('nationality');
$uReligion = unique_values('religion');
$uOcc     = unique_values('occupation');
$uUnit    = unique_values('serving_unit');

$isAnyAdvanced = $filters['national_id'] || $filters['code_isar'] || $filters['veteran_status'] ||
    $filters['age'] || $filters['gender'] || $filters['nationality'] || $filters['religion'] ||
    $filters['birth_day'] || $filters['birth_month'] || $filters['birth_year'] ||
    $filters['martyrdom_day'] || $filters['martyrdom_month'] || $filters['martyrdom_year'] ||
    $filters['birth_place'] || $filters['burial_place'] || $filters['file_location'] ||
    $filters['occupation'] || $filters['serving_unit'];

// تابع ساخت آدرس صفحه با حفظ فیلترها
function page_url(int $p, array $filters): string
{
    $q = array_filter($filters, fn($v) => $v !== '');
    $q['page'] = $p;
    return url('search.php') . '?' . http_build_query($q);
}

$page = 'search';
$title = 'گلزار تربت - جستجوی پیشرفته';
include __DIR__ . '/partials/header.php';
?>

<h1 class="page-title">جستجوی شهدا</h1>

<!-- فرم جستجو: متد GET تا مقادیر در آدرس بمانند -->
<form method="get" action="<?= url('search.php') ?>" class="glass p-6" id="search-form">
  <div class="flex gap-4" style="flex-wrap:wrap;">
    <div class="relative flex-1" style="min-width:240px;">
      <span style="position:absolute;inset-block:0;right:1rem;display:flex;align-items:center;color:var(--text-muted);pointer-events:none;">
        <span data-icon="search" data-size="20"></span>
      </span>
      <input type="text" name="q" value="<?= e($filters['q']) ?>" placeholder="جستجو با نام شهید..."
             class="input input-lg" style="padding-right:3rem;">
    </div>
    <button type="button" class="btn btn-ghost" data-filter-toggle>
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      فیلتر پیشرفته
    </button>
    <button type="submit" class="btn btn-primary">جستجو</button>
  </div>

  <!-- پنل فیلتر پیشرفته (در صورت فعال بودن فیلتری، باز می‌ماند) -->
  <div data-filter-panel class="<?= $isAnyAdvanced ? '' : 'hidden' ?>" style="margin-top:1.5rem;border-top:1px solid var(--divider);padding-top:1.5rem;">
    <div class="filters-grid">
      <div>
        <label class="field-label">کد ملی</label>
        <input type="text" name="national_id" value="<?= e($filters['national_id']) ?>" class="input" placeholder="مثال: ...070">
      </div>
      <div>
        <label class="field-label">کد ایثار</label>
        <input type="text" name="code_isar" value="<?= e($filters['code_isar']) ?>" class="input" placeholder="کد ایثار...">
      </div>
      <div>
        <label class="field-label">ایثارگری</label>
        <input list="l-vet" name="veteran_status" value="<?= e($filters['veteran_status']) ?>" class="input" placeholder="انتخاب...">
        <datalist id="l-vet"><?php foreach ($uVeteran as $o): ?><option value="<?= e($o) ?>"><?php endforeach; ?></datalist>
      </div>
      <div>
        <label class="field-label">سن شهادت</label>
        <input type="number" name="age" value="<?= e($filters['age']) ?>" class="input" placeholder="مثال: ۲۰">
      </div>
      <div>
        <label class="field-label">جنسیت</label>
        <div class="select-wrap">
          <select name="gender" class="select">
            <option value="">همه</option>
            <?php foreach ($uGender as $o): ?><option value="<?= e($o) ?>" <?= $filters['gender'] === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?>
          </select>
          <span class="chev"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></span>
        </div>
      </div>
      <div>
        <label class="field-label">ملیت</label>
        <div class="select-wrap">
          <select name="nationality" class="select">
            <option value="">همه</option>
            <?php foreach ($uNat as $o): ?><option value="<?= e($o) ?>" <?= $filters['nationality'] === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?>
          </select>
          <span class="chev"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></span>
        </div>
      </div>

      <!-- تاریخ تولد -->
      <div class="col-span-3" style="background:var(--hover-bg);padding:0.5rem;border-radius:0.75rem;">
        <label class="field-label" style="margin-bottom:0.5rem;">تاریخ تولد</label>
        <div class="flex gap-2">
          <input type="number" name="birth_day" value="<?= e($filters['birth_day']) ?>" class="input" placeholder="روز">
          <input type="number" name="birth_month" value="<?= e($filters['birth_month']) ?>" class="input" placeholder="ماه">
          <input type="number" name="birth_year" value="<?= e($filters['birth_year']) ?>" class="input" placeholder="سال">
        </div>
      </div>
      <!-- تاریخ شهادت -->
      <div class="col-span-3" style="background:var(--hover-bg);padding:0.5rem;border-radius:0.75rem;">
        <label class="field-label" style="margin-bottom:0.5rem;">تاریخ شهادت</label>
        <div class="flex gap-2">
          <input type="number" name="martyrdom_day" value="<?= e($filters['martyrdom_day']) ?>" class="input" placeholder="روز">
          <input type="number" name="martyrdom_month" value="<?= e($filters['martyrdom_month']) ?>" class="input" placeholder="ماه">
          <input type="number" name="martyrdom_year" value="<?= e($filters['martyrdom_year']) ?>" class="input" placeholder="سال">
        </div>
      </div>

      <div class="col-span-2">
        <label class="field-label">محل تولد</label>
        <input list="l-bp" name="birth_place" value="<?= e($filters['birth_place']) ?>" class="input" placeholder="جستجوی منطقه...">
        <datalist id="l-bp"><?php foreach ($uBirth as $o): ?><option value="<?= e($o) ?>"><?php endforeach; ?></datalist>
      </div>
      <div class="col-span-2">
        <label class="field-label">محل گلزار</label>
        <input list="l-bur" name="burial_place" value="<?= e($filters['burial_place']) ?>" class="input" placeholder="جستجوی گلزار...">
        <datalist id="l-bur"><?php foreach ($uBurial as $o): ?><option value="<?= e($o) ?>"><?php endforeach; ?></datalist>
      </div>
      <div class="col-span-2">
        <label class="field-label">محل پرونده</label>
        <input list="l-file" name="file_location" value="<?= e($filters['file_location']) ?>" class="input" placeholder="جستجوی محل پرونده...">
        <datalist id="l-file"><?php foreach ($uFile as $o): ?><option value="<?= e($o) ?>"><?php endforeach; ?></datalist>
      </div>

      <div class="col-span-2">
        <label class="field-label">مذهب</label>
        <div class="select-wrap">
          <select name="religion" class="select">
            <option value="">همه</option>
            <?php foreach ($uReligion as $o): ?><option value="<?= e($o) ?>" <?= $filters['religion'] === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?>
          </select>
          <span class="chev"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></span>
        </div>
      </div>
      <div class="col-span-2">
        <label class="field-label">شغل</label>
        <input list="l-occ" name="occupation" value="<?= e($filters['occupation']) ?>" class="input" placeholder="جستجوی شغل...">
        <datalist id="l-occ"><?php foreach ($uOcc as $o): ?><option value="<?= e($o) ?>"><?php endforeach; ?></datalist>
      </div>
      <div class="col-span-2">
        <label class="field-label">یگان خدمت</label>
        <input list="l-unit" name="serving_unit" value="<?= e($filters['serving_unit']) ?>" class="input" placeholder="جستجوی یگان...">
        <datalist id="l-unit"><?php foreach ($uUnit as $o): ?><option value="<?= e($o) ?>"><?php endforeach; ?></datalist>
      </div>
    </div>

    <div class="flex justify-between mt-4" style="align-items:center;">
      <a href="<?= url('search.php') ?>" class="btn btn-rose">پاک کردن همه فیلترها</a>
      <button type="submit" class="btn btn-primary">اعمال فیلترها</button>
    </div>
  </div>
</form>

<!-- نتایج -->
<p class="text-muted mt-6 mb-4" style="font-size:0.875rem;"><?= $total ?> نتیجه یافت شد</p>

<?php if (count($results) > 0): ?>
  <div class="grid grid-cards">
    <?php foreach ($results as $m) echo render_martyr_card($m); ?>
  </div>

  <?php if ($totalPages > 1): ?>
  <nav class="pagination" aria-label="صفحه‌بندی">
    <?php if ($currentPage > 1): ?>
      <a href="<?= e(page_url($currentPage - 1, $filters)) ?>">قبلی</a>
    <?php else: ?><span class="disabled">قبلی</span><?php endif; ?>

    <?php
      $start = max(1, $currentPage - 2);
      $end = min($totalPages, $currentPage + 2);
      if ($start > 1) echo '<a href="' . e(page_url(1, $filters)) . '">۱</a><span class="disabled">…</span>';
      for ($p = $start; $p <= $end; $p++):
    ?>
      <?php if ($p === $currentPage): ?>
        <span class="current"><?= $p ?></span>
      <?php else: ?>
        <a href="<?= e(page_url($p, $filters)) ?>"><?= $p ?></a>
      <?php endif; ?>
    <?php endfor;
      if ($end < $totalPages) echo '<span class="disabled">…</span><a href="' . e(page_url($totalPages, $filters)) . '">' . $totalPages . '</a>';
    ?>

    <?php if ($currentPage < $totalPages): ?>
      <a href="<?= e(page_url($currentPage + 1, $filters)) ?>">بعدی</a>
    <?php else: ?><span class="disabled">بعدی</span><?php endif; ?>
  </nav>
  <?php endif; ?>
<?php else: ?>
  <div class="glass p-10 text-center">
    <h3 style="font-weight:700;">نتیجه‌ای یافت نشد</h3>
    <p class="text-muted mt-2">لطفاً عبارت یا فیلترهای جستجو را تغییر دهید.</p>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/functions.php';

$pdo = db();

// اسلایدرها
$sliders = $pdo->query("SELECT * FROM sliders ORDER BY sort_order ASC")->fetchAll();

// شهدای روز (بر اساس تاریخ شمسی امروز)
function jalali_today(): array
{
    // محاسبه ساده تاریخ شمسی امروز (ماه و روز) با تبدیل گریگوری
    $tz = new DateTime('now', new DateTimeZone('Asia/Tehran'));
    $gy = (int)$tz->format('Y');
    $gm = (int)$tz->format('n');
    $gd = (int)$tz->format('j');
    return gregorian_to_jalali($gy, $gm, $gd);
}

function gregorian_to_jalali($gy, $gm, $gd): array
{
    $g_d_m = [0,31,59,90,120,151,181,212,243,273,304,334];
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100))
          + ((int)(($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
    $jy = -1595 + (33 * ((int)($days / 12053)));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    if ($days > 365) {
        $jy += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    if ($days < 186) {
        $jm = 1 + (int)($days / 31);
        $jd = 1 + ($days % 31);
    } else {
        $jm = 7 + (int)(($days - 186) / 30);
        $jd = 1 + (($days - 186) % 30);
    }
    return [$jy, $jm, $jd];
}

[$jy, $jm, $jd] = jalali_today();

// شهدایی که امروز (همین ماه و روز شمسی) به شهادت رسیده‌اند
$stmt = $pdo->prepare(
    "SELECT * FROM martyrs
     WHERE CAST(martyrdom_month AS UNSIGNED) = :m
       AND CAST(martyrdom_day AS UNSIGNED) = :d
     LIMIT 3"
);
$stmt->execute(['m' => $jm, 'd' => $jd]);
$martyrsOfDay = $stmt->fetchAll();

// در صورت نبود، چند شهید نمایش بده تا صفحه خالی نباشد
if (count($martyrsOfDay) === 0) {
    $martyrsOfDay = $pdo->query("SELECT * FROM martyrs ORDER BY id ASC LIMIT 3")->fetchAll();
}

$blogPosts = [
    ['title' => 'برگزاری مراسم بزرگداشت شهدای گمنام', 'date' => '۲۴ اردیبهشت ۱۴۰۵',
     'excerpt' => 'مراسم بزرگداشت شهدای گمنام با حضور پرشور مردم و مسئولین برگزار شد...'],
    ['title' => 'انتشار کتاب جدید «خاطرات اروند»', 'date' => '۱۸ اردیبهشت ۱۴۰۵',
     'excerpt' => 'کتاب جدیدی شامل خاطرات ناگفته رزمندگان از عملیات والفجر ۸ منتشر شد...'],
    ['title' => 'فراخوان جشنواره شعر دفاع مقدس', 'date' => '۱۰ اردیبهشت ۱۴۰۵',
     'excerpt' => 'دهمین دوره جشنواره سراسری شعر دفاع مقدس آغاز به کار کرد...'],
];

$page = 'home';
$title = 'گلزار تربت - صفحه اصلی';
include __DIR__ . '/partials/header.php';
?>

<!-- اسلایدر -->
<?php if (count($sliders) > 0): ?>
<section class="slider-section">
  <div class="slider-track" data-slider>
    <?php foreach ($sliders as $s): ?>
      <div class="slide"><img src="<?= url($s['url']) ?>" alt="اسلاید"></div>
    <?php endforeach; ?>
  </div>
  <div class="slider-controls glass">
    <button data-slider-prev aria-label="قبلی">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    <div class="dots">
      <?php foreach ($sliders as $i => $s): ?>
        <button class="dot <?= $i === 0 ? 'active' : '' ?>" data-slider-dot aria-label="اسلاید <?= $i + 1 ?>"></button>
      <?php endforeach; ?>
    </div>
    <button data-slider-next aria-label="بعدی">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
  </div>
</section>
<?php endif; ?>

<!-- معرفی -->
<section class="section">
  <div class="glass intro">
    <div class="text">
      <h2>درباره سامانه گلزار تربت</h2>
      <p>
        این سامانه با هدف زنده نگه داشتن یاد و خاطره شهیدان گرانقدر تربت حیدریه و شهرستان‌های اطراف
        و دسترسی آسان به مشخصات این عزیزان راه‌اندازی شده است. ما بر این باوریم که ترویج فرهنگ ایثار
        و شهادت، ضامن بقای ارزش‌های والای انسانی است.
      </p>
    </div>
    <div class="art">
      <?php $roseClass = ''; include __DIR__ . '/partials/rose.php'; ?>
    </div>
  </div>
</section>

<!-- شهدای روز -->
<section class="section">
  <div class="section-head">
    <div>
      <h3>شهدای روز</h3>
      <p>آشنایی با قهرمانانی که در این روز به شهادت رسیده‌اند</p>
    </div>
    <a href="<?= url('search.php') ?>" class="btn btn-ghost">مشاهده همه</a>
  </div>
  <div class="grid grid-cards" style="grid-template-columns:repeat(auto-fill,minmax(240px,1fr));">
    <?php foreach ($martyrsOfDay as $m) echo render_martyr_card($m); ?>
  </div>
</section>

<!-- اخبار -->
<section class="section">
  <div class="section-head">
    <div>
      <h3>اخبار و رویدادها</h3>
      <p>تازه‌ترین اخبار مرتبط با فرهنگ ایثار و شهادت</p>
    </div>
  </div>
  <div class="grid grid-cards" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr));">
    <?php foreach ($blogPosts as $post): ?>
      <article class="glass glass-hover" style="overflow:hidden;border-radius:1rem;">
        <div style="padding:1.5rem;">
          <span class="text-muted" style="font-size:0.75rem;"><?= e($post['date']) ?></span>
          <h4 style="font-size:1.125rem;font-weight:700;margin:0.5rem 0;"><?= e($post['title']) ?></h4>
          <p class="text-muted" style="font-size:0.875rem;"><?= e($post['excerpt']) ?></p>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

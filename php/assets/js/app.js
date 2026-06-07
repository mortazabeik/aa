/* ============================================================
   گلزار تربت - اسکریپت‌های کلاینت
   ============================================================ */

// --- مدیریت تم (روشن/تاریک/سیستمی) ---
(function () {
  const KEY = 'golzar_theme';
  function apply(theme) {
    const isDark = theme === 'dark' || (theme === 'system' &&
      window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', isDark);
    document.querySelectorAll('[data-theme-btn]').forEach((b) => {
      b.classList.toggle('active', b.dataset.themeBtn === theme);
    });
  }
  window.getTheme = () => localStorage.getItem(KEY) || 'system';
  window.setTheme = (t) => { localStorage.setItem(KEY, t); apply(t); };
  apply(window.getTheme());
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (window.getTheme() === 'system') apply('system');
  });
  document.addEventListener('DOMContentLoaded', () => {
    apply(window.getTheme());
    document.querySelectorAll('[data-theme-btn]').forEach((b) => {
      b.addEventListener('click', () => window.setTheme(b.dataset.themeBtn));
    });
    const mobile = document.querySelector('[data-theme-cycle]');
    if (mobile) {
      mobile.addEventListener('click', () => {
        const order = { light: 'dark', dark: 'system', system: 'light' };
        window.setTheme(order[window.getTheme()] || 'light');
      });
    }
  });
})();

// --- اسلایدر صفحه اصلی ---
function initSlider() {
  const track = document.querySelector('[data-slider]');
  if (!track) return;
  const slides = Array.from(track.querySelectorAll('.slide'));
  const dots = Array.from(document.querySelectorAll('[data-slider-dot]'));
  if (slides.length === 0) return;
  let current = 0;
  let timer;

  function render() {
    const n = slides.length;
    slides.forEach((slide, index) => {
      let pos = index - current;
      if (pos > n / 2) pos -= n;
      else if (pos < -n / 2) pos += n;
      const x = pos * 100 * -1;
      const scale = 1 - Math.abs(pos) * 0.15;
      const opacity = Math.abs(pos) > 1.5 ? 0 : 1;
      slide.style.transform = `translateX(${x}%) scale(${scale})`;
      slide.style.opacity = opacity;
      slide.style.zIndex = 10 - Math.abs(pos);
    });
    dots.forEach((d, i) => d.classList.toggle('active', i === current));
  }
  function go(i) { current = (i + slides.length) % slides.length; render(); }
  function next() { go(current + 1); }
  function prev() { go(current - 1); }
  function reset() { clearInterval(timer); timer = setInterval(next, 6000); }

  document.querySelector('[data-slider-next]')?.addEventListener('click', () => { next(); reset(); });
  document.querySelector('[data-slider-prev]')?.addEventListener('click', () => { prev(); reset(); });
  dots.forEach((d, i) => d.addEventListener('click', () => { go(i); reset(); }));
  slides.forEach((s, i) => s.addEventListener('click', () => { if (i !== current) { go(i); reset(); } }));

  // کشیدن با لمس
  let startX = 0;
  track.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
  track.addEventListener('touchend', (e) => {
    const diff = e.changedTouches[0].clientX - startX;
    if (diff > 50) next();
    else if (diff < -50) prev();
    reset();
  });

  render();
  reset();
}
document.addEventListener('DOMContentLoaded', initSlider);

// --- آیکون‌های SVG ساده ---
const ICONS = {
  home: '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
  search: '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
  plus: '<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>',
  sun: '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>',
  moon: '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>',
  monitor: '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
};
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-icon]').forEach((el) => {
    const name = el.dataset.icon;
    if (ICONS[name]) {
      el.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="${el.dataset.size || 20}" height="${el.dataset.size || 20}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${ICONS[name]}</svg>`;
    }
  });
});

// --- مودال ویرایش شهید ---
function openEditModal(id) {
  const modal = document.getElementById('edit-modal');
  if (!modal) return;
  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  const form = document.getElementById('edit-form');
  form.querySelectorAll('[data-field]').forEach((inp) => { inp.value = ''; });
  // دریافت داده شهید
  fetch(window.API_MARTYR + '?id=' + encodeURIComponent(id))
    .then((r) => r.json())
    .then((data) => {
      if (!data || data.error) { alert('خطا در دریافت اطلاعات'); return; }
      form.querySelector('[name="id"]').value = data.id;
      form.querySelectorAll('[data-field]').forEach((inp) => {
        const key = inp.dataset.field;
        if (data[key] !== undefined && data[key] !== null) inp.value = data[key];
      });
      const img = document.getElementById('edit-current-img');
      if (img) img.src = data.profile_image ? (window.BASE_URL + '/' + data.profile_image) : window.ROSE_IMG;
    });
}
function closeEditModal() {
  const modal = document.getElementById('edit-modal');
  if (modal) modal.classList.add('hidden');
  document.body.style.overflow = '';
}
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-edit-id]').forEach((btn) => {
    btn.addEventListener('click', () => openEditModal(btn.dataset.editId));
  });
  document.querySelectorAll('[data-close-modal]').forEach((btn) => {
    btn.addEventListener('click', closeEditModal);
  });
});

// --- تایید حذف ---
function confirmDelete(msg) { return window.confirm(msg || 'آیا از حذف اطمینان دارید؟'); }

// --- باز/بسته کردن فیلتر پیشرفته ---
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('[data-filter-toggle]');
  const panel = document.querySelector('[data-filter-panel]');
  if (toggle && panel) {
    toggle.addEventListener('click', () => panel.classList.toggle('hidden'));
  }
});

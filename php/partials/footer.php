    </div><!-- /.container -->
  </main>

  <footer class="site-footer">
    <div class="inner">
      <p class="text-muted" style="font-size:0.875rem;">
        © ۱۴۰۵ سامانه گلزار تربت. تمامی حقوق محفوظ است.
      </p>
      <a href="<?= url('admin/login.php') ?>" class="text-muted flex items-center gap-2" style="font-size:0.875rem;">
        ورود ادمین
      </a>
    </div>
  </footer>

  <!-- ناوبری موبایل -->
  <nav class="mobile-nav glass">
    <a href="<?= url('index.php') ?>" class="<?= ($page ?? '') === 'home' ? 'active' : '' ?>">
      <span data-icon="home" data-size="22"></span>
      <span>صفحه اصلی</span>
    </a>
    <a href="<?= url('search.php') ?>" class="<?= ($page ?? '') === 'search' ? 'active' : '' ?>">
      <span data-icon="search" data-size="22"></span>
      <span>جستجو</span>
    </a>
    <a href="<?= url('submit.php') ?>" class="<?= ($page ?? '') === 'submit' ? 'active' : '' ?>">
      <span data-icon="plus" data-size="22"></span>
      <span>ارسال آثار</span>
    </a>
  </nav>
</body>
</html>

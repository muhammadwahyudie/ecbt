<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'CBT Sekolah') ?></title>
  <meta name="csrf-token-name" content="<?= csrf_token() ?>">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <!-- Bootstrap 5 first (base) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <!-- App CSS last (overrides Bootstrap) -->
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/app.css') ?>">
  <?= $this->renderSection('styles') ?>
</head>
<body>

<div class="layout">

  <!-- ─── Sidebar ────────────────────────────────────────────────── -->
  <?php $role = session()->get('role'); ?>
  <aside class="sidebar" id="sidebar">

    <a href="<?= base_url($role . '/dashboard') ?>" class="sidebar-logo">
      <div class="sidebar-logo-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
        </svg>
      </div>
      <div>
        <div class="sidebar-logo-text">CBT Sekolah</div>
        <div class="sidebar-logo-sub">Computer Based Test</div>
      </div>
    </a>

    <!-- Nav per peran -->
    <?php if ($role === 'admin'): ?>
      <?= view('partials/_nav_admin') ?>
    <?php elseif ($role === 'proktor'): ?>
      <?= view('partials/_nav_proktor') ?>
    <?php elseif ($role === 'guru'): ?>
      <?= view('partials/_nav_guru') ?>
    <?php elseif ($role === 'siswa'): ?>
      <?= view('partials/_nav_siswa') ?>
    <?php endif; ?>

  </aside>

  <!-- ─── Main ─────────────────────────────────────────────────────── -->
  <div class="main">

    <!-- Header -->
    <header class="header">
      <!-- Mobile toggle -->
      <button class="btn-icon d-md-none" id="sidebar-toggle" title="Menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
      </button>

      <div class="header-title-wrap">
        <div class="header-title"><?= esc($pageTitle ?? $title ?? 'Dashboard') ?></div>
        <div class="header-meta">
          <?= date('l, d F Y') ?> &nbsp;&middot;&nbsp;
          <?php
            $ta = \Config\Database::connect()->table('tahun_ajaran')->where('aktif', 1)->get()->getRow();
            echo $ta ? esc("TA {$ta->nama} Semester {$ta->semester}") : 'Tahun Ajaran Belum Diset';
          ?>
        </div>
      </div>

      <div class="header-actions">
        <div class="header-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
          </svg>
          <input type="text" placeholder="Cari..." id="global-search">
        </div>

        <a href="#" class="btn-icon" title="Notifikasi">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
          </svg>
        </a>

        <!-- Profil & Tombol Logout di Pojok Kanan Atas -->
        <div class="header-user-profile">
          <div class="header-avatar" title="<?= esc(session()->get('nama')) ?>">
            <?= strtoupper(substr(session()->get('nama') ?? 'U', 0, 1)) ?>
          </div>
          <div class="header-user-info d-none d-md-flex">
            <span class="header-user-name"><?= esc(session()->get('nama')) ?></span>
            <span class="header-user-role"><?= esc(session()->get('role')) ?></span>
          </div>
          <a href="<?= base_url('/logout') ?>" class="header-logout-btn" title="Keluar dari sistem">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
            </svg>
            <span>Keluar</span>
          </a>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main class="content">
      <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
      </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
      <?php endif; ?>

      <?= $this->renderSection('content') ?>
    </main>

  </div><!-- .main -->
</div><!-- .layout -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('assets/js/app.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/app.js') ?>"></script>
<script>
  // Sidebar toggle mobile
  document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
  });

  // CSRF token for AJAX
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': csrfToken }
  });

  // Pengelolaan sub-menu: hanya menu aktif yang terbuka, menu lain tertutup
  (function() {
    try {
      localStorage.removeItem('cbt_open_dropdowns');
    } catch(e) {}

    $(document).ready(function() {
      // Pastikan dropdown yang memiliki sub-menu aktif tetap terbuka, yang tidak aktif tertutup
      $('.nav-dropdown').each(function() {
        const $col = $(this).find('.collapse');
        const $btn = $(this).find('.nav-dropdown-toggle');
        const hasActive = $(this).find('.nav-subitem.active').length > 0;

        if (hasActive) {
          $col.addClass('show');
          $btn.attr('aria-expanded', 'true').removeClass('collapsed');
        } else {
          $col.removeClass('show');
          $btn.attr('aria-expanded', 'false').addClass('collapsed');
        }
      });

      // Efek Accordion: jika satu dropdown dibuka, tutup otomatis dropdown lain yang sedang terbuka
      $('.nav-dropdown .collapse').on('show.bs.collapse', function() {
        const current = this;
        $('.nav-dropdown .collapse').each(function() {
          if (this !== current && $(this).hasClass('show')) {
            const bsCollapse = bootstrap.Collapse.getInstance(this) || new bootstrap.Collapse(this, { toggle: false });
            bsCollapse.hide();
          }
        });
      });
    });
  })();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>

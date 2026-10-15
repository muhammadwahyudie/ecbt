<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Ujian Berlangsung') ?> — CBT Sekolah</title>
  <meta name="csrf-token-name" content="<?= csrf_token() ?>">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">

  <!-- Google Fonts: Sora & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">

  <!-- CSS Design System -->
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- KaTeX CSS for Math expressions -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">

  <style>
    body {
      background-color: var(--color-bg, #F3F6F7);
      color: var(--color-text, #10222B);
      font-family: 'Plus Jakarta Sans', sans-serif;
      user-select: none;
      -webkit-user-select: none;
    }
    .exam-header {
      background-color: #0F2229;
      color: #FFFFFF;
      height: 64px;
      padding: 0 1.5rem;
      position: sticky;
      top: 0;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #1D3B45;
    }
    .exam-title {
      font-family: 'Sora', sans-serif;
      font-size: 1.1rem;
      font-weight: 600;
      line-height: 1.2;
    }
    .exam-meta {
      font-size: 0.8rem;
      color: #94A3B8;
    }
    .exam-timer {
      background: #1D3B45;
      padding: 0.35rem 1rem;
      border-radius: 8px;
      font-family: 'Sora', sans-serif;
      font-weight: 700;
      font-size: 1.25rem;
      letter-spacing: 1px;
      color: #38BDF8;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .exam-timer.timer-warning {
      background: #7F1D1D;
      color: #F87171;
      animation: pulse 1s infinite alternate;
    }
    @keyframes pulse {
      from { opacity: 0.8; }
      to { opacity: 1; }
    }
    .status-save-indicator {
      font-size: 0.85rem;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
    }
    .nav-nomor-btn {
      width: 44px;
      height: 44px;
      font-weight: 600;
      font-size: 0.95rem;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #CBD5E1;
      background: #FFFFFF;
      color: #334155;
      transition: all 0.15s ease;
      cursor: pointer;
    }
    .nav-nomor-btn:hover {
      border-color: #0E7C86;
      background: #F1F5F9;
    }
    .nav-nomor-btn.active {
      border-color: #0E7C86;
      background: #0E7C86;
      color: #FFFFFF !important;
      font-weight: 700;
    }
    .nav-nomor-btn.dijawab {
      background: #0E7C86;
      color: #FFFFFF;
      border-color: #0E7C86;
    }
    .nav-nomor-btn.ragu {
      background: #F59E0B;
      color: #FFFFFF;
      border-color: #D97706;
    }
  </style>
  <?= $this->renderSection('styles') ?>
</head>
<body>

  <!-- Top Examination Bar -->
  <header class="exam-header">
    <div class="d-flex align-items-center gap-3">
      <div>
        <div class="exam-title"><?= esc($ujian['nama'] ?? 'Ujian Berbasis Komputer') ?></div>
        <div class="exam-meta">
          Peserta: <strong><?= esc(session()->get('nama')) ?></strong> (<?= esc(session()->get('username')) ?>)
        </div>
      </div>
    </div>

    <!-- Center: Autosave Status -->
    <div class="d-none d-md-flex align-items-center gap-2">
      <span id="saveStatusIndicator" class="status-save-indicator text-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Tersimpan
      </span>
    </div>

    <!-- Right: Countdown Timer & Selesai button -->
    <div class="d-flex align-items-center gap-3">
      <div class="exam-timer" id="countdownBox">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span id="examCountdown">00:00:00</span>
      </div>

      <button type="button" class="btn btn-danger btn-sm px-3 fw-semibold" id="btnSelesaiUjianTop">
        Selesai Ujian
      </button>
    </div>
  </header>

  <!-- Examination Main Area -->
  <main class="container-fluid py-4 px-3 px-md-4">
    <?= $this->renderSection('content') ?>
  </main>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
  <script src="<?= base_url('assets/js/app.js') ?>"></script>

  <script>
    // AJAX CSRF Token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': csrfToken }
    });

    // Disable right click and devtools shortcut keys in exam layout
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
      if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) || (e.ctrlKey && e.key === 'U')) {
        e.preventDefault();
      }
    });
  </script>

  <?= $this->renderSection('scripts') ?>
</body>
</html>

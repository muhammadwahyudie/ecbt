<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'CBT Sekolah') ?></title>
  <meta name="description" content="Sistem Computer Based Test — Login">
  <!-- CSRF -->
  <meta name="csrf-token-name" content="<?= csrf_token() ?>">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <!-- Fonts & Styles -->
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
  <style>
    /* ─── Login page specific ─ */
    body { background: var(--sidebar); display: flex; align-items: center; justify-content: center; min-height: 100vh; }

    .login-wrap {
      width: 100%;
      max-width: 420px;
      padding: 20px;
    }

    .login-brand {
      text-align: center;
      margin-bottom: 32px;
    }

    .login-brand-icon {
      width: 60px; height: 60px;
      background: var(--accent);
      border-radius: 18px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 14px;
    }

    .login-brand-icon svg { width: 32px; height: 32px; color: #fff; stroke-width: 1.5; }

    .login-brand-title {
      font-family: var(--font-head);
      font-size: 1.5rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 4px;
    }

    .login-brand-sub {
      font-size: 0.85rem;
      color: var(--sidebar-text);
    }

    .login-card {
      background: var(--surface);
      border-radius: var(--r-card);
      padding: 32px;
      border: 1px solid rgba(255,255,255,0.05);
    }

    .login-card h2 {
      font-size: 1.1rem;
      margin-bottom: 4px;
    }

    .login-card p {
      font-size: 0.85rem;
      color: var(--text-sec);
      margin-bottom: 24px;
    }

    .input-icon-wrap {
      position: relative;
    }

    .input-icon-wrap .form-control {
      padding-left: 42px;
    }

    .input-icon-wrap .icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-sec);
    }

    .input-icon-wrap .icon svg { width: 17px; height: 17px; stroke-width: 1.75; }

    .login-footer {
      text-align: center;
      margin-top: 24px;
      font-size: 0.78rem;
      color: var(--sidebar-text);
    }

    .login-footer a { color: var(--accent); text-decoration: none; }
    .login-footer a:hover { text-decoration: underline; }
  </style>
</head>
<body>

<div class="login-wrap">
  <!-- Brand -->
  <div class="login-brand">
    <div class="login-brand-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
      </svg>
    </div>
    <div class="login-brand-title">CBT Sekolah</div>
    <div class="login-brand-sub">Computer Based Test System</div>
  </div>

  <!-- Card -->
  <div class="login-card">
    <h2>Masuk ke akun Anda</h2>
    <p>Gunakan username, NIS, atau NISN untuk login.</p>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" role="alert">
      <?= esc(session()->getFlashdata('error')) ?>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" role="alert">
      <?= esc(session()->getFlashdata('success')) ?>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('/login') ?>" method="POST" id="form-login" novalidate>
      <?= csrf_field() ?>

      <div class="form-group">
        <label class="form-label" for="username">Username / NIS / NISN <span>*</span></label>
        <div class="input-icon-wrap">
          <span class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
          </span>
          <input
            type="text"
            id="username"
            name="username"
            class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
            value="<?= esc(old('username')) ?>"
            placeholder="Masukkan username, NIS, atau NISN"
            autocomplete="username"
            required>
        </div>
        <?php if (isset($errors['username'])): ?>
          <div class="form-error"><?= esc($errors['username']) ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Kata Sandi <span>*</span></label>
        <div class="input-icon-wrap">
          <span class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
          </span>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
            placeholder="Masukkan kata sandi"
            autocomplete="current-password"
            required>
        </div>
        <?php if (isset($errors['password'])): ?>
          <div class="form-error"><?= esc($errors['password']) ?></div>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn btn-primary w-100" id="btn-login">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
        Masuk
      </button>
    </form>
  </div>

  <div class="login-footer">
    &copy; <?= date('Y') ?> CBT Sekolah &mdash; Versi 1.0
  </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Flash message via SweetAlert2
  <?php if (session()->getFlashdata('error')): ?>
  Swal.fire({
    icon: 'error',
    title: 'Login Gagal',
    text: '<?= addslashes(session()->getFlashdata('error')) ?>',
    confirmButtonColor: '#0E7C86',
    confirmButtonText: 'OK'
  });
  <?php endif; ?>
</script>
</body>
</html>

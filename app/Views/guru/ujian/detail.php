<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<div class="d-flex gap-2">
  <a href="<?= base_url('guru/ujian') ?>" class="btn btn-outline-secondary btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Kembali
  </a>
  <a href="<?= base_url('guru/koreksi/ujian/' . $ujian['id']) ?>" class="btn btn-outline-primary btn-sm">
    Koreksi Nilai
  </a>
  <a href="<?= base_url('guru/nilai/ujian/' . $ujian['id']) ?>" class="btn btn-primary btn-sm">
    Rekap Nilai
  </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">
  <div class="col-12 col-lg-8">
    <div class="card mb-4 border-0" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
      <div class="card-body p-4">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="badge bg-accent-light text-accent"><?= esc($ujian['nama_mapel'] ?? '-') ?></span>
          <span class="badge bg-info text-capitalize"><?= esc($ujian['status']) ?></span>
        </div>
        <h3 class="fw-bold mb-1" style="font-family: 'Sora', sans-serif;"><?= esc($ujian['nama']) ?></h3>
        <p class="mb-0 text-white-50">Paket Soal: <strong><?= esc($ujian['nama_paket']) ?></strong></p>
      </div>
    </div>

    <!-- 4 Mini Stat Card Peserta -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
          <div class="text-secondary small fw-semibold">Belum Masuk</div>
          <div class="fs-3 fw-bold text-dark tabular-nums"><?= $stats['belum_login'] ?></div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card p-3 text-center border-warning">
          <div class="text-warning small fw-semibold">Mengerjakan</div>
          <div class="fs-3 fw-bold text-warning tabular-nums"><?= $stats['mengerjakan'] ?></div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card p-3 text-center border-success">
          <div class="text-success small fw-semibold">Telah Selesai</div>
          <div class="fs-3 fw-bold text-success tabular-nums"><?= $stats['selesai'] ?></div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card p-3 text-center border-danger">
          <div class="text-danger small fw-semibold">Terkunci</div>
          <div class="fs-3 fw-bold text-danger tabular-nums"><?= $stats['terkunci'] ?></div>
        </div>
      </div>
    </div>

    <!-- Parameter Ujian -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">Parameter Ujian</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-6 col-md-4">
            <div class="small text-muted">Jadwal Mulai</div>
            <div class="fw-semibold text-dark tabular-nums"><?= date('d/m/Y H:i', strtotime($ujian['mulai_at'])) ?> WIB</div>
          </div>
          <div class="col-6 col-md-4">
            <div class="small text-muted">Jadwal Selesai</div>
            <div class="fw-semibold text-dark tabular-nums"><?= date('d/m/Y H:i', strtotime($ujian['selesai_at'])) ?> WIB</div>
          </div>
          <div class="col-6 col-md-4">
            <div class="small text-muted">Durasi Pengerjaan</div>
            <div class="fw-semibold text-dark tabular-nums"><?= esc($ujian['durasi_menit']) ?> Menit</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kelas Peserta -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Rombel Peserta Ujian</h5>
      </div>
      <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($kelasList as $k): ?>
            <span class="badge bg-light text-dark border fs-6 px-3 py-2">
              <?= esc($k['nama']) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Token Card -->
  <div class="col-12 col-lg-4">
    <div class="card text-center p-4 shadow-sm" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
      <div class="text-white-50 text-uppercase letter-spacing-1 small mb-1">Token Akses Siswa</div>
      <div class="fs-1 fw-bold font-monospace text-warning py-3" style="letter-spacing: 6px;">
        <?= esc($ujian['token'] ?? '------') ?>
      </div>
      <div class="small text-white-50 mb-3">
        Bagikan token kepada siswa saat ujian dimulai
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-light btn-sm flex-grow-1 btn-salin-token" data-token="<?= esc($ujian['token']) ?>">
          Salin Token
        </button>
        <form action="<?= base_url('guru/ujian/token/' . $ujian['id']) ?>" method="POST" class="d-inline flex-grow-1">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">
            Buat Ulang
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('.btn-salin-token').on('click', function() {
    const token = $(this).data('token');
    navigator.clipboard.writeText(token).then(() => {
      showToast('success', 'Token disalin ke clipboard!');
    });
  });
});
</script>
<?= $this->endSection() ?>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-sm border-0 text-center p-4 border-top border-danger border-4" style="border-radius: 14px;">
      <div class="card-body">
        <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center bg-danger-light text-danger rounded-circle" style="width: 72px; height: 72px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="36" height="36">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
          </svg>
        </div>

        <h3 class="fw-bold text-danger mb-1" style="font-family: 'Sora', sans-serif;">Ujian Terkunci!</h3>
        <p class="text-secondary small mb-4">Sesi ujian Anda dihentikan dan dikunci secara otomatis oleh sistem keamanan CBT.</p>

        <div class="alert alert-danger text-start small mb-4">
          <strong>Penyebab Terkunci:</strong><br>
          Anda terdeteksi melakukan pelanggaran navigasi (seperti berpindah tab browser, membuka aplikasi lain, atau keluar dari layar penuh) melebihi batas toleransi yang diizinkan (<strong><?= esc($ujian['batas_pelanggaran']) ?> kali</strong>).
        </div>

        <div class="bg-light p-3 rounded-3 text-start small mb-4 border">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Nama Siswa:</span>
            <span class="fw-semibold text-dark"><?= esc(session()->get('nama')) ?></span>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Nama Ujian:</span>
            <span class="fw-semibold text-dark"><?= esc($ujian['nama']) ?></span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Jumlah Pelanggaran:</span>
            <span class="fw-bold text-danger"><?= esc($peserta['pelanggaran']) ?> kali</span>
          </div>
        </div>

        <div class="text-secondary small mb-4">
          Silakan melapor dan meminta bantuan kepada <strong>Proktor / Pengawas Ruang</strong> untuk membuka kunci ujian Anda.
        </div>

        <a href="<?= base_url('siswa/dashboard') ?>" class="btn btn-outline-secondary px-4">
          Kembali ke Dashboard
        </a>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

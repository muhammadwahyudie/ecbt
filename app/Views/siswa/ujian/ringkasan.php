<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-sm border-0 text-center p-4" style="border-radius: 14px;">
      <div class="card-body">
        <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center bg-success-light text-success rounded-circle" style="width: 72px; height: 72px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="36" height="36">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"/>
          </svg>
        </div>

        <h3 class="fw-bold text-dark mb-1" style="font-family: 'Sora', sans-serif;">Ujian Berhasil Diselesaikan!</h3>
        <p class="text-secondary small mb-4">Jawaban Anda telah tersimpan dan terverifikasi secara aman di server CBT.</p>

        <div class="bg-light p-3 rounded-3 text-start small mb-4 border">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Nama Ujian:</span>
            <span class="fw-semibold text-dark"><?= esc($ujian['nama']) ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Nama Siswa:</span>
            <span class="fw-semibold text-dark"><?= esc(session()->get('nama')) ?> (<?= esc(session()->get('username')) ?>)</span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Waktu Selesai:</span>
            <span class="fw-semibold text-dark tabular-nums"><?= $peserta['selesai_at'] ? date('d/m/Y H:i', strtotime($peserta['selesai_at'])) : date('d/m/Y H:i') ?> WIB</span>
          </div>
        </div>

        <?php if ($ujian['tampil_hasil'] === 'langsung' && !empty($hasil)): ?>
          <div class="p-4 rounded-3 mb-4" style="background-color: #0F2229; color: #FFFFFF;">
            <div class="text-white-50 text-uppercase letter-spacing-1 small mb-1">Nilai Akhir Anda</div>
            <div class="display-4 fw-bold text-success font-monospace" style="font-family: 'Sora', sans-serif;">
              <?= round($hasil['nilai_akhir'], 1) ?>
            </div>
            <div class="small text-white-50 mt-2">
              Status Koreksi: <?= $hasil['status_koreksi'] === 'selesai' ? '<span class="text-success">Telah Dinilai Penuh</span>' : '<span class="text-warning">Menunggu Koreksi Soal Uraian</span>' ?>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-info small py-3 mb-4">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
            Sesuai kebijakan pelaksanaan, nilai ujian akan dipublikasikan oleh pihak sekolah atau guru pengampu setelah seluruh sesi ujian berakhir.
          </div>
        <?php endif; ?>

        <a href="<?= base_url('siswa/dashboard') ?>" class="btn btn-primary px-4 py-2 fw-semibold">
          Kembali ke Dashboard Siswa
        </a>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

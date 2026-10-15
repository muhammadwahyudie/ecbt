<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<a href="<?= base_url('guru/koreksi/ujian/' . $ujian['id']) ?>" class="btn btn-outline-secondary btn-sm">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
  </svg>
  Kembali ke Daftar Peserta
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Banner Siswa -->
<div class="card mb-4 border-0" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
  <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <span class="badge bg-accent-light text-accent mb-2"><?= esc($ujian['nama']) ?></span>
      <h3 class="fw-bold mb-1" style="font-family: 'Sora', sans-serif;"><?= esc($siswa['nama']) ?></h3>
      <p class="mb-0 text-white-50">Username: <?= esc($siswa['username']) ?> &nbsp;&middot;&nbsp; Skor Objektif: <strong><?= $hasil['skor_objektif'] ?? '0.00' ?></strong></p>
    </div>
    <div class="text-md-end">
      <div class="small text-white-50">Nilai Akhir Saat Ini</div>
      <div class="fs-2 fw-bold text-warning tabular-nums font-monospace"><?= round($hasil['nilai_akhir'] ?? 0, 1) ?></div>
    </div>
  </div>
</div>

<form action="<?= base_url('guru/koreksi/simpan') ?>" method="POST">
  <?= csrf_field() ?>
  <input type="hidden" name="peserta_id" value="<?= $peserta['id'] ?>">

  <div class="d-flex flex-column gap-4 mb-4">
    <?php foreach ($soalUraian as $idx => $su): ?>
      <div class="card shadow-sm border" style="border-radius: 14px;">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <span class="fw-bold text-dark fs-6">Butir Uraian #<?= $su['urutan'] ?></span>
          <span class="badge bg-secondary">Bobot Maksimal: <?= $su['bobot'] ?></span>
        </div>
        <div class="card-body p-4">
          <!-- Pertanyaan -->
          <div class="mb-3">
            <label class="small text-muted fw-bold text-uppercase">Pertanyaan:</label>
            <div class="fs-6 text-dark mt-1"><?= $su['pertanyaan'] ?></div>
          </div>

          <!-- Pedoman Rubrik -->
          <?php if (!empty($su['pedoman'])): ?>
            <div class="p-3 bg-light rounded border mb-3 small">
              <strong class="text-secondary">Rubrik / Pedoman Penilaian:</strong><br>
              <span class="text-muted"><?= nl2br(esc($su['pedoman'])) ?></span>
            </div>
          <?php endif; ?>

          <!-- Jawaban Siswa -->
          <div class="mb-4">
            <label class="small text-muted fw-bold text-uppercase">Jawaban Siswa:</label>
            <div class="p-3 rounded border mt-1" style="background-color: #F8FAFC; min-height: 80px;">
              <?php if (empty($su['jawaban'])): ?>
                <em class="text-muted">(Siswa tidak memberikan jawaban untuk butir ini)</em>
              <?php else: ?>
                <div class="text-dark fs-6" style="white-space: pre-wrap;"><?= esc($su['jawaban']) ?></div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Input Skor Guru -->
          <div class="row align-items-center bg-light p-3 rounded border">
            <div class="col-12 col-md-8">
              <label class="form-label fw-semibold mb-0">Input Skor Nilai Guru (0 s.d. <?= $su['bobot'] ?>):</label>
              <div class="small text-muted">Berikan skor sesuai dengan ketepatan jawaban terhadap rubrik.</div>
            </div>
            <div class="col-12 col-md-4">
              <div class="input-group">
                <input type="number" step="0.25" min="0" max="<?= $su['bobot'] ?>" 
                       name="skor[<?= $su['id'] ?>]" class="form-control form-control-lg fw-bold text-end" 
                       value="<?= esc($su['skor'] ?? 0) ?>" required>
                <span class="input-group-text font-monospace">/ <?= $su['bobot'] ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="card p-3 mb-5">
    <div class="d-flex justify-content-between align-items-center">
      <a href="<?= base_url('guru/koreksi/ujian/' . $ujian['id']) ?>" class="btn btn-light px-4">Batal</a>
      <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
        Simpan Nilai Koreksi
      </button>
    </div>
  </div>
</form>
<?= $this->endSection() ?>

<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<a href="<?= base_url('guru/paket-soal') ?>" class="btn btn-outline-secondary btn-sm">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
  </svg>
  Kembali ke Bank Soal
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Buat Paket Soal Baru</h5>
        <small class="text-secondary">Tentukan nama, mata pelajaran, dan sasaran tingkat kelas</small>
      </div>
      <div class="card-body">
        <form action="<?= base_url('guru/paket-soal/simpan') ?>" method="POST">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Paket Soal <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Penilaian Harian Bab 1 - Aljabar" autofocus>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
            <select name="mapel_id" class="form-select" required>
              <option value="">-- Pilih Mata Pelajaran --</option>
              <?php foreach ($mapelList as $m): ?>
                <option value="<?= $m['id'] ?>"><?= esc($m['kode']) ?> - <?= esc($m['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Sasaran Tingkat Kelas</label>
            <select name="tingkat" class="form-select">
              <option value="">-- Semua Tingkat / Fleksibel --</option>
              <option value="10">Kelas 10 (SMA/SMK)</option>
              <option value="11">Kelas 11 (SMA/SMK)</option>
              <option value="12">Kelas 12 (SMA/SMK)</option>
              <option value="7">Kelas 7 (SMP)</option>
              <option value="8">Kelas 8 (SMP)</option>
              <option value="9">Kelas 9 (SMP)</option>
            </select>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('guru/paket-soal') ?>" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary px-4">Lanjut & Tambah Soal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-12 col-lg-10">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Konfigurasi Sekolah & Kebijakan Ujian</h5>
        <small class="text-secondary">Identitas lembaga, kop laporan hasil ujian, dan aturan keamanan sistem</small>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/pengaturan/simpan') ?>" method="POST" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Profil & Identitas Lembaga</h6>
          <div class="row g-3 mb-4">
            <div class="col-12 col-md-8">
              <label class="form-label fw-semibold">Nama Sekolah / Lembaga</label>
              <input type="text" name="nama_sekolah" class="form-control" 
                     value="<?= esc($settings['nama_sekolah'] ?? 'SMK / SMA Negeri Contoh') ?>" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label fw-semibold">Telepon Sekolah</label>
              <input type="text" name="telepon_sekolah" class="form-control" 
                     value="<?= esc($settings['telepon_sekolah'] ?? '021-12345678') ?>">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Email Resmi Sekolah</label>
              <input type="email" name="email_sekolah" class="form-control" 
                     value="<?= esc($settings['email_sekolah'] ?? 'admin@sekolah.sch.id') ?>">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Logo Sekolah</label>
              <input type="file" name="logo" class="form-control" accept="image/*">
              <small class="text-muted">Format: PNG, JPG, WebP. Maks 2 MB.</small>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Alamat Lengkap</label>
              <textarea name="alamat_sekolah" class="form-control" rows="2"><?= esc($settings['alamat_sekolah'] ?? 'Jl. Pendidikan No. 1, Jakarta') ?></textarea>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Nama Kepala Sekolah</label>
              <input type="text" name="kepala_sekolah" class="form-control" 
                     value="<?= esc($settings['kepala_sekolah'] ?? 'Drs. H. Ahmad Sudirman, M.Pd') ?>">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">NIP Kepala Sekolah</label>
              <input type="text" name="nip_kepala_sekolah" class="form-control" 
                     value="<?= esc($settings['nip_kepala_sekolah'] ?? '197501012000031001') ?>">
            </div>
          </div>

          <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Kebijakan Keamanan & Pelaksanaan Ujian</h6>
          <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
              <label class="form-label fw-semibold">Batas Login Gagal</label>
              <div class="input-group">
                <input type="number" name="batas_login_gagal" class="form-control" 
                       value="<?= esc($settings['batas_login_gagal'] ?? '5') ?>" min="3" max="10">
                <span class="input-group-text">kali</span>
              </div>
              <small class="text-muted">Akun dikunci 5 menit jika melebihi batas.</small>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label fw-semibold">Toleransi Keterlambatan</label>
              <div class="input-group">
                <input type="number" name="toleransi_terlambat_menit" class="form-control" 
                       value="<?= esc($settings['toleransi_terlambat_menit'] ?? '15') ?>" min="0" max="120">
                <span class="input-group-text">menit</span>
              </div>
              <small class="text-muted">Batas menit siswa boleh masuk setelah ujian mulai.</small>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label fw-semibold">Maksimal Pelanggaran</label>
              <div class="input-group">
                <input type="number" name="maks_pelanggaran_default" class="form-control" 
                       value="<?= esc($settings['maks_pelanggaran_default'] ?? '3') ?>" min="1" max="10">
                <span class="input-group-text">kali</span>
              </div>
              <small class="text-muted">Batas siswa keluar tab sebelum ujian dikunci otomatis.</small>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary px-4">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
              Simpan Pengaturan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

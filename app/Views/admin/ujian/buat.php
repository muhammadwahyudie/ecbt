<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<a href="<?= base_url('admin/ujian') ?>" class="btn btn-outline-secondary btn-sm">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
  </svg>
  Kembali ke Jadwal
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="<?= base_url('admin/ujian/simpan') ?>" method="POST">
  <?= csrf_field() ?>

  <div class="row g-4">
    <!-- Kolom Kiri: Pengaturan Utama -->
    <div class="col-12 col-lg-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">1. Identitas & Paket Ujian</h5>
          <small class="text-secondary">Pilih materi soal dan beri nama sesi ujian</small>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Sesi Ujian <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Penilaian Akhir Semester Ganjil 2026/2027">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Paket Soal <span class="text-danger">*</span></label>
            <select name="paket_id" class="form-select" required>
              <option value="">-- Pilih Paket Soal --</option>
              <?php foreach ($paketList as $p): ?>
                <option value="<?= $p['id'] ?>"><?= esc($p['nama']) ?> (Tingkat <?= esc($p['tingkat'] ?? '-') ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold">Pilih Rombongan Belajar / Kelas Peserta <span class="text-danger">*</span></label>
            <div class="row g-2 border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
              <?php foreach ($kelasList as $k): ?>
                <div class="col-6 col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="kelas_ids[]" value="<?= $k['id'] ?>" id="kelas_<?= $k['id'] ?>">
                    <label class="form-check-label" for="kelas_<?= $k['id'] ?>">
                      <?= esc($k['nama']) ?>
                    </label>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <small class="text-muted">Semua siswa di rombel yang dicentang otomatis terdaftar sebagai peserta ujian.</small>
          </div>
        </div>
      </div>

      <!-- Jadwal & Durasi -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">2. Waktu & Durasi Pengerjaan</h5>
          <small class="text-secondary">Atur rentang waktu pembukaan ujian dan alokasi waktu siswa</small>
        </div>
        <div class="card-body">
          <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Waktu Buka Ujian <span class="text-danger">*</span></label>
              <input type="datetime-local" name="mulai_at" class="form-control" required value="<?= date('Y-m-d\TH:i') ?>">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Waktu Tutup Ujian <span class="text-danger">*</span></label>
              <input type="datetime-local" name="selesai_at" class="form-control" required value="<?= date('Y-m-d\TH:i', time() + 7200) ?>">
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Durasi Pengerjaan Siswa <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" name="durasi_menit" class="form-control" value="90" min="5" max="360" required>
                <span class="input-group-text">Menit</span>
              </div>
              <small class="text-muted">Dihitung sejak siswa menekan tombol "Mulai Ujian".</small>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Batas Toleransi Keterlambatan</label>
              <div class="input-group">
                <input type="number" name="batas_terlambat" class="form-control" value="15" min="0" max="120">
                <span class="input-group-text">Menit</span>
              </div>
              <small class="text-muted">0 = bebas masuk selama ujian belum ditutup.</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kolom Kanan: Token & Keamanan -->
    <div class="col-12 col-lg-4">
      <!-- Token Ujian Card -->
      <div class="card mb-4 border-0" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
        <div class="card-header bg-transparent border-0 pb-0">
          <h5 class="card-title text-white mb-0">Token Akses Ujian</h5>
          <small class="text-white-50">Kunci masuk siswa ke layar pengerjaan</small>
        </div>
        <div class="card-body">
          <div class="text-center py-3">
            <input type="text" name="token" id="tokenField" 
                   class="form-control form-control-lg text-center fw-bold font-monospace bg-dark text-white border-secondary" 
                   value="<?= esc($token) ?>" maxlength="6" style="letter-spacing: 6px; font-size: 1.75rem;" required>
          </div>
          <button type="button" class="btn btn-outline-light btn-sm w-100" id="btnAcakToken">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
            Acak Ulang Token
          </button>
        </div>
      </div>

      <!-- Opsi Acak & Keamanan -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Kebijakan Ujian</h5>
        </div>
        <div class="card-body">
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="acak_soal" value="1" id="acakSoal" checked>
            <label class="form-check-label fw-semibold" for="acakSoal">Acak Urutan Soal</label>
            <div class="small text-muted">Setiap siswa mendapatkan urutan nomor yang berbeda.</div>
          </div>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="acak_opsi" value="1" id="acakOpsi" checked>
            <label class="form-check-label fw-semibold" for="acakOpsi">Acak Urutan Pilihan (Opsi)</label>
            <div class="small text-muted">Pilihan A, B, C, D diacak per peserta.</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tampilkan Hasil ke Siswa</label>
            <select name="tampil_hasil" class="form-select">
              <option value="tidak">Tidak Ditampilkan</option>
              <option value="langsung">Langsung Setelah Selesai</option>
              <option value="setelah_tutup">Setelah Jadwal Ditutup</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nilai KKM / Minimal</label>
            <input type="number" step="0.5" name="nilai_minimal" class="form-control" value="75.00">
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Batas Pelanggaran (Anti-Cheat)</label>
            <div class="input-group">
              <input type="number" name="batas_pelanggaran" class="form-control" value="3" min="1" max="10">
              <span class="input-group-text">kali</span>
            </div>
            <small class="text-muted">Siswa terkunci jika keluar tab melebih batas.</small>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg fw-bold">
              Jadwalkan Sekarang
            </button>
            <a href="<?= base_url('admin/ujian') ?>" class="btn btn-light">
              Batal
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('#btnAcakToken').on('click', function() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let token = '';
    for (let i = 0; i < 6; i++) {
      token += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    $('#tokenField').val(token);
  });
});
</script>
<?= $this->endSection() ?>

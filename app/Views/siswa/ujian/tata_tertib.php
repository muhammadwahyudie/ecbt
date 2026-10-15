<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-9 col-lg-8">
    <div class="card shadow-sm border-0" style="border-radius: 14px;">
      <div class="card-header border-0 pb-0 pt-4 px-4 text-center">
        <div class="sidebar-logo-icon mx-auto mb-3" style="width: 56px; height: 56px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="28" height="28">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
          </svg>
        </div>
        <h4 class="fw-bold mb-1" style="font-family: 'Sora', sans-serif;">Konfirmasi & Tata Tertib Ujian</h4>
        <p class="text-secondary small">Periksa informasi diri Anda dan baca tata tertib sebelum memulai pengerjaan.</p>
      </div>

      <div class="card-body p-4">
        <!-- Rincian Identitas Peserta & Ujian -->
        <div class="bg-light p-3 rounded-3 mb-4 border">
          <div class="row g-2 small">
            <div class="col-4 text-muted">Nama Peserta</div>
            <div class="col-8 fw-semibold text-dark"><?= esc(session()->get('nama')) ?></div>

            <div class="col-4 text-muted">Username / NISN</div>
            <div class="col-8 fw-semibold text-dark"><?= esc(session()->get('username')) ?></div>

            <div class="col-4 text-muted">Nama Ujian</div>
            <div class="col-8 fw-semibold text-primary"><?= esc($ujian['nama']) ?></div>

            <div class="col-4 text-muted">Durasi Waktu</div>
            <div class="col-8 fw-semibold text-dark"><?= esc($ujian['durasi_menit']) ?> Menit</div>
          </div>
        </div>

        <!-- Butir Tata Tertib -->
        <h6 class="fw-bold text-dark mb-3">Ketentuan & Tata Tertib:</h6>
        <ul class="text-secondary small mb-4 d-flex flex-column gap-2 ps-3">
          <li>Ujian wajib dikerjakan secara mandiri dan jujur tanpa bantuan pihak lain atau materi di luar ujian.</li>
          <li><strong>Dilarang membuka tab baru, browser lain, atau aplikasi apa pun</strong> selama ujian berlangsung.</li>
          <li>Sistem akan secara otomatis <strong>mencatat setiap pelanggaran</strong> perpindahan tab atau keluar dari mode layar penuh (fullscreen).</li>
          <li>Jika pelanggaran mencapai batas maksimal (<strong><?= esc($ujian['batas_pelanggaran']) ?> kali</strong>), ujian Anda akan <strong>terkunci secara otomatis</strong> dan hanya dapat dibuka kembali oleh proktor ruang.</li>
          <li>Setiap jawaban Anda tersimpan otomatis (autosave) secara berkala ke server. Jika koneksi terputus, segera hubungi pengawas dan Anda dapat melanjutkan dari soal terakhir.</li>
          <li>Waktu pengerjaan akan terus berjalan dan ujian akan otomatis diselesaikan ketika waktu habis.</li>
        </ul>

        <!-- Form Konfirmasi Mulai -->
        <form action="<?= base_url('siswa/ujian/mulai/' . $ujian['id']) ?>" method="POST" id="formTataTertib">
          <?= csrf_field() ?>

          <div class="form-check mb-4 p-3 bg-light rounded border">
            <input class="form-check-input ms-0 me-2" type="checkbox" id="checkSetuju" required>
            <label class="form-check-label fw-semibold text-dark small" for="checkSetuju">
              Saya telah membaca, memahami, dan menyetujui seluruh tata tertib ujian di atas serta bersedia menanggung konsekuensinya jika melanggar.
            </label>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a href="<?= base_url('siswa/dashboard') ?>" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" id="btnMulaiKerja" disabled>
              Mulai Mengerjakan Ujian
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="ms-1">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
              </svg>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('#checkSetuju').on('change', function() {
    $('#btnMulaiKerja').prop('disabled', !this.checked);
  });
});
</script>
<?= $this->endSection() ?>

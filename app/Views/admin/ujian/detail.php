<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<div class="d-flex gap-2">
  <a href="<?= base_url('admin/ujian') ?>" class="btn btn-outline-secondary btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Kembali
  </a>
  <a href="<?= base_url('proktor/monitoring/' . $ujian['id']) ?>" class="btn btn-primary btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    Live Monitoring
  </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">
  <!-- Kolom Kiri: Info Ujian & Peserta -->
  <div class="col-12 col-lg-8">
    <!-- Banner Ujian -->
    <div class="card mb-4 border-0" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
      <div class="card-body p-4">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="badge bg-accent-light text-accent"><?= esc($ujian['nama_mapel'] ?? '-') ?></span>
          <?php
            $bCls = match($ujian['status']) {
              'berlangsung' => 'bg-success',
              'terjadwal'   => 'bg-info',
              'selesai'     => 'bg-secondary',
              'draf'        => 'bg-warning',
              default       => 'bg-light text-dark'
            };
          ?>
          <span class="badge <?= $bCls ?>"><?= ucfirst(esc($ujian['status'])) ?></span>
        </div>
        <h3 class="fw-bold mb-1" style="font-family: 'Sora', sans-serif;"><?= esc($ujian['nama']) ?></h3>
        <p class="mb-0 text-white-50">Paket Soal: <strong><?= esc($ujian['nama_paket']) ?></strong> &nbsp;&middot;&nbsp; Dibuat oleh: <?= esc($ujian['nama_pembuat'] ?? '-') ?></p>
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

    <!-- Rincian Parameter Ujian -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Parameter & Kebijakan Ujian</h5>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditUjian">
          Ubah Parameter
        </button>
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
            <div class="small text-muted">Alokasi Durasi</div>
            <div class="fw-semibold text-dark tabular-nums"><?= esc($ujian['durasi_menit']) ?> Menit</div>
          </div>
          <div class="col-6 col-md-4">
            <div class="small text-muted">Acak Soal & Opsi</div>
            <div class="fw-semibold text-dark">
              Soal: <?= $ujian['acak_soal'] ? 'Ya' : 'Tidak' ?>, Opsi: <?= $ujian['acak_opsi'] ? 'Ya' : 'Tidak' ?>
            </div>
          </div>
          <div class="col-6 col-md-4">
            <div class="small text-muted">Tampilkan Nilai</div>
            <div class="fw-semibold text-dark text-capitalize"><?= esc($ujian['tampil_hasil']) ?></div>
          </div>
          <div class="col-6 col-md-4">
            <div class="small text-muted">Maks. Pelanggaran</div>
            <div class="fw-semibold text-dark"><?= esc($ujian['batas_pelanggaran']) ?> Kali</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kelas Peserta -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Rombongan Belajar (Kelas) Peserta</h5>
      </div>
      <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($kelasList as $k): ?>
            <span class="badge bg-light text-dark border fs-6 px-3 py-2">
              <?= esc($k['nama']) ?> (Tingkat <?= esc($k['tingkat']) ?>)
            </span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Kartu Token Besar -->
  <div class="col-12 col-lg-4">
    <div class="card text-center p-4 shadow-sm" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
      <div class="text-white-50 text-uppercase letter-spacing-1 small mb-1">Token Akses Ujian</div>
      <div class="fs-1 fw-bold font-monospace text-warning py-3" style="letter-spacing: 6px;">
        <?= esc($ujian['token'] ?? '------') ?>
      </div>
      <div class="small text-white-50 mb-3">
        Masa Berlaku: <?= $ujian['token_berlaku_sampai'] ? date('H:i', strtotime($ujian['token_berlaku_sampai'])) . ' WIB' : 'Selama Ujian' ?>
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-light btn-sm flex-grow-1 btn-salin-token" data-token="<?= esc($ujian['token']) ?>">
          Salin Token
        </button>
        <form action="<?= base_url('admin/ujian/token/' . $ujian['id']) ?>" method="POST" class="d-inline flex-grow-1">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">
            Buat Ulang Token
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ubah Ujian -->
<div class="modal fade" id="modalEditUjian" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Ubah Parameter Ujian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/ujian/update/' . $ujian['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Ujian <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" required value="<?= esc($ujian['nama']) ?>">
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Waktu Buka <span class="text-danger">*</span></label>
              <input type="datetime-local" name="mulai_at" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime($ujian['mulai_at'])) ?>">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Waktu Tutup <span class="text-danger">*</span></label>
              <input type="datetime-local" name="selesai_at" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime($ujian['selesai_at'])) ?>">
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Durasi (Menit)</label>
              <input type="number" name="durasi_menit" class="form-control" required value="<?= esc($ujian['durasi_menit']) ?>">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Batas Pelanggaran</label>
              <input type="number" name="batas_pelanggaran" class="form-control" required value="<?= esc($ujian['batas_pelanggaran']) ?>">
            </div>
          </div>
          <div class="row g-3">
            <div class="col-6">
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="acak_soal" value="1" id="editAcakSoal" <?= $ujian['acak_soal'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="editAcakSoal">Acak Soal</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="acak_opsi" value="1" id="editAcakOpsi" <?= $ujian['acak_opsi'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="editAcakOpsi">Acak Opsi</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
        </div>
      </form>
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

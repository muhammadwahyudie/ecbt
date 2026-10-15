<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- ─── Banner Sambutan Siswa ────────────────────────────────────── -->
<div class="card mb-4 border-0" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
  <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h4 class="fw-bold mb-1" style="font-family: 'Sora', sans-serif;">Selamat Datang, <?= esc(session()->get('nama')) ?>! 👋</h4>
      <p class="mb-0 text-white-50 fs-6">Periksa jadwal ujian Anda hari ini, pastikan koneksi stabil dan siapkan token ujian dari pengawas.</p>
    </div>
    <div>
      <button type="button" class="btn btn-accent px-4 py-2" data-bs-toggle="modal" data-bs-target="#tokenModal">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
        </svg>
        Masukkan Token Ujian
      </button>
    </div>
  </div>
</div>

<!-- ─── Ujian Hari Ini ────────────────────────────────────────────── -->
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="card-title mb-0">Ujian Hari Ini</h5>
      <small class="text-secondary">Ujian yang dapat Anda ikuti pada hari ini</small>
    </div>
    <span class="badge bg-primary"><?= count($ujianHariIni) ?> Ujian</span>
  </div>
  <div class="card-body p-0">
    <?php if (empty($ujianHariIni)): ?>
      <div class="text-center py-5 text-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="48" height="48" class="mb-2 text-muted">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="mb-0">Tidak ada jadwal ujian untuk Anda hari ini. Selamat beristirahat atau belajar materi berikutnya!</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Mata Pelajaran & Ujian</th>
              <th>Waktu Pelaksanaan</th>
              <th>Durasi</th>
              <th>Status Anda</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ujianHariIni as $u): ?>
              <?php
                $now = date('Y-m-d H:i:s');
                $isStarted = ($now >= $u['mulai_at']);
                $isEnded = ($now > $u['selesai_at']);
                $pesertaStatus = $u['status_peserta'] ?? 'belum_login';
              ?>
              <tr>
                <td>
                  <div class="fw-bold text-dark"><?= esc($u['nama_mapel'] ?? 'Mata Pelajaran') ?></div>
                  <small class="text-secondary"><?= esc($u['nama']) ?></small>
                </td>
                <td class="tabular-nums">
                  <?= date('H:i', strtotime($u['mulai_at'])) ?> - <?= date('H:i', strtotime($u['selesai_at'])) ?> WIB
                </td>
                <td class="tabular-nums"><?= esc($u['durasi_menit']) ?> Menit</td>
                <td>
                  <?php if ($pesertaStatus === 'selesai'): ?>
                    <span class="badge bg-success">Sudah Selesai</span>
                  <?php elseif ($pesertaStatus === 'mengerjakan'): ?>
                    <span class="badge bg-warning">Sedang Mengerjakan</span>
                  <?php elseif ($isEnded): ?>
                    <span class="badge bg-secondary">Waktu Berakhir</span>
                  <?php elseif ($isStarted): ?>
                    <span class="badge bg-info">Siap Dimulai</span>
                  <?php else: ?>
                    <span class="badge bg-light text-dark border">Belum Mulai</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($pesertaStatus === 'selesai'): ?>
                    <button class="btn btn-sm btn-outline-secondary" disabled>Telah Selesai</button>
                  <?php elseif ($pesertaStatus === 'mengerjakan'): ?>
                    <a href="<?= base_url('siswa/ujian/kerjakan/' . $u['id']) ?>" class="btn btn-sm btn-warning fw-semibold">
                      Lanjutkan Ujian
                    </a>
                  <?php elseif ($isStarted && !$isEnded): ?>
                    <button type="button" class="btn btn-sm btn-primary fw-semibold btn-mulai-ujian" 
                            data-ujian-id="<?= $u['id'] ?>" 
                            data-ujian-nama="<?= esc($u['nama']) ?>"
                            data-bs-toggle="modal" 
                            data-bs-target="#tokenModal">
                      Mulai Ujian
                    </button>
                  <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary" disabled>Belum Waktunya</button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ─── 2 Kolom: Ujian Mendatang & Riwayat Ujian ────────────────────── -->
<div class="row g-4">
  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Ujian Mendatang</h5>
        <small class="text-secondary">Jadwal ujian yang akan datang</small>
      </div>
      <div class="card-body p-0">
        <?php if (empty($ujianMendatang)): ?>
          <div class="text-center py-4 text-secondary">
            <p class="mb-0">Tidak ada jadwal ujian mendatang.</p>
          </div>
        <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($ujianMendatang as $um): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                <div>
                  <div class="fw-semibold text-dark"><?= esc($um['nama_mapel']) ?> - <?= esc($um['nama']) ?></div>
                  <small class="text-secondary"><?= date('d M Y, H:i', strtotime($um['mulai_at'])) ?> WIB (<?= esc($um['durasi_menit']) ?> mnt)</small>
                </div>
                <span class="badge bg-light text-secondary border">Terjadwal</span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Riwayat Ujian Selesai</h5>
        <small class="text-secondary">Daftar ujian yang telah Anda selesaikan</small>
      </div>
      <div class="card-body p-0">
        <?php if (empty($ujianSelesai)): ?>
          <div class="text-center py-4 text-secondary">
            <p class="mb-0">Belum ada riwayat ujian yang selesai.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Ujian</th>
                  <th>Tanggal Selesai</th>
                  <th class="text-end">Nilai</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ujianSelesai as $us): ?>
                  <tr>
                    <td>
                      <div class="fw-semibold text-dark"><?= esc($us['nama_mapel']) ?></div>
                      <small class="text-secondary"><?= esc($us['nama']) ?></small>
                    </td>
                    <td class="tabular-nums text-secondary small">
                      <?= $us['selesai_at'] ? date('d/m/Y H:i', strtotime($us['selesai_at'])) : '-' ?>
                    </td>
                    <td class="text-end">
                      <?php if (isset($us['nilai_akhir'])): ?>
                        <span class="badge bg-success fs-6 tabular-nums"><?= round($us['nilai_akhir'], 1) ?></span>
                      <?php else: ?>
                        <span class="badge bg-secondary">Menunggu Koreksi</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ─── Modal Masukkan Token ──────────────────────────────────────── -->
<div class="modal fade" id="tokenModal" tabindex="-1" aria-labelledby="tokenModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px; border: 1px solid #E1E8EB;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="tokenModalLabel">Konfirmasi Masuk Ujian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('siswa/ujian/konfirmasi') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="ujian_id" id="modalUjianId" value="">
        <div class="modal-body py-3">
          <p class="text-secondary small mb-3">Masukkan 6 karakter token ujian yang telah diumumkan atau diberikan oleh pengawas/proktor ujian.</p>
          <div class="mb-3">
            <label for="tokenInput" class="form-label fw-semibold">Token Ujian</label>
            <input type="text" class="form-control form-control-lg text-center fw-bold font-monospace" 
                   id="tokenInput" name="token" maxlength="6" placeholder="Contoh: AB12CD" 
                   style="letter-spacing: 4px; text-transform: uppercase;" required autofocus>
          </div>
          <div class="alert alert-info py-2 small mb-0">
            <strong>Catatan:</strong> Pastikan Anda telah membaca dan memahami tata tertib pengerjaan ujian berbasis komputer sebelum memulai.
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Lanjut ke Ujian</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    $('.btn-mulai-ujian').on('click', function() {
      const ujianId = $(this).data('ujian-id');
      $('#modalUjianId').val(ujianId);
    });

    // Auto uppercase on token input
    $('#tokenInput').on('input', function() {
      this.value = this.value.toUpperCase();
    });
  });
</script>
<?= $this->endSection() ?>

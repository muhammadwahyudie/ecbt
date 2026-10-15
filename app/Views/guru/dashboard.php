<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- ─── 4 Kartu Metrik Ringkasan ─────────────────────────────────── -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-info-light text-info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= number_format($paketSoal) ?></div>
        <div class="card-metric-label">Paket Soal Saya</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-success-light text-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= number_format($ujianAktif) ?></div>
        <div class="card-metric-label">Ujian Terjadwal/Aktif</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-warning-light text-warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= number_format($menungguKoreksi) ?></div>
        <div class="card-metric-label">Jawaban Belum Dikoreksi</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-accent-light text-accent">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= $rataRataNilai ?></div>
        <div class="card-metric-label">Rata-rata Nilai Siswa</div>
      </div>
    </div>
  </div>
</div>

<!-- ─── Daftar Ujian Milik Guru ───────────────────────────────────── -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="card-title mb-0">Ujian yang Dikelola</h5>
      <small class="text-secondary">Daftar riwayat dan jadwal ujian berdasarkan paket soal Anda</small>
    </div>
    <a href="<?= base_url('guru/ujian') ?>" class="btn btn-outline-secondary btn-sm">Lihat Semua</a>
  </div>
  <div class="card-body p-0">
    <?php if (empty($ujianSaya)): ?>
      <div class="text-center py-5 text-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="48" height="48" class="mb-2 text-muted">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
        </svg>
        <p class="mb-0">Anda belum membuat ujian apa pun.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama Ujian</th>
              <th>Paket Soal</th>
              <th>Jadwal Pelaksanaan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ujianSaya as $u): ?>
            <tr>
              <td class="fw-semibold text-dark"><?= esc($u['nama']) ?></td>
              <td><?= esc($u['nama_paket']) ?></td>
              <td class="tabular-nums"><?= date('d M Y, H:i', strtotime($u['mulai_at'])) ?></td>
              <td>
                <?php
                  $badgeClass = match($u['status']) {
                    'berlangsung' => 'bg-success',
                    'terjadwal'   => 'bg-info',
                    'selesai'     => 'bg-secondary',
                    'draf'        => 'bg-warning',
                    default       => 'bg-light text-dark'
                  };
                ?>
                <span class="badge <?= $badgeClass ?>"><?= ucfirst(esc($u['status'])) ?></span>
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <a href="<?= base_url('guru/koreksi/' . $u['id']) ?>" class="btn btn-outline-primary" title="Koreksi">
                    Koreksi
                  </a>
                  <a href="<?= base_url('guru/rekap/' . $u['id']) ?>" class="btn btn-outline-secondary" title="Rekap Nilai">
                    Rekap
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>

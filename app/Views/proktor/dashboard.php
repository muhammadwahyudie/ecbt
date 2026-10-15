<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- ─── 4 Kartu Metrik Ringkasan ─────────────────────────────────── -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-info-light text-info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= number_format($pesertaOnline) ?></div>
        <div class="card-metric-label">Peserta Mengerjakan</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-warning-light text-warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= number_format($belumLogin) ?></div>
        <div class="card-metric-label">Belum Masuk Ujian</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-danger-light text-danger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= number_format($pelanggaran) ?></div>
        <div class="card-metric-label">Indikasi Pelanggaran</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-success-light text-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= number_format($ujianBerlangsung) ?></div>
        <div class="card-metric-label">Ujian Aktif</div>
      </div>
    </div>
  </div>
</div>

<!-- ─── Tabel Ujian Aktif Dipantau ────────────────────────────────── -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="card-title mb-0">Ujian yang Sedang Berlangsung</h5>
      <small class="text-secondary">Pantau aktivitas, token, dan kondisi peserta secara real-time</small>
    </div>
  </div>
  <div class="card-body p-0">
    <?php if (empty($ujianAktif)): ?>
      <div class="text-center py-5 text-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="48" height="48" class="mb-2 text-muted">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="mb-0">Saat ini tidak ada sesi ujian yang sedang berjalan.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama Ujian</th>
              <th>Token Ujian</th>
              <th>Waktu Mulai</th>
              <th>Durasi</th>
              <th>Aksi Monitoring</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ujianAktif as $u): ?>
            <tr>
              <td class="fw-semibold text-dark"><?= esc($u['nama']) ?></td>
              <td>
                <span class="badge bg-dark fs-6 px-3 py-2 font-monospace letter-spacing-1">
                  <?= esc($u['token'] ?? '------') ?>
                </span>
              </td>
              <td class="tabular-nums"><?= date('d/m/Y H:i', strtotime($u['mulai_at'])) ?></td>
              <td class="tabular-nums"><?= esc($u['durasi_menit']) ?> Menit</td>
              <td>
                <a href="<?= base_url('proktor/monitoring/' . $u['id']) ?>" class="btn btn-primary btn-sm">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  Buka Pengawasan
                </a>
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

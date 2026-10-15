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
        <div class="card-metric-label">Peserta Online</div>
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
        <div class="card-metric-value"><?= number_format($ujianBerlangsung) ?></div>
        <div class="card-metric-label">Ujian Berlangsung</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-warning-light text-warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= number_format($paketSoal) ?></div>
        <div class="card-metric-label">Paket Soal Aktif</div>
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
        <div class="card-metric-label">Rata-rata Nilai Sekolah</div>
      </div>
    </div>
  </div>
</div>

<!-- ─── Tabel Ujian Hari Ini & Rata-rata Mapel ──────────────────────── -->
<div class="row g-4">
  <div class="col-12 col-lg-7">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0">Jadwal Ujian Hari Ini</h5>
          <small class="text-secondary"><?= count($ujianHariIni) ?> sesi ujian terjadwal hari ini</small>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="<?= base_url('admin/ujian') ?>" class="btn btn-outline-secondary btn-sm">Lihat Semua</a>
          <a href="<?= base_url('admin/ujian/buat') ?>" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Jadwalkan Ujian
          </a>
        </div>
      </div>
      <div class="card-body p-0">
        <?php if (empty($ujianHariIni)): ?>
          <div class="text-center py-5 text-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="48" height="48" class="mb-2 text-muted">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            <p class="mb-0">Tidak ada jadwal ujian untuk hari ini.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Nama Ujian</th>
                  <th>Jam</th>
                  <th>Durasi</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ujianHariIni as $u): ?>
                <tr>
                  <td class="fw-semibold text-dark"><?= esc($u['nama']) ?></td>
                  <td class="tabular-nums"><?= date('H:i', strtotime($u['mulai_at'])) ?> - <?= date('H:i', strtotime($u['selesai_at'])) ?></td>
                  <td class="tabular-nums"><?= esc($u['durasi_menit']) ?> mnt</td>
                  <td>
                    <?php if ($u['status'] === 'berlangsung'): ?>
                      <span class="badge bg-success">Berlangsung</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Terjadwal</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="<?= base_url('proktor/monitoring/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary" title="Monitoring">
                      Monitor
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
  </div>

  <div class="col-12 col-lg-5">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Rata-rata Nilai per Mapel</h5>
        <small class="text-secondary">Statistik hasil ujian selesai</small>
      </div>
      <div class="card-body">
        <?php if (empty($nilaiPerMapel)): ?>
          <div class="text-center py-5 text-secondary">
            <p class="mb-0">Belum ada data nilai ujian yang tersimpan.</p>
          </div>
        <?php else: ?>
          <div class="d-flex flex-column gap-3">
            <?php foreach ($nilaiPerMapel as $m): ?>
              <?php 
                $rata = round($m['rata'] ?? 0, 1);
                $percent = min(100, max(0, $rata));
                $barColor = $rata >= 75 ? 'bg-success' : ($rata >= 60 ? 'bg-primary' : 'bg-warning');
              ?>
              <div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="fw-semibold text-dark"><?= esc($m['mapel']) ?></span>
                  <span class="fw-bold tabular-nums <?= $rata >= 75 ? 'text-success' : ($rata >= 60 ? 'text-primary' : 'text-warning') ?>">
                    <?= $rata ?>
                  </span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px; background-color: #E8EEF0;">
                  <div class="progress-bar <?= $barColor ?>" role="progressbar" style="width: <?= $percent ?>%; border-radius: 4px;"></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <div>
      <h5 class="card-title mb-0">Riwayat Hasil & Nilai Ujian Anda</h5>
      <small class="text-secondary">Daftar perolehan nilai dari ujian yang telah Anda selesaikan</small>
    </div>
  </div>
  <div class="card-body p-0">
    <?php if (empty($riwayatNilai)): ?>
      <div class="text-center py-5 text-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="48" height="48" class="mb-2 text-muted">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
        </svg>
        <p class="mb-0">Belum ada riwayat hasil ujian yang tersimpan.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Mata Pelajaran & Ujian</th>
              <th>Waktu Selesai</th>
              <th>KKM</th>
              <th>Nilai Akhir</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($riwayatNilai as $rn): ?>
              <?php
                $na = (float)($rn['nilai_akhir'] ?? 0);
                $kkm = (float)($rn['nilai_minimal'] ?? 75);
                $isTuntas = ($na >= $kkm);
                $tampil = ($rn['tampil_hasil'] === 'langsung' || ($rn['tampil_hasil'] === 'setelah_tutup' && strtotime($rn['selesai_at']) < time()));
              ?>
              <tr>
                <td>
                  <div class="fw-bold text-dark"><?= esc($rn['nama_mapel']) ?></div>
                  <small class="text-secondary"><?= esc($rn['nama']) ?></small>
                </td>
                <td class="tabular-nums text-secondary small">
                  <?= $rn['selesai_at'] ? date('d/m/Y H:i', strtotime($rn['selesai_at'])) : '-' ?>
                </td>
                <td class="tabular-nums"><?= esc($rn['nilai_minimal']) ?></td>
                <td>
                  <?php if ($tampil): ?>
                    <span class="badge bg-dark fs-6 px-3 py-1 font-monospace"><?= round($na, 1) ?></span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Menunggu Pengumuman</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!$tampil): ?>
                    <span class="text-muted small">-</span>
                  <?php elseif ($isTuntas): ?>
                    <span class="badge bg-success-light text-success fw-bold">✓ Tuntas</span>
                  <?php else: ?>
                    <span class="badge bg-danger-light text-danger fw-bold">Belum Tuntas</span>
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
<?= $this->endSection() ?>

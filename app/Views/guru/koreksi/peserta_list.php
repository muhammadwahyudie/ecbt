<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<a href="<?= base_url('guru/koreksi') ?>" class="btn btn-outline-secondary btn-sm">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
  </svg>
  Kembali ke Antrean
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="card-title mb-0">Peserta Ujian: <?= esc($ujian['nama']) ?></h5>
      <small class="text-secondary">Pilih lembar siswa untuk menginput nilai butir soal uraian</small>
    </div>
  </div>
  <div class="card-body p-0">
    <?php if (empty($pesertaList)): ?>
      <div class="text-center py-5 text-secondary">
        <p class="mb-0">Belum ada siswa yang menyelesaikan ujian ini.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama Siswa</th>
              <th>Kelas</th>
              <th>Skor Objektif</th>
              <th>Skor Uraian</th>
              <th>Nilai Akhir</th>
              <th>Status Koreksi</th>
              <th class="text-end" style="width: 70px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pesertaList as $p): ?>
              <tr>
                <td>
                  <div class="fw-bold text-dark"><?= esc($p['nama']) ?></div>
                  <small class="text-muted"><?= esc($p['username']) ?> <?= $p['nis'] ? '· NIS ' . esc($p['nis']) : '' ?></small>
                </td>
                <td>
                  <span class="badge bg-light text-dark border"><?= esc($p['nama_kelas'] ?? '-') ?></span>
                </td>
                <td class="tabular-nums"><?= $p['skor_objektif'] ?? '0.00' ?></td>
                <td class="tabular-nums"><?= $p['skor_uraian'] ?? '0.00' ?></td>
                <td class="tabular-nums">
                  <span class="badge bg-dark fs-6 px-3 py-1"><?= round($p['nilai_akhir'] ?? 0, 1) ?></span>
                </td>
                <td>
                  <?php if ($p['status_koreksi'] === 'selesai'): ?>
                    <span class="badge bg-success-light text-success fw-bold">✓ Sudah Dinilai</span>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark fw-bold">Belum Dikoreksi</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <div class="btn-action-group">
                    <a href="<?= base_url('guru/koreksi/peserta/' . $p['id']) ?>" class="btn-action btn-action-edit" title="Koreksi Jawaban Peserta">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                      </svg>
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

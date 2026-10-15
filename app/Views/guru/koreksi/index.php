<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <div>
      <h5 class="card-title mb-0">Antrean Koreksi Soal Uraian</h5>
      <small class="text-secondary">Daftar ujian yang memiliki butir soal esai/uraian untuk dinilai secara manual</small>
    </div>
  </div>
  <div class="card-body p-0">
    <?php if (empty($ujianList)): ?>
      <div class="text-center py-5 text-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="48" height="48" class="mb-2 text-muted">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="mb-0">Tidak ada antrean koreksi uraian untuk Anda saat ini.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama Ujian & Paket</th>
              <th>Mata Pelajaran</th>
              <th>Jadwal Selesai</th>
              <th>Peserta Selesai</th>
              <th>Belum Dikoreksi</th>
              <th class="text-end" style="width: 70px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ujianList as $u): ?>
              <tr>
                <td>
                  <div class="fw-bold text-dark"><?= esc($u['nama']) ?></div>
                  <small class="text-muted">Paket: <?= esc($u['nama_paket']) ?></small>
                </td>
                <td>
                  <span class="badge bg-light text-dark border"><?= esc($u['nama_mapel']) ?></span>
                </td>
                <td class="tabular-nums text-secondary small">
                  <?= date('d/m/Y H:i', strtotime($u['selesai_at'])) ?>
                </td>
                <td class="tabular-nums">
                  <span class="badge bg-light text-dark border"><?= esc($u['total_peserta']) ?> Peserta</span>
                </td>
                <td>
                  <?php if ($u['belum_koreksi'] > 0): ?>
                    <span class="badge bg-warning text-dark px-3 py-1 fw-bold"><?= $u['belum_koreksi'] ?> Peserta Menunggu</span>
                  <?php else: ?>
                    <span class="badge bg-success-light text-success fw-bold">Semua Selesai</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <div class="btn-action-group">
                    <a href="<?= base_url('guru/koreksi/ujian/' . $u['id']) ?>" class="btn-action btn-action-edit" title="Buka Antrean Koreksi">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.155.095 2 1.059 2 2.194V7.5M9 14.25l2.25 2.25L15 12M6 8.25h12a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 19.5v-9A2.25 2.25 0 016 8.25z"/>
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

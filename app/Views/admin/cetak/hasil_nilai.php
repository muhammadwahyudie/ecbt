<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Cetak Hasil Nilai</h5>
    <small class="text-secondary">Pilih ujian untuk mencetak hasil nilai peserta</small>
  </div>
  <div class="card-body">
    <?php if (empty($ujianList)): ?>
      <p class="text-muted text-center py-4">Belum ada data ujian.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle" id="tblNilai">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nama Ujian</th>
            <th>Mata Pelajaran</th>
            <th>Waktu Pelaksanaan</th>
            <th>Status</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ujianList as $i => $u): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td class="fw-semibold"><?= esc($u['nama']) ?></td>
            <td><?= esc($u['nama_mapel'] ?? '-') ?></td>
            <td>
              <?= $u['mulai_at'] ? date('d M Y H:i', strtotime($u['mulai_at'])) : '-' ?>
              <?= $u['selesai_at'] ? ' s/d ' . date('H:i', strtotime($u['selesai_at'])) : '' ?>
            </td>
            <td>
              <?php
              $cls = ['berlangsung' => 'bg-success', 'selesai' => 'bg-secondary', 'menunggu' => 'bg-warning text-dark', 'dibatalkan' => 'bg-danger'];
              ?>
              <span class="badge <?= $cls[$u['status']] ?? 'bg-light text-dark' ?>"><?= esc($u['status']) ?></span>
            </td>
            <td class="text-end">
              <a href="<?= base_url('admin/cetak/hasil-nilai/' . $u['id']) ?>" target="_blank" class="btn btn-sm btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14" class="me-1">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
                Cetak
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

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('#tblNilai').DataTable({
    language: { search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data', info: 'Menampilkan _START_–_END_ dari _TOTAL_', paginate: { next: '›', previous: '‹' } }
  });
});
</script>
<?= $this->endSection() ?>

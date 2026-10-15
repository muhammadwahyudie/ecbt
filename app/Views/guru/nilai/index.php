<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <div>
      <h5 class="card-title mb-0">Rekapitulasi Nilai Ujian</h5>
      <small class="text-secondary">Pilih sesi ujian untuk melihat rekapitulasi nilai, analisis butir soal, dan ekspor data</small>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableRekapNilai">
        <thead class="table-light">
          <tr>
            <th>Nama Ujian & Paket</th>
            <th>Mata Pelajaran</th>
            <th>Jadwal Pelaksanaan</th>
            <th>Status</th>
            <th>Peserta Selesai</th>
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
                <span class="badge bg-light text-dark border"><?= esc($u['nama_mapel'] ?? '-') ?></span>
              </td>
              <td class="tabular-nums">
                <?= date('d M Y, H:i', strtotime($u['mulai_at'])) ?>
              </td>
              <td>
                <span class="badge bg-info text-capitalize"><?= esc($u['status']) ?></span>
              </td>
              <td>
                <span class="badge bg-light text-dark border"><?= esc($u['total_peserta']) ?> Peserta</span>
              </td>
              <td class="text-end">
                <div class="btn-action-group">
                  <a href="<?= base_url('guru/nilai/ujian/' . $u['id']) ?>" class="btn-action btn-action-view" title="Buka Rekap Nilai">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('#tableRekapNilai').DataTable({
    language: {
      search: "Cari ujian:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ ujian",
      paginate: { next: "›", previous: "‹" }
    }
  });
});
</script>
<?= $this->endSection() ?>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Monitoring Soal</h5>
      <small class="text-secondary">Pantau progres pembuatan soal oleh setiap guru</small>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableMonSoal">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nama Guru</th>
            <th>Username / NIP</th>
            <th class="text-center">Paket Soal</th>
            <th class="text-center">Total Soal</th>
            <th>Terakhir Buat</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('#tableMonSoal').DataTable({
    ajax: { url: '<?= base_url('admin/monitoring-soal/data') ?>' },
    columns: [
      { data: null, render: (d,t,r,m) => m.row+1, orderable: false },
      { data: 'nama', render: d => `<div class="fw-semibold">${d}</div>` },
      { data: 'username' },
      {
        data: 'jumlah_paket', className: 'text-center',
        render: d => `<span class="badge bg-light text-dark border px-3">${d} paket</span>`
      },
      {
        data: 'jumlah_soal', className: 'text-center',
        render: d => {
          const cls = d > 0 ? 'bg-success' : 'bg-secondary';
          return `<span class="badge ${cls} px-3">${d} soal</span>`;
        }
      },
      {
        data: 'terakhir_buat',
        render: d => d ? new Date(d).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) : '<span class="text-muted">Belum ada</span>'
      },
      {
        data: null, orderable: false, className: 'text-end',
        render: (d, t, row) => `<a href="<?= base_url('admin/bank-soal') ?>?guru=${row.id}" class="btn btn-sm btn-outline-primary">Lihat Soal</a>`
      }
    ],
    order: [[4, 'desc']],
    language: { search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data', info: 'Menampilkan _START_–_END_ dari _TOTAL_', paginate: { next: '›', previous: '‹' } }
  });
});
</script>
<?= $this->endSection() ?>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Token Ujian</h5>
    <small class="text-secondary">Lihat dan kelola token akses untuk setiap ujian</small>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableToken">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nama Ujian</th>
            <th>Mata Pelajaran</th>
            <th>Token</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Status</th>
            <th class="text-end" style="width:120px;">Aksi</th>
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
  const table = $('#tableToken').DataTable({
    ajax: { url: '<?= base_url('admin/token/data') ?>' },
    columns: [
      { data: null, render: (d,t,r,m) => m.row+1, orderable: false },
      { data: 'nama', render: d => `<div class="fw-semibold">${d}</div>` },
      { data: 'nama_mapel', render: d => d ? `<span class="badge bg-light text-dark border">${d}</span>` : '-' },
      {
        data: 'token',
        render: d => `<code class="fw-bold fs-6 text-primary" id="tok-${d}">${d ?? '-'}</code>`
      },
      { data: 'mulai_at', render: d => d ? new Date(d).toLocaleString('id-ID') : '-' },
      { data: 'selesai_at', render: d => d ? new Date(d).toLocaleString('id-ID') : '-' },
      {
        data: 'status',
        render: d => {
          const map = { berlangsung: 'bg-success', selesai: 'bg-secondary', menunggu: 'bg-warning text-dark', dibatalkan: 'bg-danger' };
          return `<span class="badge ${map[d] ?? 'bg-light text-dark'}">${d}</span>`;
        }
      },
      {
        data: null, orderable: false, className: 'text-end',
        render: (d, t, row) => `<button class="btn btn-sm btn-outline-primary btn-regen" data-id="${row.id}" data-token="${row.token}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
          Regenerasi
        </button>`
      }
    ],
    language: { search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data', info: 'Menampilkan _START_–_END_ dari _TOTAL_', paginate: { next: '›', previous: '‹' } }
  });

  $(document).on('click', '.btn-regen', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Regenerasi Token?',
      text: 'Token lama tidak akan berlaku lagi.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Regenerasi',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#0E7C86'
    }).then(r => {
      if (r.isConfirmed) {
        $.post(`<?= base_url('admin/token/regenerate') ?>/${id}`, { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }, function(res) {
          if (res.success) {
            Swal.fire('Berhasil!', `Token baru: <strong>${res.token}</strong>`, 'success').then(() => table.ajax.reload());
          }
        });
      }
    });
  });
});
</script>
<?= $this->endSection() ?>

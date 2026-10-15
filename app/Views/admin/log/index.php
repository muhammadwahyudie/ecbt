<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <div>
      <h5 class="card-title mb-0">Audit Trail / Log Aktivitas</h5>
      <small class="text-secondary">Rekaman seluruh tindakan sensitif: login, reset token, perubahan nilai, pelanggaran, dan manipulasi data</small>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableLog">
        <thead class="table-light">
          <tr>
            <th style="width: 170px;">Waktu Kejadian</th>
            <th>Pengguna</th>
            <th>Peran</th>
            <th>Aksi</th>
            <th>Keterangan / Target</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          <!-- Diisi via DataTables AJAX -->
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('#tableLog').DataTable({
    ajax: {
      url: '<?= base_url('admin/log/data') ?>'
    },
    order: [[0, 'desc']],
    columns: [
      {
        data: 'created_at',
        render: function(data) {
          return `<span class="tabular-nums text-nowrap">${data}</span>`;
        }
      },
      {
        data: 'nama',
        render: function(data, type, row) {
          return data ? `<strong>${data}</strong><br><small class="text-muted">@${row.username}</small>` : '<em class="text-muted">Sistem / Anonim</em>';
        }
      },
      {
        data: 'role',
        render: function(data) {
          if (!data) return '-';
          const colors = { admin: 'danger', proktor: 'warning', guru: 'info', siswa: 'secondary' };
          const c = colors[data] || 'light';
          return `<span class="badge bg-${c} text-capitalize">${data}</span>`;
        }
      },
      {
        data: 'aksi',
        render: function(data) {
          return `<span class="badge bg-light text-dark border font-monospace">${data}</span>`;
        }
      },
      {
        data: 'target',
        render: function(data, type, row) {
          let str = data || '-';
          if (row.detail) {
            try {
              const obj = JSON.parse(row.detail);
              str += `<br><small class="text-muted">${JSON.stringify(obj)}</small>`;
            } catch(e) {}
          }
          return str;
        }
      },
      {
        data: 'ip',
        render: function(data) {
          return `<code class="text-muted">${data || '-'}</code>`;
        }
      }
    ],
    language: {
      search: "Cari log:",
      lengthMenu: "Tampilkan _MENU_ log",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ catatan log",
      paginate: { next: "›", previous: "‹" }
    }
  });
});
</script>
<?= $this->endSection() ?>

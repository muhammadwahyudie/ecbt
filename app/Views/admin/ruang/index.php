<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Daftar Ruang Ujian / Lab Komputer</h5>
      <small class="text-secondary">Kelola lokasi ruangan dan kapasitas peserta per sesi ujian</small>
    </div>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahRuang">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
      </svg>
      Tambah Ruang Ujian
    </button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableRuang">
        <thead class="table-light">
          <tr>
            <th>Nama Ruang / Laboratorium</th>
            <th>Kapasitas Kursi</th>
            <th class="text-end" style="width: 80px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- Diisi via DataTables AJAX -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ─── Modal Tambah Ruang ─────────────────────────────────────────── -->
<div class="modal fade" id="modalTambahRuang" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tambah Ruang Ujian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/ruang/simpan') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Ruang / Lab <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Lab Komputer 1, Ruang 04">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Kapasitas Kursi (Peserta) <span class="text-danger">*</span></label>
            <input type="number" name="kapasitas" class="form-control" required placeholder="Contoh: 40" min="1" max="500" value="40">
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ─── Modal Ubah Ruang ───────────────────────────────────────────── -->
<div class="modal fade" id="modalEditRuang" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Ubah Ruang Ujian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditRuang" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Ruang / Lab <span class="text-danger">*</span></label>
            <input type="text" name="nama" id="editNamaRuang" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Kapasitas Kursi (Peserta) <span class="text-danger">*</span></label>
            <input type="number" name="kapasitas" id="editKapasitasRuang" class="form-control" required min="1" max="500">
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Perbarui</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  const table = $('#tableRuang').DataTable({
    ajax: {
      url: '<?= base_url('admin/ruang/data') ?>'
    },
    columns: [
      {
        data: 'nama',
        render: function(data) {
          return `<div class="fw-semibold text-dark">${data}</div>`;
        }
      },
      {
        data: 'kapasitas',
        render: function(data) {
          return `<span class="badge bg-light text-dark border px-3 py-1 fs-6">${data} Komputer / Siswa</span>`;
        }
      },
      {
        data: null,
        orderable: false,
        className: 'text-end',
        render: function(data, type, row) {
          const rowJson = JSON.stringify(row).replace(/"/g, '&quot;');
          return `
            <div class="btn-action-group">
              <button type="button" class="btn-action btn-action-edit btn-edit-ruang" data-ruang="${rowJson}" title="Ubah Ruang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                </svg>
              </button>
              <button type="button" class="btn-action btn-action-delete btn-delete-ruang" data-id="${row.id}" data-nama="${row.nama}" title="Hapus Ruang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
              </button>
            </div>
          `;
        }
      }
    ],
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ ruang",
      paginate: { next: "›", previous: "‹" }
    }
  });

  $(document).on('click', '.btn-edit-ruang', function() {
    const r = JSON.parse($(this).attr('data-ruang'));
    $('#formEditRuang').attr('action', `<?= base_url('admin/ruang/update') ?>/${r.id}`);
    $('#editNamaRuang').val(r.nama);
    $('#editKapasitasRuang').val(r.kapasitas);
    new bootstrap.Modal(document.getElementById('modalEditRuang')).show();
  });

  $(document).on('click', '.btn-delete-ruang', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('admin/ruang/hapus') ?>/${id}`, `Ruang "${nama}" akan dihapus dari sistem.`);
  });
});
</script>
<?= $this->endSection() ?>

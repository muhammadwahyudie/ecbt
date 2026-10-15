<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Daftar Kelas & Rombel</h5>
      <small class="text-secondary">Kelola rombongan belajar dan penempatan peserta didik</small>
    </div>

    <!-- Filter Tahun Ajaran & Tombol Tambah -->
    <div class="d-flex align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2 me-1">
        <label class="small text-secondary mb-0 text-nowrap">Tahun Ajaran:</label>
        <select id="filterTa" class="form-select form-select-sm" style="min-width: 160px;">
          <option value="">Semua Tahun Ajaran</option>
          <?php foreach ($taList as $t): ?>
            <option value="<?= esc($t['id']) ?>" <?= $t['aktif'] ? 'selected' : '' ?>>
              <?= esc($t['nama']) ?> Sem. <?= esc($t['semester']) ?> <?= $t['aktif'] ? '(Aktif)' : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Tambah Kelas
      </button>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableKelas">
        <thead class="table-light">
          <tr>
            <th>Nama Rombel / Kelas</th>
            <th>Tingkat</th>
            <th>Tahun Ajaran</th>
            <th>Jumlah Siswa</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- Diisi via DataTables AJAX -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ─── Modal Tambah Kelas ─────────────────────────────────────────── -->
<div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tambah Kelas Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/kelas/simpan') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
            <select name="tahun_ajaran_id" class="form-select" required>
              <?php foreach ($taList as $t): ?>
                <option value="<?= $t['id'] ?>" <?= ($taAktif && $taAktif['id'] == $t['id']) ? 'selected' : '' ?>>
                  <?= esc($t['nama']) ?> - Semester <?= esc($t['semester']) ?> <?= $t['aktif'] ? '(Aktif)' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
              <input type="text" name="nama" class="form-control" required placeholder="Contoh: X RPL 1, VII A">
            </div>
            <div class="col-4">
              <label class="form-label fw-semibold">Tingkat <span class="text-danger">*</span></label>
              <input type="number" name="tingkat" class="form-control" required placeholder="10" min="1" max="13">
            </div>
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

<!-- ─── Modal Ubah Kelas ───────────────────────────────────────────── -->
<div class="modal fade" id="modalEditKelas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Ubah Data Kelas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditKelas" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
            <select name="tahun_ajaran_id" id="editTaId" class="form-select" required>
              <?php foreach ($taList as $t): ?>
                <option value="<?= $t['id'] ?>">
                  <?= esc($t['nama']) ?> - Semester <?= esc($t['semester']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
              <input type="text" name="nama" id="editNamaKelas" class="form-control" required>
            </div>
            <div class="col-4">
              <label class="form-label fw-semibold">Tingkat <span class="text-danger">*</span></label>
              <input type="number" name="tingkat" id="editTingkat" class="form-control" required min="1" max="13">
            </div>
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
  const table = $('#tableKelas').DataTable({
    ajax: {
      url: '<?= base_url('admin/kelas/data') ?>',
      data: function(d) {
        d.ta_id = $('#filterTa').val();
      }
    },
    columns: [
      {
        data: 'nama',
        render: function(data) {
          return `<div class="fw-bold text-dark">${data}</div>`;
        }
      },
      {
        data: 'tingkat',
        render: function(data) {
          return `<span class="badge bg-light text-dark border">Kelas ${data}</span>`;
        }
      },
      {
        data: null,
        render: function(data, type, row) {
          return `<span>${row.tahun_ajaran} (Sem ${row.semester})</span>`;
        }
      },
      {
        data: 'jumlah_siswa',
        render: function(data) {
          return `<span class="badge bg-info-light text-info fs-6 px-2">${data} Siswa</span>`;
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
              <a href="<?= base_url('admin/kelas/siswa') ?>/${row.id}" class="btn-action btn-action-manage" title="Kelola Siswa">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
              </a>
              <button type="button" class="btn-action btn-action-edit btn-edit-kelas" data-kelas="${rowJson}" title="Ubah Kelas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                </svg>
              </button>
              <button type="button" class="btn-action btn-action-delete btn-delete-kelas" data-id="${row.id}" data-nama="${row.nama}" title="Hapus Kelas">
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
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ rombel",
      paginate: { next: "›", previous: "‹" }
    }
  });

  $('#filterTa').on('change', function() {
    table.ajax.reload();
  });

  $(document).on('click', '.btn-edit-kelas', function() {
    const k = JSON.parse($(this).attr('data-kelas'));
    $('#formEditKelas').attr('action', `<?= base_url('admin/kelas/update') ?>/${k.id}`);
    $('#editNamaKelas').val(k.nama);
    $('#editTingkat').val(k.tingkat);
    $('#editTaId').val(k.tahun_ajaran_id);
    new bootstrap.Modal(document.getElementById('modalEditKelas')).show();
  });

  $(document).on('click', '.btn-delete-kelas', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('admin/kelas/hapus') ?>/${id}`, `Rombel "${nama}" akan dihapus dari sistem.`);
  });
});
</script>
<?= $this->endSection() ?>

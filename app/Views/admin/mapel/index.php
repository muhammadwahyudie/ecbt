<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Tabs Navigasi -->
<ul class="nav nav-pills mb-4" id="mapelTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active px-4 py-2 fw-semibold" id="tab-mapel-btn" data-bs-toggle="pill" data-bs-target="#tab-mapel" type="button" role="tab">
      Daftar Mata Pelajaran
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link px-4 py-2 fw-semibold" id="tab-penugasan-btn" data-bs-toggle="pill" data-bs-target="#tab-penugasan" type="button" role="tab">
      Penugasan Mengajar Guru (<?= count($penugasan) ?>)
    </button>
  </li>
</ul>

<div class="tab-content" id="mapelTabContent">
  <!-- ─── Tab 1: Daftar Mapel ──────────────────────────────────────── -->
  <div class="tab-pane fade show active" id="tab-mapel" role="tabpanel">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h5 class="card-title mb-0">Mata Pelajaran Kurikulum</h5>
          <small class="text-secondary">Kelola daftar mata pelajaran yang diajarkan di sekolah</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMapel">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
          </svg>
          Tambah Mapel
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle w-100" id="tableMapel">
            <thead class="table-light">
              <tr>
                <th style="width: 150px;">Kode Mapel</th>
                <th>Nama Mata Pelajaran</th>
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
  </div>

  <!-- ─── Tab 2: Penugasan Guru ────────────────────────────────────── -->
  <div class="tab-pane fade" id="tab-penugasan" role="tabpanel">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h5 class="card-title mb-0">Penugasan Guru ke Rombel</h5>
          <small class="text-secondary">Atur pengampu mata pelajaran untuk masing-masing rombongan belajar</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPenugasan">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z"/>
          </svg>
          Tugaskan Guru
        </button>
      </div>
      <div class="card-body p-0">
        <?php if (empty($penugasan)): ?>
          <div class="text-center py-5 text-secondary">
            <p class="mb-0">Belum ada penugasan guru yang tercatat. Silakan klik tombol "Tugaskan Guru".</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablePenugasan">
              <thead class="table-light">
                <tr>
                  <th>Nama Guru Pengampu</th>
                  <th>Mata Pelajaran</th>
                  <th>Kelas / Rombel</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($penugasan as $p): ?>
                  <tr>
                    <td class="fw-semibold text-dark"><?= esc($p['nama_guru']) ?></td>
                    <td>
                      <span class="badge bg-light text-dark border me-1"><?= esc($p['kode_mapel']) ?></span>
                      <?= esc($p['nama_mapel']) ?>
                    </td>
                    <td>
                      <span class="badge bg-info-light text-info fw-semibold"><?= esc($p['nama_kelas']) ?></span>
                    </td>
                    <td class="text-end">
                      <div class="btn-action-group">
                        <button type="button" class="btn-action btn-action-delete btn-hapus-penugasan" 
                                data-id="<?= $p['id'] ?>"
                                data-info="<?= esc($p['nama_guru']) ?> - <?= esc($p['nama_mapel']) ?> (<?= esc($p['nama_kelas']) ?>)"
                                title="Hapus Penugasan">
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                          </svg>
                        </button>
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
  </div>
</div>

<!-- ─── Modal Tambah Mapel ─────────────────────────────────────────── -->
<div class="modal fade" id="modalTambahMapel" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tambah Mata Pelajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/mapel/simpan') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Kode Mapel <span class="text-danger">*</span></label>
            <input type="text" name="kode" class="form-control text-uppercase" required placeholder="Contoh: MTK, BIND, PAI">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Matematika Wajib">
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

<!-- ─── Modal Ubah Mapel ───────────────────────────────────────────── -->
<div class="modal fade" id="modalEditMapel" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Ubah Mata Pelajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditMapel" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Kode Mapel <span class="text-danger">*</span></label>
            <input type="text" name="kode" id="editKodeMapel" class="form-control text-uppercase" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
            <input type="text" name="nama" id="editNamaMapel" class="form-control" required>
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

<!-- ─── Modal Tambah Penugasan Guru ───────────────────────────────── -->
<div class="modal fade" id="modalTambahPenugasan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tugaskan Guru ke Kelas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/mapel/penugasan/simpan') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Guru <span class="text-danger">*</span></label>
            <select name="guru_id" class="form-select" required>
              <option value="">-- Pilih Guru --</option>
              <?php foreach ($guruList as $g): ?>
                <option value="<?= $g['id'] ?>"><?= esc($g['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Mata Pelajaran <span class="text-danger">*</span></label>
            <select name="mapel_id" class="form-select" required>
              <option value="">-- Pilih Mapel --</option>
              <?php foreach ($mapelList as $m): ?>
                <option value="<?= $m['id'] ?>"><?= esc($m['kode']) ?> - <?= esc($m['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Kelas / Rombel <span class="text-danger">*</span></label>
            <select name="kelas_id" class="form-select" required>
              <option value="">-- Pilih Kelas --</option>
              <?php foreach ($kelasList as $k): ?>
                <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?> (Tingkat <?= esc($k['tingkat']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Tugaskan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  const table = $('#tableMapel').DataTable({
    ajax: {
      url: '<?= base_url('admin/mapel/data') ?>'
    },
    columns: [
      {
        data: 'kode',
        render: function(data) {
          return `<span class="badge bg-light text-dark border font-monospace fs-6">${data}</span>`;
        }
      },
      {
        data: 'nama',
        render: function(data) {
          return `<div class="fw-semibold text-dark">${data}</div>`;
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
              <button type="button" class="btn-action btn-action-edit btn-edit-mapel" data-mapel="${rowJson}" title="Ubah Mata Pelajaran">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                </svg>
              </button>
              <button type="button" class="btn-action btn-action-delete btn-delete-mapel" data-id="${row.id}" data-nama="${row.nama}" title="Hapus Mata Pelajaran">
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
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ mapel",
      paginate: { next: "›", previous: "‹" }
    }
  });

  $(document).on('click', '.btn-edit-mapel', function() {
    const m = JSON.parse($(this).attr('data-mapel'));
    $('#formEditMapel').attr('action', `<?= base_url('admin/mapel/update') ?>/${m.id}`);
    $('#editKodeMapel').val(m.kode);
    $('#editNamaMapel').val(m.nama);
    new bootstrap.Modal(document.getElementById('modalEditMapel')).show();
  });

  $(document).on('click', '.btn-delete-mapel', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('admin/mapel/hapus') ?>/${id}`, `Mata pelajaran "${nama}" akan dihapus dari sistem.`);
  });

  $('.btn-hapus-penugasan').on('click', function() {
    const id = $(this).data('id');
    const info = $(this).data('info');
    confirmDelete(`<?= base_url('admin/mapel/penugasan/hapus') ?>/${id}`, `Penugasan "${info}" akan dibatalkan.`);
  });
});
</script>
<?= $this->endSection() ?>

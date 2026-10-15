<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Daftar Pengguna Sistem</h5>
      <small class="text-secondary">Kelola akun Admin, Proktor, Guru, dan Siswa</small>
    </div>
    
    <div class="d-flex align-items-center flex-wrap gap-2">
      <!-- Filter Role -->
      <div class="d-flex align-items-center gap-2 me-1">
        <label class="small text-secondary mb-0">Peran:</label>
        <select id="filterRole" class="form-select form-select-sm" style="width: 135px;">
          <option value="">Semua Peran</option>
          <option value="siswa">Siswa</option>
          <option value="guru">Guru</option>
          <option value="proktor">Proktor</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <!-- Impor Excel -->
      <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
          </svg>
          Impor Excel
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalImporSiswa">Impor Data Siswa</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalImporGuru">Impor Data Guru</a></li>
        </ul>
      </div>

      <!-- Tambah Pengguna -->
      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Tambah Pengguna
      </button>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tablePengguna">
        <thead class="table-light">
          <tr>
            <th>Nama Lengkap</th>
            <th>Username</th>
            <th>NIS / NISN</th>
            <th>Peran</th>
            <th>Kelas</th>
            <th>Status</th>
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

<!-- ─── Modal Tambah Pengguna ──────────────────────────────────────── -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/pengguna/simpan') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Peran (Role) <span class="text-danger">*</span></label>
            <select name="role" id="tambahRole" class="form-select" required>
              <option value="siswa">Siswa</option>
              <option value="guru">Guru</option>
              <option value="proktor">Proktor</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Muhammad Rizky">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" class="form-control" required placeholder="Contoh: rizky123">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" required placeholder="Min 6 karakter">
            </div>
          </div>

          <!-- Bagian Khusus Siswa -->
          <div id="tambahFieldSiswa">
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">NIS</label>
                <input type="text" name="nis" class="form-control" placeholder="Nomor Induk Siswa">
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">NISN</label>
                <input type="text" name="nisn" class="form-control" placeholder="10 digit NISN">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Penempatan Kelas (Tahun Ini)</label>
              <select name="kelas_id" class="form-select">
                <option value="">-- Tanpa Kelas --</option>
                <?php foreach ($kelasList as $k): ?>
                  <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-3" id="tambahFieldEmail">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" placeholder="nama@email.com">
          </div>

          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="aktif" value="1" id="tambahAktif" checked>
            <label class="form-check-label fw-semibold" for="tambahAktif">Akun Aktif (Dapat Login)</label>
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

<!-- ─── Modal Ubah Pengguna ────────────────────────────────────────── -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Ubah Data Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditUser" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Peran (Role) <span class="text-danger">*</span></label>
            <select name="role" id="editRole" class="form-select" required>
              <option value="siswa">Siswa</option>
              <option value="guru">Guru</option>
              <option value="proktor">Proktor</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama" id="editNama" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" id="editUsername" class="form-control" required>
          </div>

          <div id="editFieldSiswa">
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">NIS</label>
                <input type="text" name="nis" id="editNis" class="form-control">
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">NISN</label>
                <input type="text" name="nisn" id="editNisn" class="form-control">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Kelas</label>
              <select name="kelas_id" id="editKelasId" class="form-select">
                <option value="">-- Tetap / Tanpa Perubahan --</option>
                <?php foreach ($kelasList as $k): ?>
                  <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-3" id="editFieldEmail">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" id="editEmail" class="form-control">
          </div>

          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="aktif" value="1" id="editAktif">
            <label class="form-check-label fw-semibold" for="editAktif">Akun Aktif (Dapat Login)</label>
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

<!-- ─── Modal Reset Password ──────────────────────────────────────── -->
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Reset Password Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formResetPassword" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <p class="text-secondary small">Masukkan password baru untuk pengguna <strong id="resetTargetName"></strong>. Jika dikosongkan, password akan diatur ke default <code>password123</code>.</p>
          <div class="mb-3">
            <label class="form-label fw-semibold">Password Baru</label>
            <input type="text" name="new_password" class="form-control font-monospace" placeholder="password123">
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning px-4">Reset Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ─── Modal Impor Siswa ─────────────────────────────────────────── -->
<div class="modal fade" id="modalImporSiswa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Impor Siswa dari Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/pengguna/impor-siswa') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <p class="text-secondary small mb-2">Gunakan templat Excel standar sistem agar proses impor berjalan lancar tanpa kendala.</p>
            <a href="<?= base_url('admin/pengguna/template-siswa') ?>" class="btn btn-outline-primary btn-sm">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
              </svg>
              Unduh Templat Siswa (.xlsx)
            </a>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih File Excel <span class="text-danger">*</span></label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
            <small class="text-muted">Maksimal 5 MB. Format didukung: .xlsx, .xls</small>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Unggah & Proses</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ─── Modal Impor Guru ──────────────────────────────────────────── -->
<div class="modal fade" id="modalImporGuru" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Impor Guru dari Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/pengguna/impor-guru') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <p class="text-secondary small mb-2">Gunakan templat Excel standar sistem untuk impor akun Guru.</p>
            <a href="<?= base_url('admin/pengguna/template-guru') ?>" class="btn btn-outline-primary btn-sm">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
              </svg>
              Unduh Templat Guru (.xlsx)
            </a>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih File Excel <span class="text-danger">*</span></label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
            <small class="text-muted">Maksimal 5 MB. Format didukung: .xlsx, .xls</small>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Unggah & Proses</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  const table = $('#tablePengguna').DataTable({
    ajax: {
      url: '<?= base_url('admin/pengguna/data') ?>',
      data: function(d) {
        d.role = $('#filterRole').val();
      }
    },
    columns: [
      {
        data: 'nama',
        render: function(data, type, row) {
          return `<div class="fw-semibold text-dark">${data}</div>`;
        }
      },
      {
        data: 'username',
        render: function(data) {
          return `<code class="text-muted">${data}</code>`;
        }
      },
      {
        data: null,
        render: function(data, type, row) {
          if (row.role !== 'siswa') return '<span class="text-muted">-</span>';
          let str = '';
          if (row.nis) str += `<span class="badge bg-light text-dark me-1">NIS: ${row.nis}</span>`;
          if (row.nisn) str += `<span class="badge bg-light text-dark">NISN: ${row.nisn}</span>`;
          return str || '<span class="text-muted">-</span>';
        }
      },
      {
        data: 'role',
        render: function(data) {
          const colors = {
            admin: 'bg-danger-light text-danger',
            proktor: 'bg-warning-light text-warning',
            guru: 'bg-accent-light text-accent',
            siswa: 'bg-info-light text-info'
          };
          const cls = colors[data] || 'bg-light text-secondary';
          return `<span class="badge ${cls} text-capitalize">${data}</span>`;
        }
      },
      {
        data: 'nama_kelas',
        render: function(data, type, row) {
          return row.role === 'siswa' && data ? `<span class="badge bg-light text-dark border">${data}</span>` : '<span class="text-muted">-</span>';
        }
      },
      {
        data: 'aktif',
        render: function(data) {
          return data == 1 
            ? '<span class="badge bg-success-light text-success">Aktif</span>'
            : '<span class="badge bg-danger-light text-danger">Nonaktif</span>';
        }
      },
      {
        data: null,
        orderable: false,
        className: 'text-end',
        render: function(data, type, row) {
          const rowJson = JSON.stringify(row).replace(/"/g, '&quot;');
          return `
            <div class="dropdown">
              <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Opsi
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><button class="dropdown-item btn-edit" data-user="${rowJson}">Ubah Data</button></li>
                <li><button class="dropdown-item btn-reset" data-id="${row.id}" data-nama="${row.nama}">Reset Password</button></li>
                <li><hr class="dropdown-divider"></li>
                <li><button class="dropdown-item text-danger btn-delete" data-id="${row.id}" data-nama="${row.nama}">Hapus Akun</button></li>
              </ul>
            </div>
          `;
        }
      }
    ],
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pengguna",
      paginate: { next: "›", previous: "‹" }
    }
  });

  // Filter Role change
  $('#filterRole').on('change', function() {
    table.ajax.reload();
  });

  // Toggle dynamic fields on tambah modal
  $('#tambahRole').on('change', function() {
    if ($(this).val() === 'siswa') {
      $('#tambahFieldSiswa').show();
    } else {
      $('#tambahFieldSiswa').hide();
    }
  });

  // Edit User Modal
  $(document).on('click', '.btn-edit', function() {
    const user = JSON.parse($(this).attr('data-user'));
    $('#formEditUser').attr('action', `<?= base_url('admin/pengguna/update') ?>/${user.id}`);
    $('#editNama').val(user.nama);
    $('#editUsername').val(user.username);
    $('#editRole').val(user.role);
    $('#editNis').val(user.nis || '');
    $('#editNisn').val(user.nisn || '');
    $('#editEmail').val(user.email || '');
    $('#editAktif').prop('checked', user.aktif == 1);

    if (user.role === 'siswa') {
      $('#editFieldSiswa').show();
    } else {
      $('#editFieldSiswa').hide();
    }

    new bootstrap.Modal(document.getElementById('modalEditUser')).show();
  });

  // Reset Password Modal
  $(document).on('click', '.btn-reset', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    $('#formResetPassword').attr('action', `<?= base_url('admin/pengguna/reset-password') ?>/${id}`);
    $('#resetTargetName').text(nama);
    new bootstrap.Modal(document.getElementById('modalResetPassword')).show();
  });

  // Delete User with SweetAlert2
  $(document).on('click', '.btn-delete', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('admin/pengguna/hapus') ?>/${id}`, `Akun "${nama}" akan dinonaktifkan / dihapus dari sistem.`);
  });
});
</script>
<?= $this->endSection() ?>

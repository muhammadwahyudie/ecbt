<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Daftar Guru</h5>
      <small class="text-secondary">Kelola akun dan data seluruh guru pengajar</small>
    </div>

    <div class="d-flex align-items-center flex-wrap gap-2">
      <!-- Impor Excel -->
      <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
          </svg>
          Impor Excel
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
          <li><a class="dropdown-item" href="<?= base_url('admin/guru/template') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14" class="me-1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            Unduh Templat (.xlsx)
          </a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalImporGuru">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14" class="me-1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            Impor Data Guru
          </a></li>
        </ul>
      </div>

      <!-- Tambah Guru -->
      <button type="button" class="btn btn-primary btn-sm" id="btnTambahGuru">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Tambah Guru
      </button>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableGuru">
        <thead class="table-light">
          <tr>
            <th>Nama Lengkap</th>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
            <th class="text-end" style="width: 110px;">Aksi</th>
          </tr>
        </thead>
        <tbody><!-- diisi DataTables AJAX --></tbody>
      </table>
    </div>
  </div>
</div>

<!-- ─── Modal Tambah / Edit Guru ──────────────────────────────────────────────── -->
<div class="modal fade" id="modalFormGuru" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalGuruLabel">Tambah Guru Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formGuru">
        <?= csrf_field() ?>
        <input type="hidden" id="guruId" name="guru_id" value="">
        <div class="modal-body py-3">
          <!-- Error container -->
          <div id="guruErrors" class="alert alert-danger d-none small"></div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama" id="guruNama" class="form-control" placeholder="Contoh: Budi Santoso, S.Pd" required>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" id="guruUsername" class="form-control" placeholder="Contoh: budi_guru" required>
            </div>
            <div class="col-6" id="wrapPassword">
              <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" id="guruPassword" class="form-control" placeholder="Min 6 karakter">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" id="guruEmail" class="form-control" placeholder="nama@email.com">
          </div>

          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="aktif" value="1" id="guruAktif" checked>
            <label class="form-check-label fw-semibold" for="guruAktif">Akun Aktif (Dapat Login)</label>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4" id="btnSimpanGuru">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ─── Modal Reset Password ──────────────────────────────────────────────────── -->
<div class="modal fade" id="modalResetGuru" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Reset Password Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formResetGuru">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <p class="text-secondary small">Masukkan password baru untuk <strong id="resetGuruNama"></strong>. Jika dikosongkan, password default: <code>guru123</code>.</p>
          <div class="mb-3">
            <label class="form-label fw-semibold">Password Baru</label>
            <input type="text" name="new_password" class="form-control font-monospace" placeholder="guru123">
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

<!-- ─── Modal Impor Excel ─────────────────────────────────────────────────────── -->
<div class="modal fade" id="modalImporGuru" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Impor Guru dari Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formImporGuru" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <p class="text-secondary small mb-3">Gunakan template standar sistem. <a href="<?= base_url('admin/guru/template') ?>" class="fw-semibold">Unduh template .xlsx</a></p>
          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih File Excel <span class="text-danger">*</span></label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
            <small class="text-muted">Maks 5 MB. Format: .xlsx, .xls</small>
          </div>
          <div id="imporGuruResult" class="d-none"></div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4" id="btnImporGuru">Unggah &amp; Proses</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {

  // ── DataTables ──────────────────────────────────────────────────────────────
  const table = $('#tableGuru').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '<?= base_url('admin/guru/data') ?>',
      type: 'GET',
    },
    columns: [
      {
        data: 'nama',
        render: (d, t, row) =>
          `<div class="fw-semibold text-dark">${d}</div>`,
      },
      {
        data: 'username',
        render: d => `<code class="text-muted">${d}</code>`,
      },
      {
        data: 'email',
        render: d => d ? `<span class="text-secondary">${d}</span>` : '<span class="text-muted">-</span>',
      },
      {
        data: 'aktif',
        render: d => d == 1
          ? '<span class="badge bg-success-light text-success">Aktif</span>'
          : '<span class="badge bg-danger-light text-danger">Nonaktif</span>',
      },
      {
        data: null,
        orderable: false,
        className: 'text-end',
        render: (d, t, row) => {
          const r = JSON.stringify(row).replace(/"/g, '&quot;');
          return `
            <div class="btn-action-group">
              <button type="button" class="btn-action btn-action-edit btn-edit-guru" data-row="${r}" title="Ubah Data Guru">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                </svg>
              </button>
              <button type="button" class="btn-action btn-action-activate btn-reset-guru" data-id="${row.id}" data-nama="${row.nama}" title="Reset Password">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                </svg>
              </button>
              <button type="button" class="btn-action btn-action-delete btn-hapus-guru" data-id="${row.id}" data-nama="${row.nama}" title="Hapus Guru">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
              </button>
            </div>`;
        },
      },
    ],
    language: {
      search: 'Cari:',
      lengthMenu: 'Tampilkan _MENU_ data',
      info: 'Menampilkan _START_ – _END_ dari _TOTAL_ guru',
      paginate: { next: '›', previous: '‹' },
      processing: '<div class="text-center py-3 text-secondary small">Memuat data...</div>',
    },
  });

  // ── Buka modal TAMBAH ───────────────────────────────────────────────────────
  $('#btnTambahGuru').on('click', function () {
    $('#modalGuruLabel').text('Tambah Guru Baru');
    $('#formGuru')[0].reset();
    $('#guruId').val('');
    $('#wrapPassword').show();
    $('#guruPassword').prop('required', true);
    $('#guruErrors').addClass('d-none').html('');
    $('#btnSimpanGuru').text('Simpan');
    new bootstrap.Modal('#modalFormGuru').show();
  });

  // ── Buka modal EDIT ─────────────────────────────────────────────────────────
  $(document).on('click', '.btn-edit-guru', function () {
    const row = JSON.parse($(this).attr('data-row'));
    $('#modalGuruLabel').text('Ubah Data Guru');
    $('#guruId').val(row.id);
    $('#guruNama').val(row.nama);
    $('#guruUsername').val(row.username);
    $('#guruEmail').val(row.email || '');
    $('#guruAktif').prop('checked', row.aktif == 1);
    $('#wrapPassword').hide();
    $('#guruPassword').prop('required', false).val('');
    $('#guruErrors').addClass('d-none').html('');
    $('#btnSimpanGuru').text('Perbarui');
    new bootstrap.Modal('#modalFormGuru').show();
  });

  // ── Submit form TAMBAH / EDIT ────────────────────────────────────────────────
  $('#formGuru').on('submit', function (e) {
    e.preventDefault();
    const id  = $('#guruId').val();
    const url = id
      ? '<?= base_url('admin/guru/update') ?>/' + id
      : '<?= base_url('admin/guru/simpan') ?>';

    $.ajax({
      url,
      method: 'POST',
      data: $(this).serialize(),
      success: function (res) {
        if (res.success) {
          bootstrap.Modal.getInstance(document.getElementById('modalFormGuru')).hide();
          table.ajax.reload();
          Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2500, showConfirmButton: false });
        } else {
          let html = '<ul class="mb-0 ps-3">';
          $.each(res.errors || {}, (k, v) => html += `<li>${v}</li>`);
          html += '</ul>';
          $('#guruErrors').removeClass('d-none').html(res.errors ? html : res.message);
        }
      },
      error: () => Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error'),
    });
  });

  // ── Reset Password ───────────────────────────────────────────────────────────
  let resetGuruId = null;
  $(document).on('click', '.btn-reset-guru', function () {
    resetGuruId = $(this).data('id');
    $('#resetGuruNama').text($(this).data('nama'));
    $('#formResetGuru')[0].reset();
    new bootstrap.Modal('#modalResetGuru').show();
  });

  $('#formResetGuru').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: '<?= base_url('admin/guru/reset-password') ?>/' + resetGuruId,
      method: 'POST',
      data: $(this).serialize(),
      success: function (res) {
        bootstrap.Modal.getInstance(document.getElementById('modalResetGuru')).hide();
        Swal.fire({
          icon: 'success',
          title: 'Password Direset',
          html: `${res.message}<br><br>Password baru: <code class="fs-5">${res.password}</code>`,
        });
      },
      error: () => Swal.fire('Error', 'Gagal mereset password.', 'error'),
    });
  });

  // ── Hapus ────────────────────────────────────────────────────────────────────
  $(document).on('click', '.btn-hapus-guru', function () {
    const id   = $(this).data('id');
    const nama = $(this).data('nama');
    Swal.fire({
      icon: 'warning',
      title: 'Hapus Guru?',
      text: `Akun "${nama}" akan dihapus dari sistem.`,
      showCancelButton: true,
      confirmButtonColor: '#991b1b',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
    }).then(result => {
      if (!result.isConfirmed) return;
      $.post('<?= base_url('admin/guru/hapus') ?>/' + id, { '<?= csrf_token() ?>': csrfToken }, function (res) {
        if (res.success) {
          table.ajax.reload();
          Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, timer: 2000, showConfirmButton: false });
        } else {
          Swal.fire('Gagal', res.message, 'error');
        }
      });
    });
  });

  // ── Impor Excel ──────────────────────────────────────────────────────────────
  $('#formImporGuru').on('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    $('#btnImporGuru').prop('disabled', true).text('Memproses...');
    $.ajax({
      url: '<?= base_url('admin/guru/impor') ?>',
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      success: function (res) {
        $('#btnImporGuru').prop('disabled', false).text('Unggah & Proses');
        let html = res.success
          ? `<div class="alert alert-success small mb-0">${res.message}</div>`
          : `<div class="alert alert-danger small mb-0">${res.message}</div>`;
        if (res.gagal && res.gagal.length) {
          html += `<div class="alert alert-warning small mt-2 mb-0"><strong>Gagal diimpor:</strong><ul class="mb-0 ps-3 mt-1">${res.gagal.slice(0,5).map(g=>`<li>${g}</li>`).join('')}</ul></div>`;
        }
        $('#imporGuruResult').removeClass('d-none').html(html);
        if (res.success) table.ajax.reload();
      },
      error: () => {
        $('#btnImporGuru').prop('disabled', false).text('Unggah & Proses');
        Swal.fire('Error', 'Gagal mengunggah file.', 'error');
      },
    });
  });
});
</script>
<?= $this->endSection() ?>

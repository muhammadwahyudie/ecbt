<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBankSoal">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
  </svg>
  Buat Bank Soal
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Bank Soal</h5>
      <small class="text-secondary">Kelola paket bank soal yang akan diisi butir soalnya oleh guru</small>
    </div>
    <div id="filterInfo" class="d-none align-items-center gap-2">
      <span class="badge bg-info text-dark">Filter Guru Aktif</span>
      <a href="<?= base_url('admin/bank-soal') ?>" class="btn btn-xs btn-outline-secondary btn-sm">Reset Filter</a>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableBankSoal">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Judul Paket Soal</th>
            <th>Guru Pengampu</th>
            <th>Mata Pelajaran</th>
            <th class="text-center">Jumlah Soal</th>
            <th>Dibuat</th>
            <th class="text-end" style="width: 110px;">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Bank Soal -->
<div class="modal fade" id="modalTambahBankSoal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Buat Bank Soal Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/bank-soal/simpan') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Paket Bank Soal <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" placeholder="Contoh: Penilaian Harian Matematika Wajib" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
            <select name="mapel_id" class="form-select" required>
              <option value="">-- Pilih Mata Pelajaran --</option>
              <?php foreach ($mapelList as $m): ?>
                <option value="<?= $m['id'] ?>"><?= esc($m['kode']) ?> - <?= esc($m['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Tingkat / Kelas</label>
              <select name="tingkat" class="form-select">
                <option value="">Semua Tingkat</option>
                <option value="7">Kelas 7 (SMP)</option>
                <option value="8">Kelas 8 (SMP)</option>
                <option value="9">Kelas 9 (SMP)</option>
                <option value="10">Kelas 10 (SMA/SMK)</option>
                <option value="11">Kelas 11 (SMA/SMK)</option>
                <option value="12">Kelas 12 (SMA/SMK)</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Guru Pengampu <span class="text-danger">*</span></label>
              <select name="pemilik_id" class="form-select" required>
                <option value="">-- Pilih Guru --</option>
                <?php foreach ($guruList as $g): ?>
                  <option value="<?= $g['id'] ?>"><?= esc($g['nama']) ?> (<?= esc($g['role']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <small class="text-muted d-block">Paket soal ini akan langsung muncul di akun guru yang bersangkutan untuk pengisian butir soal.</small>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Simpan Paket</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Bank Soal -->
<div class="modal fade" id="modalEditBankSoal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Ubah Identitas Bank Soal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditBankSoal" action="" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Paket Bank Soal <span class="text-danger">*</span></label>
            <input type="text" id="edit_nama" name="nama" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
            <select id="edit_mapel_id" name="mapel_id" class="form-select" required>
              <option value="">-- Pilih Mata Pelajaran --</option>
              <?php foreach ($mapelList as $m): ?>
                <option value="<?= $m['id'] ?>"><?= esc($m['kode']) ?> - <?= esc($m['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Tingkat / Kelas</label>
              <select id="edit_tingkat" name="tingkat" class="form-select">
                <option value="">Semua Tingkat</option>
                <option value="7">Kelas 7 (SMP)</option>
                <option value="8">Kelas 8 (SMP)</option>
                <option value="9">Kelas 9 (SMP)</option>
                <option value="10">Kelas 10 (SMA/SMK)</option>
                <option value="11">Kelas 11 (SMA/SMK)</option>
                <option value="12">Kelas 12 (SMA/SMK)</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Guru Pengampu <span class="text-danger">*</span></label>
              <select id="edit_pemilik_id" name="pemilik_id" class="form-select" required>
                <option value="">-- Pilih Guru --</option>
                <?php foreach ($guruList as $g): ?>
                  <option value="<?= $g['id'] ?>"><?= esc($g['nama']) ?> (<?= esc($g['role']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  const urlParams = new URLSearchParams(window.location.search);
  const guruId = urlParams.get('guru');
  if (guruId) {
    $('#filterInfo').removeClass('d-none').addClass('d-flex');
  }

  const ajaxUrl = '<?= base_url('admin/bank-soal/data') ?>' + (guruId ? '?guru=' + encodeURIComponent(guruId) : '');

  const table = $('#tableBankSoal').DataTable({
    ajax: { url: ajaxUrl },
    columns: [
      { data: null, render: (d, t, r, m) => m.row + 1, orderable: false },
      {
        data: 'nama',
        render: (d, t, r) => {
          let sub = r.tingkat ? `<small class="text-muted d-block">Tingkat Kelas ${r.tingkat}</small>` : '';
          return `<a href="<?= base_url('admin/bank-soal') ?>/${r.id}" class="fw-semibold text-dark text-decoration-none">${d}</a>${sub}`;
        }
      },
      { data: 'nama_pemilik', render: d => d || '<span class="text-muted">-</span>' },
      {
        data: 'nama_mapel',
        render: d => d ? `<span class="badge bg-light text-dark border">${d}</span>` : '<span class="text-muted">-</span>'
      },
      {
        data: 'jumlah_soal', className: 'text-center',
        render: d => `<span class="badge bg-primary px-3 py-1">${d} butir</span>`
      },
      {
        data: 'created_at',
        render: d => d ? new Date(d).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) : '-'
      },
      {
        data: null, orderable: false, className: 'text-end',
        render: (d, t, row) => `
          <div class="btn-action-group">
            <a href="<?= base_url('admin/bank-soal') ?>/${row.id}" class="btn-action btn-action-view" title="Lihat Butir Soal">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </a>
            <button type="button" class="btn-action btn-action-edit btn-edit-paket" data-json='${JSON.stringify(row).replace(/'/g, "&apos;")}' title="Ubah Identitas Paket">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
              </svg>
            </button>
            <button type="button" class="btn-action btn-action-delete btn-delete-paket" data-id="${row.id}" data-nama="${row.nama}" title="Hapus Paket">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
              </svg>
            </button>
          </div>
        `
      }
    ],
    language: { search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data', info: 'Menampilkan _START_–_END_ dari _TOTAL_', paginate: { next: '›', previous: '‹' } }
  });

  // Edit Paket Soal
  $('#tableBankSoal').on('click', '.btn-edit-paket', function() {
    const data = $(this).data('json');
    $('#formEditBankSoal').attr('action', `<?= base_url('admin/bank-soal/update') ?>/${data.id}`);
    $('#edit_nama').val(data.nama);
    $('#edit_mapel_id').val(data.mapel_id);
    $('#edit_tingkat').val(data.tingkat || '');
    $('#edit_pemilik_id').val(data.pemilik_id || '');
    $('#modalEditBankSoal').modal('show');
  });

  // Hapus Paket Soal
  $('#tableBankSoal').on('click', '.btn-delete-paket', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('admin/bank-soal/hapus') ?>/${id}`, `Bank soal "${nama}" beserta butir soal di dalamnya akan dihapus.`);
  });
});
</script>
<?= $this->endSection() ?>

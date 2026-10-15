<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Daftar Tahun Ajaran & Semester</h5>
      <small class="text-secondary">Hanya satu tahun ajaran dan semester yang dapat berstatus aktif sebagai acuan operasional sistem</small>
    </div>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTa">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
      </svg>
      Tambah Tahun Ajaran
    </button>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Tahun Ajaran</th>
            <th>Semester</th>
            <th>Status Operasional</th>
            <th class="text-end" style="width: 120px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($taList as $t): ?>
            <tr class="<?= $t['aktif'] ? 'table-success-light' : '' ?>">
              <td class="fw-bold text-dark fs-6"><?= esc($t['nama']) ?></td>
              <td>
                <span class="badge bg-light text-dark border">
                  Semester <?= esc($t['semester']) ?> (<?= $t['semester'] == '1' ? 'Ganjil' : 'Genap' ?>)
                </span>
              </td>
              <td>
                <?php if ($t['aktif']): ?>
                  <span class="badge bg-success fs-6 px-3 py-1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14" class="me-1">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Sedang Aktif
                  </span>
                <?php else: ?>
                  <span class="badge bg-secondary">Tidak Aktif</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <div class="btn-action-group">
                  <?php if (!$t['aktif']): ?>
                    <form action="<?= base_url('admin/tahun-ajaran/aktifkan/' . $t['id']) ?>" method="POST" class="d-inline m-0 p-0">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn-action btn-action-activate" title="Aktifkan Tahun Ajaran">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                      </button>
                    </form>
                  <?php endif; ?>
                  <button type="button" class="btn-action btn-action-edit btn-edit-ta" 
                          data-id="<?= $t['id'] ?>"
                          data-nama="<?= esc($t['nama']) ?>"
                          data-semester="<?= $t['semester'] ?>"
                          title="Ubah Tahun Ajaran">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                  </button>
                  <?php if (!$t['aktif']): ?>
                    <button type="button" class="btn-action btn-action-delete btn-delete-ta" 
                            data-id="<?= $t['id'] ?>"
                            data-nama="<?= esc($t['nama']) ?> Sem <?= esc($t['semester']) ?>"
                            title="Hapus Tahun Ajaran">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                      </svg>
                    </button>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ─── Modal Tambah Tahun Ajaran ──────────────────────────────────── -->
<div class="modal fade" id="modalTambahTa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tambah Tahun Ajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/tahun-ajaran/simpan') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Tahun Ajaran <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" required placeholder="Contoh: 2026/2027">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
            <select name="semester" class="form-select" required>
              <option value="1">Semester 1 (Ganjil)</option>
              <option value="2">Semester 2 (Genap)</option>
            </select>
          </div>
          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="aktif" value="1" id="tambahTaAktif">
            <label class="form-check-label fw-semibold" for="tambahTaAktif">Langsung Aktifkan Tahun Ajaran Ini</label>
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

<!-- ─── Modal Ubah Tahun Ajaran ────────────────────────────────────── -->
<div class="modal fade" id="modalEditTa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Ubah Tahun Ajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditTa" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Tahun Ajaran <span class="text-danger">*</span></label>
            <input type="text" name="nama" id="editNamaTa" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
            <select name="semester" id="editSemesterTa" class="form-select" required>
              <option value="1">Semester 1 (Ganjil)</option>
              <option value="2">Semester 2 (Genap)</option>
            </select>
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
  $('.btn-edit-ta').on('click', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    const sem = $(this).data('semester');

    $('#formEditTa').attr('action', `<?= base_url('admin/tahun-ajaran/update') ?>/${id}`);
    $('#editNamaTa').val(nama);
    $('#editSemesterTa').val(sem);
    new bootstrap.Modal(document.getElementById('modalEditTa')).show();
  });

  $('.btn-delete-ta').on('click', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('admin/tahun-ajaran/hapus') ?>/${id}`, `Tahun ajaran "${nama}" akan dihapus.`);
  });
});
</script>
<?= $this->endSection() ?>

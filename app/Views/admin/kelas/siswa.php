<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<div class="d-flex gap-2">
  <a href="<?= base_url('admin/kelas') ?>" class="btn btn-outline-secondary btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Kembali ke Daftar Kelas
  </a>
  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSiswaKelas">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    Tambah Siswa ke Rombel
  </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Banner Info Kelas -->
<div class="card mb-4 border-0" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
  <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <span class="badge bg-accent-light text-accent mb-2">Tingkat <?= esc($kelas['tingkat']) ?></span>
      <h3 class="fw-bold mb-1" style="font-family: 'Sora', sans-serif;">Rombel: <?= esc($kelas['nama']) ?></h3>
      <p class="mb-0 text-white-50">Tahun Ajaran: <?= esc($ta['nama'] ?? '-') ?> (Semester <?= esc($ta['semester'] ?? '-') ?>) &nbsp;&middot;&nbsp; Total: <strong><?= count($siswaInKelas) ?> Siswa</strong></p>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Anggota Siswa Rombel</h5>
  </div>
  <div class="card-body p-0">
    <?php if (empty($siswaInKelas)): ?>
      <div class="text-center py-5 text-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="48" height="48" class="mb-2 text-muted">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
        </svg>
        <p class="mb-0">Belum ada siswa yang ditempatkan di kelas ini.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 50px;">No</th>
              <th>Nama Siswa</th>
              <th>Username</th>
              <th>NIS</th>
              <th>NISN</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($siswaInKelas as $idx => $s): ?>
              <tr>
                <td class="tabular-nums text-secondary"><?= $idx + 1 ?></td>
                <td class="fw-semibold text-dark"><?= esc($s['nama']) ?></td>
                <td><code class="text-muted"><?= esc($s['username']) ?></code></td>
                <td><?= esc($s['nis'] ?: '-') ?></td>
                <td><?= esc($s['nisn'] ?: '-') ?></td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-outline-danger btn-keluarkan" 
                          data-id="<?= $s['siswa_kelas_id'] ?>" 
                          data-nama="<?= esc($s['nama']) ?>">
                    Keluarkan
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ─── Modal Tambah Siswa ke Kelas ─────────────────────────────────── -->
<div class="modal fade" id="modalTambahSiswaKelas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tambah Siswa ke Rombel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/kelas/tambah-siswa') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="kelas_id" value="<?= $kelas['id'] ?>">
        <div class="modal-body py-3">
          <p class="text-secondary small mb-3">Pilih siswa aktif yang belum terdaftar di kelas mana pun pada tahun ajaran ini:</p>
          <?php if (empty($unassignedSiswa)): ?>
            <div class="alert alert-warning mb-0 small">
              Semua siswa aktif sudah terdaftar di kelas pada tahun ajaran ini. Tambah siswa baru di menu Pengguna jika diperlukan.
            </div>
          <?php else: ?>
            <div class="mb-3">
              <label class="form-label fw-semibold">Pilih Siswa <span class="text-danger">*</span></label>
              <select name="user_id" class="form-select" required size="8">
                <?php foreach ($unassignedSiswa as $us): ?>
                  <option value="<?= $us['id'] ?>">
                    <?= esc($us['nama']) ?> (<?= esc($us['username']) ?> <?= $us['nis'] ? '· NIS ' . esc($us['nis']) : '' ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <?php if (!empty($unassignedSiswa)): ?>
            <button type="submit" class="btn btn-primary px-4">Tambahkan</button>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('.btn-keluarkan').on('click', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('admin/kelas/hapus-siswa') ?>/${id}`, `Siswa "${nama}" akan dikeluarkan dari kelas ini.`);
  });
});
</script>
<?= $this->endSection() ?>

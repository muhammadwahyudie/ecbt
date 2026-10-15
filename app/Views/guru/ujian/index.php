<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Kelola Ujian Saya</h5>
      <small class="text-secondary">Daftar jadwal ujian berdasarkan bank paket soal Anda</small>
    </div>
    <a href="<?= base_url('guru/ujian/buat') ?>" class="btn btn-primary btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
      </svg>
      Jadwalkan Ujian Baru
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableGuruUjian">
        <thead class="table-light">
          <tr>
            <th>Nama Ujian & Paket</th>
            <th>Mata Pelajaran</th>
            <th>Jadwal Pelaksanaan</th>
            <th>Durasi</th>
            <th>Token Akses</th>
            <th>Status</th>
            <th>Peserta</th>
            <th class="text-end" style="width: 150px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ujianList as $u): ?>
            <tr>
              <td>
                <a href="<?= base_url('guru/ujian/' . $u['id']) ?>" class="fw-bold text-dark text-decoration-none">
                  <?= esc($u['nama']) ?>
                </a>
                <br>
                <small class="text-secondary">Paket: <?= esc($u['nama_paket']) ?></small>
              </td>
              <td>
                <span class="badge bg-light text-dark border"><?= esc($u['nama_mapel'] ?? '-') ?></span>
              </td>
              <td class="tabular-nums">
                <?= date('d M Y', strtotime($u['mulai_at'])) ?><br>
                <small class="text-muted"><?= date('H:i', strtotime($u['mulai_at'])) ?> - <?= date('H:i', strtotime($u['selesai_at'])) ?></small>
              </td>
              <td class="tabular-nums"><?= esc($u['durasi_menit']) ?> mnt</td>
              <td>
                <?php if ($u['token']): ?>
                  <div class="d-inline-flex align-items-center gap-1 bg-dark text-white px-2 py-1 rounded font-monospace fw-bold" style="letter-spacing: 1.5px;">
                    <span><?= esc($u['token']) ?></span>
                    <button class="btn btn-link btn-sm text-white-50 p-0 ms-1 btn-salin-token" data-token="<?= esc($u['token']) ?>" title="Salin Token">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/>
                      </svg>
                    </button>
                  </div>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php
                  $badgeCls = match($u['status']) {
                    'berlangsung' => 'bg-success',
                    'terjadwal'   => 'bg-info',
                    'selesai'     => 'bg-secondary',
                    'draf'        => 'bg-warning',
                    default       => 'bg-light text-dark'
                  };
                ?>
                <span class="badge <?= $badgeCls ?>"><?= ucfirst(esc($u['status'])) ?></span>
              </td>
              <td>
                <span class="badge bg-light text-dark border"><?= esc($u['total_peserta']) ?> Siswa</span>
              </td>
              <td class="text-end">
                <div class="btn-action-group">
                  <a href="<?= base_url('guru/ujian/' . $u['id']) ?>" class="btn-action btn-action-view" title="Detail &amp; Kelola Ujian">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                  </a>
                  <a href="<?= base_url('guru/koreksi/ujian/' . $u['id']) ?>" class="btn-action btn-action-edit" title="Koreksi Uraian">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.155.095 2 1.059 2 2.194V7.5M9 14.25l2.25 2.25L15 12M6 8.25h12a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 19.5v-9A2.25 2.25 0 016 8.25z"/>
                    </svg>
                  </a>
                  <a href="<?= base_url('guru/nilai/ujian/' . $u['id']) ?>" class="btn-action btn-action-manage" title="Rekap Nilai">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                  </a>
                  <button type="button" class="btn-action btn-action-delete btn-delete-ujian" 
                          data-id="<?= $u['id'] ?>" 
                          data-nama="<?= esc($u['nama']) ?>"
                          title="Hapus Jadwal Ujian">
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
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('#tableGuruUjian').DataTable({
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ ujian",
      paginate: { next: "›", previous: "‹" }
    }
  });

  $('.btn-salin-token').on('click', function(e) {
    e.preventDefault();
    const token = $(this).data('token');
    navigator.clipboard.writeText(token).then(() => {
      showToast('success', 'Token ' + token + ' disalin!');
    });
  });

  $('.btn-delete-ujian').on('click', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('guru/ujian/hapus') ?>/${id}`, `Jadwal ujian "${nama}" akan dihapus.`);
  });
});
</script>
<?= $this->endSection() ?>

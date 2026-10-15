<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Bank Soal</h5>
      <small class="text-secondary">Paket bank soal dibuat oleh Admin. Guru dapat mengelola butir soal di dalamnya dan menggunakannya untuk ujian.</small>
    </div>

    <div class="d-flex align-items-center flex-wrap gap-2">
      <select id="filterMapelPaket" class="form-select form-select-sm" style="min-width: 160px;">
        <option value="">Semua Mata Pelajaran</option>
        <?php foreach ($mapelList as $m): ?>
          <option value="<?= esc($m['nama']) ?>"><?= esc($m['kode']) ?> - <?= esc($m['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tablePaketSoal">
        <thead class="table-light">
          <tr>
            <th>Nama Paket Soal</th>
            <th>Mata Pelajaran</th>
            <th>Tingkat</th>
            <th>Jumlah Butir Soal</th>
            <th>Pemilik / Guru</th>
            <th class="text-end" style="width: 70px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paketList as $p): ?>
            <tr>
              <td>
                <a href="<?= base_url('guru/paket-soal/' . $p['id']) ?>" class="fw-bold text-dark text-decoration-none">
                  <?= esc($p['nama']) ?>
                </a>
              </td>
              <td>
                <span class="badge bg-light text-dark border">
                  <?= esc($p['nama_mapel'] ?? '-') ?>
                </span>
              </td>
              <td>
                <?= $p['tingkat'] ? '<span class="badge bg-info-light text-info">Kelas ' . esc($p['tingkat']) . '</span>' : '-' ?>
              </td>
              <td>
                <span class="badge bg-accent-light text-accent fs-6 px-2">
                  <?= esc($p['jumlah_soal']) ?> Butir
                </span>
              </td>
              <td class="small text-secondary"><?= esc($p['nama_pemilik'] ?? '-') ?></td>
              <td class="text-end">
                <div class="btn-action-group">
                  <a href="<?= base_url('guru/paket-soal/' . $p['id']) ?>" class="btn-action btn-action-primary" title="Kelola Butir Soal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                  </a>
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
  const table = $('#tablePaketSoal').DataTable({
    language: {
      search: "Cari paket:",
      lengthMenu: "Tampilkan _MENU_ paket",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ paket soal",
      paginate: { next: "›", previous: "‹" }
    }
  });

  $('#filterMapelPaket').on('change', function() {
    table.column(1).search(this.value).draw();
  });

  $('.btn-delete-paket').on('click', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmDelete(`<?= base_url('guru/paket-soal/hapus') ?>/${id}`, `Paket soal "${nama}" beserta seluruh soal di dalamnya akan dihapus.`);
  });
});
</script>
<?= $this->endSection() ?>

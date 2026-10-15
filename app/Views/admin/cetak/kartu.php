<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-3">
  <!-- Baris Atas: Judul Halaman & Tombol Pengaturan Tersendiri -->
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h5 class="card-title mb-1 fw-bold text-dark d-flex align-items-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="22" height="22" class="me-2 text-primary">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
            </svg>
            Cetak Kartu Ujian (Kartu Peserta &amp; Kartu Meja)
          </h5>
          <small class="text-secondary">Standar A4 portrait (5 baris &times; 2 kolom = 10 kartu/lembar) sesuai kartu.md &amp; kartu.pdf</small>
        </div>

        <div class="d-flex align-items-center gap-2">
          <!-- Tombol Pengaturan Kartu Tersendiri -->
          <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalPengaturanKartu" id="btnBukaPengaturan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <circle cx="12" cy="12" r="3" stroke-width="2"/>
            </svg>
            <span>Pengaturan Kartu</span>
          </button>

          <!-- Tombol Cetak Semua Siswa (Sama Tab / Tidak Menambah Tab Baru) -->
          <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1" id="btnCetakSemua">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/>
            </svg>
            <span>Cetak Semua Siswa</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tersendiri: Pengaturan Desain & Kop Kartu -->
  <div class="modal fade" id="modalPengaturanKartu" tabindex="-1" aria-labelledby="modalPengaturanKartuLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-light py-3">
          <h5 class="modal-title fw-bold fs-6 d-flex align-items-center" id="modalPengaturanKartuLabel">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-2 text-primary">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <circle cx="12" cy="12" r="3" stroke-width="2"/>
            </svg>
            Pengaturan Desain &amp; Kop Kartu Ujian
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form id="formCetakGlobal" action="<?= base_url('admin/cetak/kartu/print') ?>" method="GET">
            <div class="row g-3">
              <div class="col-12">
                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 mb-1">Kop &amp; Judul Kartu</span>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Judul Baris 1 (Header)</label>
                <input type="text" name="judul1" id="cfg_judul1" class="form-control form-control-sm" value="KARTU PESERTA">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Judul Baris 2 (Nama Ujian)</label>
                <input type="text" name="judul2" id="cfg_judul2" class="form-control form-control-sm" value="TES KEMAMPUAN AKADEMIK SMP/MTS">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Judul Baris 3 (Tahun / Periode)</label>
                <input type="text" name="judul3" id="cfg_judul3" class="form-control form-control-sm" value="TAHUN 2026">
              </div>

              <div class="col-12 pt-2">
                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 mb-1">Identitas &amp; Pengesahan</span>
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold">Kota</label>
                <input type="text" name="kota" id="cfg_kota" class="form-control form-control-sm" value="<?= esc($settings['kota'] ?? 'Banyuwangi') ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold">Tanggal Cetak</label>
                <input type="text" name="tanggal" id="cfg_tanggal" class="form-control form-control-sm" value="<?= esc($tanggal) ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold">Kepala Satuan Pendidikan</label>
                <input type="text" name="kepala_sekolah" id="cfg_kepala" class="form-control form-control-sm" value="<?= esc($settings['kepala_sekolah'] ?? 'DIDIK PURWANTO, S.Pd') ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold">NIP Kepala Satuan Pendidikan</label>
                <input type="text" name="nip_kepala_sekolah" id="cfg_nip" class="form-control form-control-sm" value="<?= esc($settings['nip_kepala_sekolah'] ?? '-') ?>">
              </div>

              <div class="col-12 pt-2">
                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 mb-1">Format Nomor Peserta</span>
              </div>
              <div class="col-md-12">
                <select name="format_nomor" id="cfg_format" class="form-select form-select-sm">
                  <option value="tka" selected>Format Standar Ujian Resmi (T2-26-05-34-1040-0001-8)</option>
                  <option value="nisn">Nomor NISN Siswa</option>
                  <option value="nis">Nomor Induk Siswa (NIS)</option>
                </select>
                <div class="form-text small text-muted">Format standar menghasilkan nomor kode peserta ujian seperti pada dokumen lampiran resmi kartu.pdf.</div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-primary btn-sm px-4" id="btnSimpanPengaturan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14" class="me-1">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Pengaturan
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab Navigasi: Cetak per Ujian & Cetak per Kelas -->
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-bottom-0 pb-0">
        <ul class="nav nav-tabs card-header-tabs" id="cetakTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="ujian-tab" data-bs-toggle="tab" data-bs-target="#tabUjian" type="button" role="tab">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              Cetak Berdasarkan Jadwal Ujian
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="kelas-tab" data-bs-toggle="tab" data-bs-target="#tabKelas" type="button" role="tab">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              Cetak Berdasarkan Kelas
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body pt-3">
        <div class="tab-content" id="cetakTabContent">
          <!-- Tab 1: Cetak Berdasarkan Ujian -->
          <div class="tab-pane fade show active" id="tabUjian" role="tabpanel">
            <?php if (empty($ujianList)): ?>
              <p class="text-muted text-center py-4">Belum ada jadwal ujian yang terdaftar.</p>
            <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle" id="tblUjianKartu">
                <thead class="table-light">
                  <tr>
                    <th style="width: 50px;">#</th>
                    <th>Nama Ujian</th>
                    <th>Mata Pelajaran</th>
                    <th>Jadwal Pelaksanaan</th>
                    <th>Status</th>
                    <th class="text-end" style="width: 140px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($ujianList as $i => $u): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td class="fw-semibold text-dark"><?= esc($u['nama']) ?></td>
                    <td><?= esc($u['nama_mapel'] ?? '-') ?></td>
                    <td>
                      <?= $u['mulai_at'] ? date('d M Y H:i', strtotime($u['mulai_at'])) : '-' ?>
                      <?= $u['selesai_at'] ? ' &ndash; ' . date('H:i', strtotime($u['selesai_at'])) : '' ?>
                    </td>
                    <td>
                      <?php
                      $cls = ['berlangsung' => 'bg-success', 'selesai' => 'bg-secondary', 'terjadwal' => 'bg-primary', 'draf' => 'bg-warning text-dark', 'dibatalkan' => 'bg-danger'];
                      $badgeClass = $cls[$u['status']] ?? 'bg-light text-dark';
                      ?>
                      <span class="badge <?= $badgeClass ?>"><?= esc($u['status']) ?></span>
                    </td>
                    <td class="text-end">
                      <button type="button" class="btn btn-sm btn-outline-primary btn-cetak-ujian"
                              data-id="<?= $u['id'] ?>"
                              data-nama="<?= esc($u['nama']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14" class="me-1">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/>
                        </svg>
                        Cetak Kartu
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php endif; ?>
          </div>

          <!-- Tab 2: Cetak Berdasarkan Kelas -->
          <div class="tab-pane fade" id="tabKelas" role="tabpanel">
            <?php if (empty($kelasList)): ?>
              <p class="text-muted text-center py-4">Belum ada data kelas yang terdaftar.</p>
            <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle" id="tblKelasKartu">
                <thead class="table-light">
                  <tr>
                    <th style="width: 50px;">#</th>
                    <th>Nama Kelas</th>
                    <th>Tingkat</th>
                    <th>Jumlah Siswa</th>
                    <th class="text-end" style="width: 140px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($kelasList as $i => $k): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td class="fw-semibold text-dark"><?= esc($k['nama']) ?></td>
                    <td><span class="badge bg-light text-dark border">Tingkat <?= esc($k['tingkat']) ?></span></td>
                    <td><strong><?= esc($k['jumlah_siswa']) ?></strong> Siswa</td>
                    <td class="text-end">
                      <button type="button" class="btn btn-sm btn-outline-primary btn-cetak-kelas"
                              data-id="<?= $k['id'] ?>"
                              data-nama="<?= esc($k['nama']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14" class="me-1">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/>
                        </svg>
                        Cetak Kelas
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
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  if ($.fn.DataTable) {
    $('#tblUjianKartu').DataTable({
      language: { search: 'Cari Ujian:', lengthMenu: 'Tampilkan _MENU_ data', info: 'Menampilkan _START_–_END_ dari _TOTAL_', paginate: { next: '›', previous: '‹' } }
    });
    $('#tblKelasKartu').DataTable({
      language: { search: 'Cari Kelas:', lengthMenu: 'Tampilkan _MENU_ data', info: 'Menampilkan _START_–_END_ dari _TOTAL_', paginate: { next: '›', previous: '‹' } }
    });
  }

  // Muat preferensi pengaturan kartu dari localStorage jika ada
  try {
    const saved = localStorage.getItem('cbt_kartu_cfg');
    if (saved) {
      const cfg = JSON.parse(saved);
      if (cfg.judul1) $('#cfg_judul1').val(cfg.judul1);
      if (cfg.judul2) $('#cfg_judul2').val(cfg.judul2);
      if (cfg.judul3) $('#cfg_judul3').val(cfg.judul3);
      if (cfg.kota) $('#cfg_kota').val(cfg.kota);
      if (cfg.tanggal) $('#cfg_tanggal').val(cfg.tanggal);
      if (cfg.kepala_sekolah) $('#cfg_kepala').val(cfg.kepala_sekolah);
      if (cfg.nip_kepala_sekolah) $('#cfg_nip').val(cfg.nip_kepala_sekolah);
      if (cfg.format_nomor) $('#cfg_format').val(cfg.format_nomor);
    }
  } catch (e) {}

  // Tombol Simpan Pengaturan di Modal
  $('#btnSimpanPengaturan').on('click', function() {
    const form = $('#formCetakGlobal');
    const data = {
      judul1: $('#cfg_judul1').val(),
      judul2: $('#cfg_judul2').val(),
      judul3: $('#cfg_judul3').val(),
      kota: $('#cfg_kota').val(),
      tanggal: $('#cfg_tanggal').val(),
      kepala_sekolah: $('#cfg_kepala').val(),
      nip_kepala_sekolah: $('#cfg_nip').val(),
      format_nomor: $('#cfg_format').val()
    };
    try {
      localStorage.setItem('cbt_kartu_cfg', JSON.stringify(data));
    } catch (e) {}

    // Tutup modal
    const modalEl = document.getElementById('modalPengaturanKartu');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) {
      modalInstance.hide();
    }

    if (typeof showToast === 'function') {
      showToast('success', 'Pengaturan kartu berhasil disimpan.');
    }
  });

  // Handle Cetak Semua Siswa (Sama Tab)
  $('#btnCetakSemua').on('click', function() {
    const form = $('#formCetakGlobal');
    const params = new URLSearchParams(form.serialize());
    const printUrl = '<?= base_url('admin/cetak/kartu/print') ?>?' + params.toString();
    // Buka di tab yang sama (jangan menambah tab baru)
    window.location.href = printUrl;
  });

  // Handle klik tombol Cetak Ujian (Sama Tab)
  $('.btn-cetak-ujian').on('click', function() {
    const ujianId = $(this).data('id');
    const namaUjian = $(this).data('nama');
    
    const form = $('#formCetakGlobal');
    const params = new URLSearchParams(form.serialize());
    params.set('ujian_id', ujianId);
    if (!params.get('judul2') || params.get('judul2') === 'TES KEMAMPUAN AKADEMIK SMP/MTS') {
      params.set('judul2', namaUjian.toUpperCase());
    }

    const printUrl = '<?= base_url('admin/cetak/kartu/print') ?>?' + params.toString();
    // Buka di tab yang sama (jangan menambah tab baru)
    window.location.href = printUrl;
  });

  // Handle klik tombol Cetak Kelas (Sama Tab)
  $('.btn-cetak-kelas').on('click', function() {
    const kelasId = $(this).data('id');
    const namaKelas = $(this).data('nama');
    
    const form = $('#formCetakGlobal');
    const params = new URLSearchParams(form.serialize());
    params.set('kelas_id', kelasId);

    const printUrl = '<?= base_url('admin/cetak/kartu/print') ?>?' + params.toString();
    // Buka di tab yang sama (jangan menambah tab baru)
    window.location.href = printUrl;
  });
});
</script>
<?= $this->endSection() ?>

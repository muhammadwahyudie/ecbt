<?= $this->extend('layout/ujian') ?>

<?= $this->section('styles') ?>
<style>
  .question-pane {
    background: #FFFFFF;
    border: 1px solid #E1E8EB;
    border-radius: 14px;
    padding: 2rem;
    min-height: 520px;
  }
  .opsi-card {
    border: 1.5px solid #E1E8EB;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.85rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    background: #FFFFFF;
    transition: all 0.15s ease;
  }
  .opsi-card:hover {
    border-color: #0E7C86;
    background-color: #F8FAFC;
  }
  .opsi-card.selected {
    border-color: #0E7C86;
    background-color: #F0FDFA;
    font-weight: 600;
  }
  .opsi-letter {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #E8EEF0;
    color: #10222B;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .opsi-card.selected .opsi-letter {
    background: #0E7C86;
    color: #FFFFFF;
  }
  .nav-palette-card {
    background: #FFFFFF;
    border: 1px solid #E1E8EB;
    border-radius: 14px;
    padding: 1.5rem;
  }
  .nav-palette-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">

  <!-- ─── Area Kiri: Soal Aktif & Opsi Jawaban ───────────────────────── -->
  <div class="col-12 col-lg-8 col-xl-9">
    <div class="question-pane shadow-sm d-flex flex-column justify-content-between">

      <!-- Atas: Indikator Nomor & Bobot -->
      <div>
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
          <div class="d-flex align-items-center gap-2">
            <span class="fs-5 fw-bold" style="font-family: 'Sora', sans-serif;">
              Soal Nomor <span id="displayNomorSoal">1</span>
            </span>
            <span class="badge bg-light text-secondary border fs-6" id="displayTipeSoal">Pilihan Ganda</span>
          </div>
          <span class="badge bg-light text-muted border">Bobot: <span id="displayBobotSoal">1.0</span></span>
        </div>

        <!-- Wadah Soal-Soal (Ditampilkan bergantian via JS) -->
        <div id="containerSoalList">
          <?php foreach ($soalList as $idx => $s): ?>
            <?php 
              $soalNum = $idx + 1;
              $isCurrent = ($soalNum === 1);
            ?>
            <div class="single-soal-wrapper" id="soal_wrapper_<?= $soalNum ?>" 
                 data-nomor="<?= $soalNum ?>"
                 data-soal-id="<?= $s['id'] ?>"
                 data-tipe="<?= $s['tipe'] ?>"
                 data-bobot="<?= $s['bobot'] ?>"
                 style="<?= $isCurrent ? '' : 'display: none;' ?>">

              <!-- Teks Soal -->
              <div class="soal-content fs-5 text-dark mb-4">
                <?= $s['pertanyaan'] ?>
              </div>

              <!-- Input Jawaban berdasarkan Tipe -->
              <div class="soal-opsi-container mb-4">

                <!-- 1. Pilihan Ganda -->
                <?php if ($s['tipe'] === 'pilihan_ganda'): ?>
                  <?php foreach ($s['opsi'] as $op): ?>
                    <?php $isSelected = ($s['jawaban_siswa'] === $op['label']); ?>
                    <label class="opsi-card <?= $isSelected ? 'selected' : '' ?>">
                      <input type="radio" name="jawaban_soal_<?= $s['id'] ?>" value="<?= esc($op['label']) ?>" 
                             class="d-none input-jawaban-pg" 
                             data-soal-id="<?= $s['id'] ?>" 
                             data-nomor="<?= $soalNum ?>"
                             <?= $isSelected ? 'checked' : '' ?>>
                      <span class="opsi-letter"><?= esc($op['label']) ?></span>
                      <div class="flex-grow-1 fs-6"><?= esc($op['isi']) ?></div>
                    </label>
                  <?php endforeach; ?>

                <!-- 2. Pilihan Ganda Kompleks -->
                <?php elseif ($s['tipe'] === 'pilihan_ganda_kompleks'): ?>
                  <?php 
                    $selectedArray = explode(',', $s['jawaban_siswa'] ?? '');
                  ?>
                  <?php foreach ($s['opsi'] as $op): ?>
                    <?php $isSelected = in_array($op['label'], $selectedArray); ?>
                    <label class="opsi-card <?= $isSelected ? 'selected' : '' ?>">
                      <input type="checkbox" name="jawaban_soal_<?= $s['id'] ?>[]" value="<?= esc($op['label']) ?>" 
                             class="form-check-input mt-0 me-2 input-jawaban-kompleks" 
                             data-soal-id="<?= $s['id'] ?>" 
                             data-nomor="<?= $soalNum ?>"
                             <?= $isSelected ? 'checked' : '' ?>>
                      <span class="opsi-letter"><?= esc($op['label']) ?></span>
                      <div class="flex-grow-1 fs-6"><?= esc($op['isi']) ?></div>
                    </label>
                  <?php endforeach; ?>

                <!-- 3. Benar / Salah -->
                <?php elseif ($s['tipe'] === 'benar_salah'): ?>
                  <?php
                    $bsMap = [];
                    if (!empty($s['jawaban_siswa'])) {
                      foreach (explode(',', $s['jawaban_siswa']) as $pair) {
                        $p = explode(':', $pair);
                        if (count($p) === 2) { $bsMap[$p[0]] = $p[1]; }
                      }
                    }
                  ?>
                  <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Pernyataan</th>
                          <th class="text-center" style="width: 100px;">Benar</th>
                          <th class="text-center" style="width: 100px;">Salah</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($s['opsi'] as $oIdx => $op): ?>
                          <?php $rowVal = $bsMap[$op['label']] ?? null; ?>
                          <tr>
                            <td><?= esc($op['isi']) ?></td>
                            <td class="text-center">
                              <input type="radio" name="bs_row_<?= $s['id'] ?>_<?= $op['label'] ?>" value="1" 
                                     class="form-check-input input-jawaban-bs" 
                                     data-soal-id="<?= $s['id'] ?>"
                                     data-nomor="<?= $soalNum ?>"
                                     data-row-label="<?= $op['label'] ?>"
                                     <?= $rowVal === '1' ? 'checked' : '' ?>>
                            </td>
                            <td class="text-center">
                              <input type="radio" name="bs_row_<?= $s['id'] ?>_<?= $op['label'] ?>" value="0" 
                                     class="form-check-input input-jawaban-bs" 
                                     data-soal-id="<?= $s['id'] ?>"
                                     data-nomor="<?= $soalNum ?>"
                                     data-row-label="<?= $op['label'] ?>"
                                     <?= $rowVal === '0' ? 'checked' : '' ?>>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>

                <!-- 4. Isian Singkat -->
                <?php elseif ($s['tipe'] === 'isian_singkat'): ?>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Jawaban Singkat:</label>
                    <input type="text" class="form-control form-control-lg input-jawaban-isian" 
                           data-soal-id="<?= $s['id'] ?>" 
                           data-nomor="<?= $soalNum ?>"
                           value="<?= esc($s['jawaban_siswa'] ?? '') ?>" 
                           placeholder="Ketikkan jawaban Anda di sini...">
                  </div>

                <!-- 5. Uraian -->
                <?php elseif ($s['tipe'] === 'uraian'): ?>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Jawaban Uraian / Esai:</label>
                    <textarea class="form-control input-jawaban-uraian" rows="7" 
                              data-soal-id="<?= $s['id'] ?>" 
                              data-nomor="<?= $soalNum ?>"
                              placeholder="Ketikkan uraian penjelasan lengkap Anda di sini..."><?= esc($s['jawaban_siswa'] ?? '') ?></textarea>
                  </div>
                <?php endif; ?>

              </div><!-- .soal-opsi-container -->
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Bawah: Tombol Navigasi Soal -->
      <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-4">
        <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-semibold" id="btnPrev">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
          </svg>
          Sebelumnya
        </button>

        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" id="checkRaguRagu" style="cursor: pointer;">
          <label class="form-check-label fw-bold text-warning" for="checkRaguRagu" style="cursor: pointer;">
            Ragu-ragu
          </label>
        </div>

        <button type="button" class="btn btn-primary px-4 py-2 fw-semibold" id="btnNext">
          Selanjutnya
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="ms-1">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- ─── Area Kanan: Palet Nomor Navigasi ──────────────────────────── -->
  <div class="col-12 col-lg-4 col-xl-3">
    <div class="nav-palette-card shadow-sm sticky-top" style="top: 80px;">
      <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Nomor Soal</h6>
      
      <div class="nav-palette-grid mb-4">
        <?php foreach ($soalList as $idx => $s): ?>
          <?php
            $num = $idx + 1;
            $hasAnswer = !empty($s['jawaban_siswa']);
            $isRagu = !empty($s['ragu_siswa']);

            $cls = '';
            if ($num === 1) $cls .= ' active';
            if ($isRagu) {
              $cls .= ' ragu';
            } elseif ($hasAnswer) {
              $cls .= ' dijawab';
            }
          ?>
          <button type="button" class="nav-nomor-btn <?= $cls ?>" 
                  id="btn_nav_<?= $num ?>" 
                  data-nomor="<?= $num ?>">
            <?= $num ?>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Legenda Palet -->
      <div class="d-flex flex-column gap-2 small text-secondary border-top pt-3">
        <div class="d-flex align-items-center gap-2">
          <span class="nav-nomor-btn dijawab" style="width: 22px; height: 22px; font-size: 10px;">✓</span>
          <span>Sudah Dijawab</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="nav-nomor-btn ragu" style="width: 22px; height: 22px; font-size: 10px;">?</span>
          <span>Ragu-ragu</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="nav-nomor-btn" style="width: 22px; height: 22px; font-size: 10px;"></span>
          <span>Belum Dijawab</span>
        </div>
      </div>

      <div class="d-grid mt-4">
        <button type="button" class="btn btn-danger fw-bold py-2" id="btnSelesaiUjianPalette">
          Selesaikan Ujian
        </button>
      </div>
    </div>
  </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// State pengerjaan
let currentNomor = 1;
const totalSoal = <?= count($soalList) ?>;
const ujianId   = <?= $ujian['id'] ?>;
let sisaDetik   = <?= $sisaDetik ?>;
let pelanggaranCount = <?= $peserta['pelanggaran'] ?? 0 ?>;
const maxPelanggaran = <?= $ujian['batas_pelanggaran'] ?? 3 ?>;

// Simpan state ragu per soal
const stateRagu = {};
<?php foreach ($soalList as $idx => $s): ?>
  stateRagu[<?= $idx + 1 ?>] = <?= !empty($s['ragu_siswa']) ? 'true' : 'false' ?>;
<?php endforeach; ?>

// ─── 1. Countdown Timer ─────────────────────────────────────────────
function formatTimer(sec) {
  const h = Math.floor(sec / 3600);
  const m = Math.floor((sec % 3600) / 60);
  const s = sec % 60;
  return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
}

const timerInterval = setInterval(() => {
  sisaDetik--;
  if (sisaDetik <= 0) {
    clearInterval(timerInterval);
    document.getElementById('examCountdown').textContent = '00:00:00';
    autoSubmitWaktuHabis();
    return;
  }

  document.getElementById('examCountdown').textContent = formatTimer(sisaDetik);

  if (sisaDetik <= 300) { // < 5 menit
    document.getElementById('countdownBox').classList.add('timer-warning');
  }
}, 1000);

// Format tampilan awal
document.getElementById('examCountdown').textContent = formatTimer(sisaDetik);

// ─── 2. Navigasi Soal ──────────────────────────────────────────────
function showSoal(nomor) {
  if (nomor < 1 || nomor > totalSoal) return;
  currentNomor = nomor;

  $('.single-soal-wrapper').hide();
  const wrapper = $(`#soal_wrapper_${nomor}`);
  wrapper.show();

  $('#displayNomorSoal').text(nomor);
  $('#displayTipeSoal').text(wrapper.data('tipe').replace(/_/g, ' ').toUpperCase());
  $('#displayBobotSoal').text(wrapper.data('bobot'));

  // Update check ragu
  $('#checkRaguRagu').prop('checked', !!stateRagu[nomor]);

  // Update active state di palet
  $('.nav-nomor-btn').removeClass('active');
  $(`#btn_nav_${nomor}`).addClass('active');

  // Disable / Enable tombol
  $('#btnPrev').prop('disabled', nomor === 1);
  if (nomor === totalSoal) {
    $('#btnNext').text('Selesai').removeClass('btn-primary').addClass('btn-danger');
  } else {
    $('#btnNext').html('Selanjutnya <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="ms-1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>').removeClass('btn-danger').addClass('btn-primary');
  }
}

$('#btnPrev').on('click', () => showSoal(currentNomor - 1));
$('#btnNext').on('click', () => {
  if (currentNomor === totalSoal) {
    konfirmasiSelesai();
  } else {
    showSoal(currentNomor + 1);
  }
});
$('.nav-nomor-btn').on('click', function() {
  showSoal($(this).data('nomor'));
});

// ─── 3. Autosave Handler ────────────────────────────────────────────
function triggerAutosave(soalId, nomor, jawaban) {
  const ragu = stateRagu[nomor] ? 1 : 0;
  $('#saveStatusIndicator').html('<span class="spinner-border spinner-border-sm text-secondary me-1"></span> Menyimpan...').removeClass('text-success').addClass('text-secondary');

  $.ajax({
    url: '<?= base_url('siswa/ujian/simpan-jawaban') ?>',
    type: 'POST',
    data: {
      ujian_id: ujianId,
      soal_id: soalId,
      jawaban: jawaban,
      ragu: ragu
    },
    success: function(res) {
      $('#saveStatusIndicator').html('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tersimpan ✓').removeClass('text-secondary').addClass('text-success');

      // Update warna palet
      const btn = $(`#btn_nav_${nomor}`);
      if (ragu) {
        btn.removeClass('dijawab').addClass('ragu');
      } else if (jawaban !== '' && jawaban !== null) {
        btn.removeClass('ragu').addClass('dijawab');
      } else {
        btn.removeClass('dijawab ragu');
      }
    },
    error: function() {
      $('#saveStatusIndicator').html('⚠ Gagal menyimpan, mencoba lagi...').removeClass('text-success').addClass('text-danger');
    }
  });
}

// Opsi Pilihan Ganda Click
$(document).on('change', '.input-jawaban-pg', function() {
  const soalId = $(this).data('soal-id');
  const nomor  = $(this).data('nomor');
  const val    = $(this).val();

  $(`#soal_wrapper_${nomor} .opsi-card`).removeClass('selected');
  $(this).closest('.opsi-card').addClass('selected');

  triggerAutosave(soalId, nomor, val);
});

// Opsi PG Kompleks Click
$(document).on('change', '.input-jawaban-kompleks', function() {
  const soalId = $(this).data('soal-id');
  const nomor  = $(this).data('nomor');

  const selected = [];
  $(`#soal_wrapper_${nomor} .input-jawaban-kompleks:checked`).each(function() {
    selected.push($(this).val());
  });

  if ($(this).is(':checked')) {
    $(this).closest('.opsi-card').addClass('selected');
  } else {
    $(this).closest('.opsi-card').removeClass('selected');
  }

  triggerAutosave(soalId, nomor, selected.join(','));
});

// Opsi Benar/Salah Click
$(document).on('change', '.input-jawaban-bs', function() {
  const soalId = $(this).data('soal-id');
  const nomor  = $(this).data('nomor');

  const rows = [];
  $(`#soal_wrapper_${nomor} .input-jawaban-bs:checked`).each(function() {
    rows.push($(this).data('row-label') + ':' + $(this).val());
  });

  triggerAutosave(soalId, nomor, rows.join(','));
});

// Isian Singkat & Uraian (Debounce input)
let debounceTimer;
$(document).on('input', '.input-jawaban-isian, .input-jawaban-uraian', function() {
  const soalId = $(this).data('soal-id');
  const nomor  = $(this).data('nomor');
  const val    = $(this).val();

  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    triggerAutosave(soalId, nomor, val);
  }, 600);
});

// Toggle Ragu-ragu
$('#checkRaguRagu').on('change', function() {
  const isRagu = this.checked;
  stateRagu[currentNomor] = isRagu;

  const wrapper = $(`#soal_wrapper_${currentNomor}`);
  const soalId = wrapper.data('soal-id');
  const tipe   = wrapper.data('tipe');

  // Dapatkan nilai jawaban saat ini
  let curVal = '';
  if (tipe === 'pilihan_ganda') {
    curVal = wrapper.find('.input-jawaban-pg:checked').val() || '';
  } else if (tipe === 'pilihan_ganda_kompleks') {
    const arr = [];
    wrapper.find('.input-jawaban-kompleks:checked').each(function() { arr.push($(this).val()); });
    curVal = arr.join(',');
  } else if (tipe === 'isian_singkat') {
    curVal = wrapper.find('.input-jawaban-isian').val() || '';
  } else if (tipe === 'uraian') {
    curVal = wrapper.find('.input-jawaban-uraian').val() || '';
  }

  triggerAutosave(soalId, currentNomor, curVal);
});

// ─── 4. Anti-Cheat & Deteksi Pelanggaran ─────────────────────────────
function laporPelanggaran(jenis, keterangan) {
  $.ajax({
    url: '<?= base_url('siswa/ujian/catat-pelanggaran') ?>',
    type: 'POST',
    data: {
      ujian_id: ujianId,
      jenis: jenis,
      keterangan: keterangan
    },
    success: function(res) {
      pelanggaranCount = res.total;
      if (res.terkunci) {
        Swal.fire({
          icon: 'error',
          title: 'Akun Ujian Terkunci!',
          text: `Anda telah melakukan pelanggaran keluar tab/fullscreen sebanyak ${res.total} kali (melebihi batas maksimal). Ujian terkunci.`,
          allowOutsideClick: false,
          confirmButtonColor: '#DC2626',
          confirmButtonText: 'Tutup'
        }).then(() => {
          window.location.reload();
        });
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Peringatan Pelanggaran!',
          html: `Terdeteksi <strong>${jenis.replace(/_/g, ' ')}</strong>.<br>Pelanggaran ke-<strong>${res.total}</strong> dari maksimal <strong>${maxPelanggaran}</strong> kali.<br><span class="text-danger small">Sisa toleransi: ${res.sisa} kali sebelum ujian Anda dikunci otomatis!</span>`,
          confirmButtonColor: '#0E7C86',
          confirmButtonText: 'Saya Mengerti, Kembali Ujian'
        });
      }
    }
  });
}

// Deteksi pindah tab
document.addEventListener('visibilitychange', function() {
  if (document.hidden) {
    laporPelanggaran('keluar_tab', 'Membuka tab atau jendela lain');
  }
});

// ─── 5. Konfirmasi Selesai Ujian ────────────────────────────────────
function konfirmasiSelesai() {
  const dijawab = $('.nav-nomor-btn.dijawab').length;
  const ragu = $('.nav-nomor-btn.ragu').length;
  const belum = totalSoal - dijawab - ragu;

  Swal.fire({
    title: 'Kumpulkan Jawaban Ujian?',
    html: `
      <div class="text-start py-2">
        <p class="mb-2">Periksa ringkasan jawaban Anda sebelum mengumpulkan:</p>
        <ul class="list-group list-group-flush small mb-3">
          <li class="list-group-item d-flex justify-content-between"><span>Total Soal:</span> <strong>${totalSoal} butir</strong></li>
          <li class="list-group-item d-flex justify-content-between text-success"><span>Sudah Dijawab:</span> <strong>${dijawab}</strong></li>
          <li class="list-group-item d-flex justify-content-between text-warning"><span>Ragu-ragu:</span> <strong>${ragu}</strong></li>
          <li class="list-group-item d-flex justify-content-between text-danger"><span>Belum Dijawab:</span> <strong>${belum}</strong></li>
        </ul>
        <small class="text-muted">Setelah mengumpulkan, Anda tidak dapat mengubah jawaban lagi.</small>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#0E7C86',
    cancelButtonColor: '#64748B',
    confirmButtonText: 'Ya, Selesaikan Ujian',
    cancelButtonText: 'Periksa Kembali',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      submitUjianSelesai();
    }
  });
}

function submitUjianSelesai() {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '<?= base_url('siswa/ujian/selesai/' . $ujian['id']) ?>';

  const csrf = document.createElement('input');
  csrf.type = 'hidden';
  csrf.name = 'csrf_test_name';
  csrf.value = csrfToken;
  form.appendChild(csrf);

  document.body.appendChild(form);
  form.submit();
}

function autoSubmitWaktuHabis() {
  Swal.fire({
    icon: 'info',
    title: 'Waktu Ujian Telah Habis!',
    text: 'Sistem sedang menyimpan jawaban Anda dan mengumpulkan lembar ujian secara otomatis...',
    allowOutsideClick: false,
    showConfirmButton: false,
    timer: 2000
  }).then(() => {
    submitUjianSelesai();
  });
}

$('#btnSelesaiUjianTop, #btnSelesaiUjianPalette').on('click', konfirmasiSelesai);

// Initial display
showSoal(1);
</script>
<?= $this->endSection() ?>

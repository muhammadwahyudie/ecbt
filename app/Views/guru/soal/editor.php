<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<!-- Quill.js CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet">
<!-- KaTeX CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<style>
  .ql-editor {
    min-height: 220px;
    font-size: 1rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .type-option-card {
    border: 2px solid #E1E8EB;
    border-radius: 12px;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .type-option-card:hover {
    border-color: #0E7C86;
  }
  .type-option-card.selected {
    border-color: #0E7C86;
    background-color: #F0FDFA;
  }

  /* Style Gambar dalam Editor */
  .ql-editor img {
    cursor: pointer;
    max-width: 100%;
    transition: outline 0.15s ease;
    border-radius: 4px;
  }
  .ql-editor img:hover {
    outline: 2px dashed #0E7C86;
  }
  .ql-editor img.img-selected {
    outline: 2px solid #0E7C86 !important;
  }

  /* Overlay Resizer Gambar */
  #quillImageResizer {
    position: absolute;
    display: none;
    pointer-events: none;
    z-index: 1050;
    box-sizing: border-box;
    border: 2px solid #0E7C86;
    border-radius: 4px;
  }
  #quillImageResizer .resize-handle {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #FFFFFF;
    border: 2px solid #0E7C86;
    border-radius: 50%;
    pointer-events: auto;
    box-shadow: 0 1px 4px rgba(0,0,0,0.25);
  }
  #quillImageResizer .handle-nw { top: -6px; left: -6px; cursor: nwse-resize; }
  #quillImageResizer .handle-ne { top: -6px; right: -6px; cursor: nesw-resize; }
  #quillImageResizer .handle-sw { bottom: -6px; left: -6px; cursor: nesw-resize; }
  #quillImageResizer .handle-se { bottom: -6px; right: -6px; cursor: nwse-resize; }

  /* Toolbar Aksi Gambar */
  #quillImageToolbar {
    position: absolute;
    display: none;
    z-index: 1051;
    background: #0F2229;
    color: #FFFFFF;
    border-radius: 8px;
    padding: 5px 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    align-items: center;
    gap: 4px;
    font-size: 12px;
    white-space: nowrap;
  }
  #quillImageToolbar button {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.25);
    color: #FFFFFF;
    border-radius: 5px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  #quillImageToolbar button:hover {
    background: rgba(255,255,255,0.25);
    border-color: #FFFFFF;
  }
  #quillImageToolbar button.btn-danger-tool:hover {
    background: #DC2626;
    border-color: #DC2626;
  }
  #quillImageToolbar .tool-divider {
    width: 1px;
    height: 16px;
    background: rgba(255,255,255,0.25);
    margin: 0 3px;
  }
  #quillImageToolbar .size-badge {
    color: #2DD4BF;
    font-size: 11px;
    font-weight: 600;
    margin-right: 4px;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('header_action') ?>
<a href="<?= base_url('guru/paket-soal/' . $paket['id']) ?>" class="btn btn-outline-secondary btn-sm">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
  </svg>
  Batal & Kembali
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php 
  $isEdit = !empty($soal);
  $actionUrl = $isEdit 
    ? base_url("guru/soal/{$paket['id']}/update/{$soal['id']}") 
    : base_url("guru/soal/{$paket['id']}/simpan");
  $curTipe = $soal['tipe'] ?? 'pilihan_ganda';
?>

<form action="<?= $actionUrl ?>" method="POST" id="formSoal">
  <?= csrf_field() ?>
  <input type="hidden" name="pertanyaan" id="inputPertanyaan">

  <div class="row g-4">
    <!-- Kolom Kiri: Pertanyaan & Opsi -->
    <div class="col-12 col-lg-8">
      <!-- 1. Tipe Soal Selection -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">1. Bentuk / Tipe Soal</h5>
          <small class="text-secondary">Pilih tipe evaluasi yang sesuai dengan capaian pembelajaran</small>
        </div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-6 col-md">
              <label class="type-option-card d-block <?= $curTipe === 'pilihan_ganda' ? 'selected' : '' ?>">
                <input type="radio" name="tipe" value="pilihan_ganda" class="d-none" <?= $curTipe === 'pilihan_ganda' ? 'checked' : '' ?>>
                <div class="fw-bold fs-6">Pilihan Ganda</div>
                <small class="text-muted">1 jawaban benar</small>
              </label>
            </div>
            <div class="col-6 col-md">
              <label class="type-option-card d-block <?= $curTipe === 'pilihan_ganda_kompleks' ? 'selected' : '' ?>">
                <input type="radio" name="tipe" value="pilihan_ganda_kompleks" class="d-none" <?= $curTipe === 'pilihan_ganda_kompleks' ? 'checked' : '' ?>>
                <div class="fw-bold fs-6">PG Kompleks</div>
                <small class="text-muted">> 1 jawaban benar</small>
              </label>
            </div>
            <div class="col-6 col-md">
              <label class="type-option-card d-block <?= $curTipe === 'benar_salah' ? 'selected' : '' ?>">
                <input type="radio" name="tipe" value="benar_salah" class="d-none" <?= $curTipe === 'benar_salah' ? 'checked' : '' ?>>
                <div class="fw-bold fs-6">Benar / Salah</div>
                <small class="text-muted">Matriks pernyataan</small>
              </label>
            </div>
            <div class="col-6 col-md">
              <label class="type-option-card d-block <?= $curTipe === 'isian_singkat' ? 'selected' : '' ?>">
                <input type="radio" name="tipe" value="isian_singkat" class="d-none" <?= $curTipe === 'isian_singkat' ? 'checked' : '' ?>>
                <div class="fw-bold fs-6">Isian Singkat</div>
                <small class="text-muted">Kata / angka pasti</small>
              </label>
            </div>
            <div class="col-6 col-md">
              <label class="type-option-card d-block <?= $curTipe === 'uraian' ? 'selected' : '' ?>">
                <input type="radio" name="tipe" value="uraian" class="d-none" <?= $curTipe === 'uraian' ? 'checked' : '' ?>>
                <div class="fw-bold fs-6">Uraian / Essay</div>
                <small class="text-muted">Koreksi manual guru</small>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Isi Teks Pertanyaan (Editor Quill) -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">2. Pertanyaan Soal</h5>
            <small class="text-secondary">Mendukung format teks, gambar, tabel, dan simbol matematika KaTeX</small>
          </div>
        </div>
        <div class="card-body p-0">
          <div id="editorPertanyaan">
            <?= $soal['pertanyaan'] ?? '' ?>
          </div>
        </div>
      </div>

      <!-- 3. Dynamic Section: Pilihan Jawaban / Kunci -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">3. Opsi Jawaban & Kunci</h5>
          <small class="text-secondary">Tentukan opsi pilihan dan tandai jawaban yang benar</small>
        </div>
        <div class="card-body">

          <!-- Section A: Pilihan Ganda -->
          <div id="section_pilihan_ganda" class="type-section" style="<?= $curTipe === 'pilihan_ganda' ? '' : 'display:none;' ?>">
            <p class="text-secondary small mb-3">Tuliskan opsi jawaban dan pilih tombol radio pada opsi yang merupakan kunci jawaban benar:</p>
            <?php 
              $labels = ['A', 'B', 'C', 'D', 'E'];
              $opsiMap = [];
              if ($isEdit && $curTipe === 'pilihan_ganda') {
                foreach ($opsi as $op) { $opsiMap[$op['label']] = $op; }
              }
            ?>
            <?php foreach ($labels as $idx => $label): ?>
              <?php 
                $val = $opsiMap[$label]['isi'] ?? '';
                $isCorrect = !empty($opsiMap[$label]['benar']);
              ?>
              <div class="input-group mb-2">
                <span class="input-group-text bg-white">
                  <input class="form-check-input mt-0" type="radio" name="pg_kunci" value="<?= $label ?>" <?= ($isCorrect || (!$isEdit && $label === 'A')) ? 'checked' : '' ?> title="Tandai Kunci Jawaban Benar">
                </span>
                <span class="input-group-text fw-bold"><?= $label ?></span>
                <input type="text" name="pg_opsi[]" class="form-control" placeholder="Pilihan jawaban <?= $label ?>" value="<?= esc($val) ?>">
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Section B: PG Kompleks -->
          <div id="section_pilihan_ganda_kompleks" class="type-section" style="<?= $curTipe === 'pilihan_ganda_kompleks' ? '' : 'display:none;' ?>">
            <p class="text-secondary small mb-3">Centang checkbox pada satu atau lebih pilihan yang merupakan jawaban benar:</p>
            <?php 
              $opsiKMap = [];
              if ($isEdit && $curTipe === 'pilihan_ganda_kompleks') {
                foreach ($opsi as $op) { $opsiKMap[$op['label']] = $op; }
              }
            ?>
            <?php foreach ($labels as $idx => $label): ?>
              <?php 
                $val = $opsiKMap[$label]['isi'] ?? '';
                $isCorrect = !empty($opsiKMap[$label]['benar']);
              ?>
              <div class="input-group mb-2">
                <span class="input-group-text bg-white">
                  <input class="form-check-input mt-0" type="checkbox" name="kompleks_kunci[]" value="<?= $label ?>" <?= $isCorrect ? 'checked' : '' ?> title="Centang jika Benar">
                </span>
                <span class="input-group-text fw-bold"><?= $label ?></span>
                <input type="text" name="kompleks_opsi[]" class="form-control" placeholder="Pilihan kompleks <?= $label ?>" value="<?= esc($val) ?>">
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Section C: Benar / Salah -->
          <div id="section_benar_salah" class="type-section" style="<?= $curTipe === 'benar_salah' ? '' : 'display:none;' ?>">
            <p class="text-secondary small mb-3">Tuliskan butir pernyataan dan tentukan status kebenarannya:</p>
            <?php for ($i = 0; $i < 3; $i++): ?>
              <?php
                $bsVal = ($isEdit && isset($opsi[$i])) ? $opsi[$i]['isi'] : '';
                $bsBenar = ($isEdit && isset($opsi[$i])) ? $opsi[$i]['benar'] : 1;
              ?>
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-light text-dark border"><?= $i + 1 ?></span>
                <input type="text" name="bs_pernyataan[]" class="form-control" placeholder="Pernyataan <?= $i + 1 ?>" value="<?= esc($bsVal) ?>">
                <select name="bs_kunci[]" class="form-select" style="width: 130px;">
                  <option value="1" <?= $bsBenar == 1 ? 'selected' : '' ?>>Benar</option>
                  <option value="0" <?= $bsBenar == 0 ? 'selected' : '' ?>>Salah</option>
                </select>
              </div>
            <?php endfor; ?>
          </div>

          <!-- Section D: Isian Singkat -->
          <div id="section_isian_singkat" class="type-section" style="<?= $curTipe === 'isian_singkat' ? '' : 'display:none;' ?>">
            <p class="text-secondary small mb-3">Tuliskan kata kunci atau angka jawaban yang diharapkan (sistem otomatis tidak sensitif huruf besar/kecil):</p>
            <?php
              $isianVal = ($isEdit && !empty($opsi)) ? $opsi[0]['isi'] : '';
            ?>
            <div class="mb-2">
              <label class="form-label fw-semibold">Kunci Jawaban Isian</label>
              <input type="text" name="isian_kunci[]" class="form-control" placeholder="Contoh: Fotosintesis, 1945, Oksigen" value="<?= esc($isianVal) ?>">
            </div>
          </div>

          <!-- Section E: Uraian -->
          <div id="section_uraian" class="type-section" style="<?= $curTipe === 'uraian' ? '' : 'display:none;' ?>">
            <p class="text-secondary small mb-3">Jawaban siswa berupa esai bebas yang akan diperiksa oleh guru melalui menu Koreksi Nilai.</p>
            <div class="mb-2">
              <label class="form-label fw-semibold">Rubrik / Pedoman Penilaian (Opsional)</label>
              <textarea name="pedoman" class="form-control" rows="4" placeholder="Tuliskan kata kunci yang harus ada, kriteria nilai maksimal, dan toleransi penjelasan..."><?= esc($soal['pedoman'] ?? '') ?></textarea>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Kolom Kanan: Pengaturan Bobot & Simpan -->
    <div class="col-12 col-lg-4">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Pengaturan Soal</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Urutan</label>
            <input type="number" name="urutan" class="form-control" value="<?= esc($soal['urutan'] ?? $nextUrutan ?? 1) ?>" min="1" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Bobot Soal</label>
            <input type="number" step="0.5" name="bobot" class="form-control" value="<?= esc($soal['bobot'] ?? '1.00') ?>" min="0.5" max="100" required>
            <small class="text-muted">Nilai bobot digunakan untuk perhitungan skor otomatis.</small>
          </div>

          <div class="alert alert-info py-2 small mb-4">
            Paket: <strong><?= esc($paket['nama'] ?? '-') ?></strong><br>
            Mapel: <?= esc($paket['nama_mapel'] ?? '-') ?>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg fw-bold">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20" class="me-1">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
              <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Butir Soal' ?>
            </button>
            <a href="<?= base_url('guru/paket-soal/' . $paket['id']) ?>" class="btn btn-light">
              Batal
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Quill JS & KaTeX -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>

<!-- Markup Overlay Resizer & Toolbar Gambar -->
<div id="quillImageResizer">
  <div class="resize-handle handle-nw" data-handle="nw"></div>
  <div class="resize-handle handle-ne" data-handle="ne"></div>
  <div class="resize-handle handle-sw" data-handle="sw"></div>
  <div class="resize-handle handle-se" data-handle="se"></div>
</div>
<div id="quillImageToolbar">
  <span class="size-badge" id="imgSizeBadge">100%</span>
  <button type="button" class="btn-resize-preset" data-size="25%">25%</button>
  <button type="button" class="btn-resize-preset" data-size="50%">50%</button>
  <button type="button" class="btn-resize-preset" data-size="75%">75%</button>
  <button type="button" class="btn-resize-preset" data-size="100%">100%</button>
  <div class="tool-divider"></div>
  <button type="button" class="btn-align-tool" data-align="left" title="Rata Kiri">◄ Kiri</button>
  <button type="button" class="btn-align-tool" data-align="center" title="Rata Tengah">▲ Tengah</button>
  <button type="button" class="btn-align-tool" data-align="right" title="Rata Kanan">Kanan ►</button>
  <div class="tool-divider"></div>
  <button type="button" class="btn-danger-tool" id="btnHapusGambar" title="Hapus Gambar">Hapus</button>
</div>

<script>
$(document).ready(function() {
  // ── Register Custom Image Blot agar atribut style & width tidak dihapus oleh Quill ──
  const BaseImage = Quill.import('formats/image');
  class ResizableImage extends BaseImage {
    static formats(domNode) {
      const formats = super.formats(domNode) || {};
      if (domNode.hasAttribute('style')) formats.style = domNode.getAttribute('style');
      if (domNode.hasAttribute('width')) formats.width = domNode.getAttribute('width');
      return formats;
    }
    format(name, value) {
      if (name === 'style') {
        if (value) this.domNode.setAttribute('style', value);
        else this.domNode.removeAttribute('style');
      } else if (name === 'width') {
        if (value) {
          this.domNode.setAttribute('width', value);
          this.domNode.style.width = value;
        } else {
          this.domNode.removeAttribute('width');
        }
      } else {
        super.format(name, value);
      }
    }
  }
  Quill.register(ResizableImage, true);

  // ── Inisialisasi Quill Editor ──
  const quill = new Quill('#editorPertanyaan', {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['blockquote', 'code-block'],
        ['link', 'image', 'video'],
        ['clean']
      ],
      // Nonaktifkan handler gambar bawaan Quill pada clipboard agar tidak duplikat
      clipboard: {
        matchers: [
          ['IMG', function(node, delta) {
            // Kembalikan delta kosong → Quill tidak memasukkan gambar sendiri
            // Gambar akan ditangani sepenuhnya oleh paste handler kustom di bawah
            return new Quill.import('delta')();
          }]
        ]
      }
    }
  });

  // ── 1. Handle Paste Gambar (Clipboard Screenshot / File / Web Eksternal) ──
  // Flag untuk mencegah double-insert jika event paste terpanggil lebih dari sekali
  let _handlingPaste = false;

  quill.root.addEventListener('paste', function(e) {
    if (_handlingPaste) return;
    const clipboardData = e.clipboardData || window.clipboardData;
    if (!clipboardData) return;

    // A. Gambar langsung di clipboard (Screenshot, Snipping Tool, copy file gambar)
    const items = clipboardData.items;
    if (items && items.length) {
      for (let i = 0; i < items.length; i++) {
        if (items[i].type.indexOf('image') !== -1) {
          const file = items[i].getAsFile();
          if (file) {
            e.preventDefault();
            e.stopPropagation();
            _handlingPaste = true;
            const reader = new FileReader();
            reader.onload = function(evt) {
              const range = quill.getSelection(true) || { index: quill.getLength() };
              quill.insertEmbed(range.index, 'image', evt.target.result, 'user');
              quill.setSelection(range.index + 1);
              _handlingPaste = false;
            };
            reader.readAsDataURL(file);
            return;
          }
        }
      }
    }

    // B. Gambar yang dicopy dari website/browser eksternal (tag <img> dalam HTML clipboard)
    const html = clipboardData.getData('text/html');
    if (html) {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const imgs = doc.querySelectorAll('img');
      if (imgs.length > 0) {
        e.preventDefault();
        e.stopPropagation();
        _handlingPaste = true;
        let insertIndex = (quill.getSelection(true) || { index: quill.getLength() }).index;
        imgs.forEach(img => {
          if (img.src) {
            quill.insertEmbed(insertIndex, 'image', img.src, 'user');
            insertIndex++;
          }
        });
        quill.setSelection(insertIndex);
        _handlingPaste = false;
        return;
      }
    }
  }, true); // capture phase agar lebih awal dari handler Quill internal

  // ── 2. Handle Drag & Drop File Gambar langsung ke Editor ──
  quill.root.addEventListener('drop', function(e) {
    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
      const file = e.dataTransfer.files[0];
      if (file.type.indexOf('image') !== -1) {
        e.preventDefault();
        const reader = new FileReader();
        reader.onload = function(evt) {
          const range = quill.getSelection(true) || { index: quill.getLength() };
          quill.insertEmbed(range.index, 'image', evt.target.result, 'user');
          quill.setSelection(range.index + 1);
        };
        reader.readAsDataURL(file);
      }
    }
  });

  // ── 3. Image Resizer & Alignment Controller ──
  let currentImg = null;
  let isDragging = false;
  let activeHandle = null;
  let startX, startY, startWidth, startHeight, aspectRatio;

  function positionOverlay() {
    if (!currentImg) {
      $('#quillImageResizer').hide();
      $('#quillImageToolbar').hide();
      return;
    }

    const rect = currentImg.getBoundingClientRect();
    const scrollX = window.scrollX || window.pageXOffset;
    const scrollY = window.scrollY || window.pageYOffset;

    $('#quillImageResizer').css({
      display: 'block',
      top: (rect.top + scrollY) + 'px',
      left: (rect.left + scrollX) + 'px',
      width: rect.width + 'px',
      height: rect.height + 'px'
    });

    const parentWidth = $(quill.root).width() || 600;
    const pct = Math.round((rect.width / parentWidth) * 100);
    $('#imgSizeBadge').text(Math.round(rect.width) + 'px (' + pct + '%)');

    // Tempatkan toolbar di atas gambar, atau di bawah jika mepet atas
    let tbTop = rect.top + scrollY - 42;
    if (tbTop < scrollY + 10) {
      tbTop = rect.bottom + scrollY + 8;
    }
    const tbLeft = Math.max(10, rect.left + scrollX + (rect.width / 2) - ($('#quillImageToolbar').outerWidth() / 2));

    $('#quillImageToolbar').css({
      display: 'flex',
      top: tbTop + 'px',
      left: tbLeft + 'px'
    });
  }

  // Klik gambar untuk menampilkan resizer
  $(quill.root).on('click', 'img', function(e) {
    e.stopPropagation();
    $('.ql-editor img').removeClass('img-selected');
    currentImg = this;
    $(currentImg).addClass('img-selected');
    positionOverlay();
  });

  // Klik di luar gambar untuk menutup overlay
  $(document).on('click', function(e) {
    if (!$(e.target).closest('#quillImageResizer, #quillImageToolbar, .ql-editor img').length) {
      if (currentImg) {
        $(currentImg).removeClass('img-selected');
        currentImg = null;
        positionOverlay();
      }
    }
  });

  // Pertahankan posisi overlay saat scroll atau window resize
  $(window).on('scroll resize', function() {
    if (currentImg) positionOverlay();
  });
  $(quill.root).on('scroll', function() {
    if (currentImg) positionOverlay();
  });

  // Mulai drag handle resizer
  $(document).on('mousedown', '#quillImageResizer .resize-handle', function(e) {
    if (!currentImg) return;
    e.preventDefault();
    e.stopPropagation();

    isDragging = true;
    activeHandle = $(this).data('handle');
    startX = e.clientX;
    startY = e.clientY;
    startWidth = currentImg.offsetWidth;
    startHeight = currentImg.offsetHeight;
    aspectRatio = startWidth / startHeight;

    $('body').css('user-select', 'none');
  });

  // Dragging mousemove
  $(document).on('mousemove', function(e) {
    if (!isDragging || !currentImg) return;

    const dx = e.clientX - startX;
    const maxWidth = $(quill.root).width() || 800;
    let newWidth = startWidth;

    if (activeHandle === 'se' || activeHandle === 'ne') {
      newWidth = startWidth + dx;
    } else if (activeHandle === 'sw' || activeHandle === 'nw') {
      newWidth = startWidth - dx;
    }

    newWidth = Math.max(50, Math.min(newWidth, maxWidth));
    currentImg.style.width = Math.round(newWidth) + 'px';
    currentImg.style.height = 'auto';

    positionOverlay();
  });

  // Selesai drag mouseup
  $(document).on('mouseup', function() {
    if (isDragging) {
      isDragging = false;
      activeHandle = null;
      $('body').css('user-select', '');
      if (currentImg) positionOverlay();
    }
  });

  // Preset Ukuran Cepat (25%, 50%, 75%, 100%)
  $(document).on('click', '.btn-resize-preset', function(e) {
    e.stopPropagation();
    if (!currentImg) return;
    const size = $(this).data('size');
    currentImg.style.width = size;
    currentImg.style.height = 'auto';
    positionOverlay();
  });

  // Posisi Rata Kiri, Tengah, Kanan
  $(document).on('click', '.btn-align-tool', function(e) {
    e.stopPropagation();
    if (!currentImg) return;
    const align = $(this).data('align');
    if (align === 'left') {
      currentImg.style.display = 'inline-block';
      currentImg.style.float = 'left';
      currentImg.style.margin = '0.5rem 1rem 0.5rem 0';
    } else if (align === 'center') {
      currentImg.style.display = 'block';
      currentImg.style.float = 'none';
      currentImg.style.margin = '0.75rem auto';
    } else if (align === 'right') {
      currentImg.style.display = 'inline-block';
      currentImg.style.float = 'right';
      currentImg.style.margin = '0.5rem 0 0.5rem 1rem';
    }
    positionOverlay();
  });

  // Hapus Gambar
  $(document).on('click', '#btnHapusGambar', function(e) {
    e.stopPropagation();
    if (!currentImg) return;
    currentImg.remove();
    currentImg = null;
    positionOverlay();
  });

  // ── Switch Tipe Soal ──
  $('input[name="tipe"]').on('change', function() {
    const tipe = $(this).val();
    $('.type-option-card').removeClass('selected');
    $(this).closest('.type-option-card').addClass('selected');

    $('.type-section').hide();
    $('#section_' + tipe).show();
  });

  // ── Submit handler: copy quill HTML to hidden input ──
  $('#formSoal').on('submit', function() {
    // Bersihkan overlay sebelum copy HTML
    $('.ql-editor img').removeClass('img-selected');
    $('#quillImageResizer').hide();
    $('#quillImageToolbar').hide();
    $('#inputPertanyaan').val(quill.root.innerHTML);
  });
});
</script>
<?= $this->endSection() ?>

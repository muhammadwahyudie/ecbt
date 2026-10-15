<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ─── Public ───────────────────────────────────────────────────────────────────
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// ─── Admin ────────────────────────────────────────────────────────────────────
$routes->group('/admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Guru
    $routes->get('guru', 'Admin\Guru::index');
    $routes->get('guru/data', 'Admin\Guru::data');
    $routes->post('guru/simpan', 'Admin\Guru::simpan');
    $routes->post('guru/update/(:num)', 'Admin\Guru::update/$1');
    $routes->match(['post', 'delete'], 'guru/hapus/(:num)', 'Admin\Guru::hapus/$1');
    $routes->post('guru/reset-password/(:num)', 'Admin\Guru::resetPassword/$1');
    $routes->get('guru/template', 'Admin\Guru::template');
    $routes->post('guru/impor', 'Admin\Guru::impor');

    // Siswa
    $routes->get('siswa', 'Admin\Siswa::index');
    $routes->get('siswa/data', 'Admin\Siswa::data');
    $routes->post('siswa/simpan', 'Admin\Siswa::simpan');
    $routes->post('siswa/update/(:num)', 'Admin\Siswa::update/$1');
    $routes->match(['post', 'delete'], 'siswa/hapus/(:num)', 'Admin\Siswa::hapus/$1');
    $routes->post('siswa/reset-password/(:num)', 'Admin\Siswa::resetPassword/$1');
    $routes->get('siswa/template', 'Admin\Siswa::template');
    $routes->post('siswa/impor', 'Admin\Siswa::impor');

    // Kelas
    $routes->get('kelas', 'Admin\Kelas::index');
    $routes->get('kelas/data', 'Admin\Kelas::data');
    $routes->post('kelas/simpan', 'Admin\Kelas::simpan');
    $routes->post('kelas/update/(:num)', 'Admin\Kelas::update/$1');
    $routes->match(['post', 'delete'], 'kelas/hapus/(:num)', 'Admin\Kelas::hapus/$1');
    $routes->get('kelas/siswa/(:num)', 'Admin\Kelas::siswa/$1');
    $routes->post('kelas/tambah-siswa', 'Admin\Kelas::tambahSiswa');
    $routes->match(['post', 'delete'], 'kelas/hapus-siswa/(:num)', 'Admin\Kelas::hapusSiswa/$1');

    // Mapel
    $routes->get('mapel', 'Admin\Mapel::index');
    $routes->get('mapel/data', 'Admin\Mapel::data');
    $routes->post('mapel/simpan', 'Admin\Mapel::simpan');
    $routes->post('mapel/update/(:num)', 'Admin\Mapel::update/$1');
    $routes->match(['post', 'delete'], 'mapel/hapus/(:num)', 'Admin\Mapel::hapus/$1');
    $routes->post('mapel/penugasan/simpan', 'Admin\Mapel::simpanPenugasan');
    $routes->match(['post', 'delete'], 'mapel/penugasan/hapus/(:num)', 'Admin\Mapel::hapusPenugasan/$1');

    // Ruang
    $routes->get('ruang', 'Admin\Ruang::index');
    $routes->get('ruang/data', 'Admin\Ruang::data');
    $routes->post('ruang/simpan', 'Admin\Ruang::simpan');
    $routes->post('ruang/update/(:num)', 'Admin\Ruang::update/$1');
    $routes->match(['post', 'delete'], 'ruang/hapus/(:num)', 'Admin\Ruang::hapus/$1');

    // Tahun Ajaran
    $routes->get('tahun-ajaran', 'Admin\TahunAjaran::index');
    $routes->post('tahun-ajaran/simpan', 'Admin\TahunAjaran::simpan');
    $routes->post('tahun-ajaran/update/(:num)', 'Admin\TahunAjaran::update/$1');
    $routes->match(['post', 'delete'], 'tahun-ajaran/hapus/(:num)', 'Admin\TahunAjaran::hapus/$1');
    $routes->post('tahun-ajaran/aktifkan/(:num)', 'Admin\TahunAjaran::aktifkan/$1');

    // Pengaturan
    $routes->get('pengaturan', 'Admin\Pengaturan::index');
    $routes->post('pengaturan/simpan', 'Admin\Pengaturan::simpan');

    // Log Aktivitas
    $routes->get('log', 'Admin\Log::index');
    $routes->get('log/data', 'Admin\Log::data');

    // Ujian (admin)
    $routes->get('ujian', 'Admin\Ujian::index');
    $routes->get('ujian/buat', 'Admin\Ujian::buat');
    $routes->post('ujian/simpan', 'Admin\Ujian::simpan');
    $routes->get('ujian/(:num)', 'Admin\Ujian::detail/$1');
    $routes->post('ujian/update/(:num)', 'Admin\Ujian::update/$1');
    $routes->match(['post', 'delete'], 'ujian/hapus/(:num)', 'Admin\Ujian::hapus/$1');
    $routes->post('ujian/token/(:num)', 'Admin\Ujian::regenerateToken/$1');

    // Rekap Nilai
    $routes->get('nilai', 'Admin\\Nilai::index');
    $routes->get('nilai/ujian/(:num)', 'Admin\\Nilai::ujian/$1');
    $routes->get('nilai/ekspor-excel/(:num)', 'Admin\\Nilai::eksporExcel/$1');
    $routes->get('nilai/ekspor-pdf/(:num)', 'Admin\\Nilai::eksporPdf/$1');

    // Jenis Ujian
    $routes->get('jenis-ujian', 'Admin\\JenisUjian::index');
    $routes->get('jenis-ujian/data', 'Admin\\JenisUjian::data');
    $routes->post('jenis-ujian/simpan', 'Admin\\JenisUjian::simpan');
    $routes->post('jenis-ujian/update/(:num)', 'Admin\\JenisUjian::update/$1');
    $routes->match(['post', 'delete'], 'jenis-ujian/hapus/(:num)', 'Admin\\JenisUjian::hapus/$1');

    // Bank Soal (admin)
    $routes->get('bank-soal', 'Admin\\BankSoal::index');
    $routes->get('bank-soal/data', 'Admin\\BankSoal::data');
    $routes->post('bank-soal/simpan', 'Admin\\BankSoal::simpan');
    $routes->post('bank-soal/update/(:num)', 'Admin\\BankSoal::update/$1');
    $routes->match(['post', 'delete'], 'bank-soal/hapus/(:num)', 'Admin\\BankSoal::hapus/$1');
    $routes->get('bank-soal/(:num)', 'Admin\\BankSoal::detail/$1');

    // Monitoring Soal
    $routes->get('monitoring-soal', 'Admin\\MonitoringSoal::index');
    $routes->get('monitoring-soal/data', 'Admin\\MonitoringSoal::data');

    // Token
    $routes->get('token', 'Admin\\Token::index');
    $routes->get('token/data', 'Admin\\Token::data');
    $routes->post('token/regenerate/(:num)', 'Admin\\Token::regenerate/$1');

    // Cetak / Administrasi
    $routes->get('cetak/kartu', 'Admin\\Cetak::kartu');
    $routes->get('cetak/kartu/print', 'Admin\\Cetak::cetakKartu');
    $routes->get('cetak/kartu/(:num)', 'Admin\\Cetak::cetakKartu/$1');
    $routes->get('cetak/berita-acara', 'Admin\\Cetak::beritaAcara');
    $routes->get('cetak/berita-acara/(:num)', 'Admin\\Cetak::cetakBeritaAcara/$1');
    $routes->get('cetak/daftar-hadir', 'Admin\\Cetak::daftarHadir');
    $routes->get('cetak/daftar-hadir/(:num)', 'Admin\\Cetak::cetakDaftarHadir/$1');
    $routes->get('cetak/hasil-nilai', 'Admin\\Cetak::hasilNilai');
    $routes->get('cetak/hasil-nilai/(:num)', 'Admin\\Cetak::cetakHasilNilai/$1');
});


// ─── Proktor ──────────────────────────────────────────────────────────────────
$routes->group('/proktor', ['filter' => 'auth:proktor'], function ($routes) {
    $routes->get('/', 'Proktor\Dashboard::index');
    $routes->get('dashboard', 'Proktor\Dashboard::index');
    $routes->get('monitoring', 'Proktor\Monitoring::index');
    $routes->get('monitoring/data', 'Proktor\Monitoring::data');
    $routes->get('monitoring/(:num)', 'Proktor\Monitoring::index/$1');
    $routes->post('monitoring/reset-login/(:num)', 'Proktor\Monitoring::resetLogin/$1');
    $routes->post('monitoring/buka-kunci/(:num)', 'Proktor\Monitoring::bukaKunci/$1');
    $routes->post('monitoring/tambah-waktu/(:num)', 'Proktor\Monitoring::tambahWaktu/$1');
    $routes->post('monitoring/paksa-selesai/(:num)', 'Proktor\Monitoring::paksaSelesai/$1');
    $routes->get('token/(:num)', 'Proktor\Token::detail/$1');
    $routes->post('token/regenerate/(:num)', 'Proktor\Token::regenerate/$1');
    $routes->get('nilai', 'Proktor\Nilai::index');
    $routes->get('nilai/ujian/(:num)', 'Proktor\Nilai::ujian/$1');
});

// ─── Guru ─────────────────────────────────────────────────────────────────────
$routes->group('/guru', ['filter' => 'auth:guru'], function ($routes) {
    $routes->get('/', 'Guru\Dashboard::index');
    $routes->get('dashboard', 'Guru\Dashboard::index');

    // Bank Soal
    $routes->get('paket-soal', 'Guru\PaketSoal::index');
    $routes->get('paket-soal/buat', 'Guru\PaketSoal::buat');
    $routes->post('paket-soal/simpan', 'Guru\PaketSoal::simpan');
    $routes->get('paket-soal/(:num)', 'Guru\PaketSoal::detail/$1');
    $routes->post('paket-soal/update/(:num)', 'Guru\PaketSoal::update/$1');
    $routes->match(['post', 'delete'], 'paket-soal/hapus/(:num)', 'Guru\PaketSoal::hapus/$1');
    $routes->post('paket-soal/duplikat/(:num)', 'Guru\PaketSoal::duplikat/$1');

    // Soal
    $routes->get('soal/(:num)/tambah', 'Guru\Soal::tambah/$1');
    $routes->post('soal/(:num)/simpan', 'Guru\Soal::simpan/$1');
    $routes->get('soal/(:num)/edit/(:num)', 'Guru\Soal::edit/$1/$2');
    $routes->post('soal/(:num)/update/(:num)', 'Guru\Soal::update/$1/$2');
    $routes->match(['post', 'delete'], 'soal/hapus/(:num)', 'Guru\Soal::hapus/$1');
    $routes->post('soal/duplikat/(:num)', 'Guru\Soal::duplikat/$1');
    $routes->get('soal/pratinjau/(:num)', 'Guru\Soal::pratinjau/$1');
    $routes->post('soal/impor/(:num)', 'Guru\Soal::impor/$1');
    $routes->get('soal/template/(:num)', 'Guru\Soal::template/$1');

    // Ujian
    $routes->get('ujian', 'Guru\Ujian::index');
    $routes->get('ujian/buat', 'Guru\Ujian::buat');
    $routes->post('ujian/simpan', 'Guru\Ujian::simpan');
    $routes->get('ujian/(:num)', 'Guru\Ujian::detail/$1');
    $routes->post('ujian/update/(:num)', 'Guru\Ujian::update/$1');
    $routes->match(['post', 'delete'], 'ujian/hapus/(:num)', 'Guru\Ujian::hapus/$1');
    $routes->post('ujian/token/(:num)', 'Guru\Ujian::regenerateToken/$1');
    $routes->get('ujian/monitoring/(:num)', 'Guru\Ujian::monitoring/$1');

    // Koreksi
    $routes->get('koreksi', 'Guru\Koreksi::index');
    $routes->get('koreksi/ujian/(:num)', 'Guru\Koreksi::ujian/$1');
    $routes->get('koreksi/peserta/(:num)', 'Guru\Koreksi::peserta/$1');
    $routes->post('koreksi/simpan', 'Guru\Koreksi::simpan');

    // Nilai
    $routes->get('nilai', 'Guru\Nilai::index');
    $routes->get('nilai/ujian/(:num)', 'Guru\Nilai::ujian/$1');
    $routes->get('nilai/ekspor-excel/(:num)', 'Guru\Nilai::eksporExcel/$1');
    $routes->get('nilai/ekspor-pdf/(:num)', 'Guru\Nilai::eksporPdf/$1');
});

// ─── Siswa ────────────────────────────────────────────────────────────────────
$routes->group('/siswa', ['filter' => 'auth:siswa'], function ($routes) {
    $routes->get('/', 'Siswa\Dashboard::index');
    $routes->get('dashboard', 'Siswa\Dashboard::index');
    $routes->get('ujian', 'Siswa\Ujian::index');
    $routes->post('ujian/masuk', 'Siswa\Ujian::masuk');
    $routes->post('ujian/konfirmasi', 'Siswa\Ujian::masuk');
    $routes->get('ujian/tata-tertib/(:num)', 'Siswa\Ujian::tataTertib/$1');
    $routes->post('ujian/mulai/(:num)', 'Siswa\Ujian::mulai/$1');
    $routes->get('ujian/kerjakan/(:num)', 'Siswa\Ujian::kerjakan/$1');
    $routes->post('ujian/simpan-jawaban', 'Siswa\Ujian::simpanJawaban');
    $routes->post('ujian/selesai/(:num)', 'Siswa\Ujian::selesai/$1');
    $routes->get('ujian/ringkasan/(:num)', 'Siswa\Ujian::ringkasan/$1');
    $routes->post('ujian/catat-pelanggaran', 'Siswa\Ujian::catatPelanggaran');
    $routes->get('nilai', 'Siswa\Nilai::index');
});


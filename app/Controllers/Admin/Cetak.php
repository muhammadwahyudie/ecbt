<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Cetak extends BaseController
{
    private function getUjianList(): array
    {
        $db = \Config\Database::connect();
        return $db->table('ujian u')
            ->select('u.id, u.nama, u.mulai_at, u.selesai_at, u.status, m.nama AS nama_mapel')
            ->join('paket_soal ps', 'ps.id = u.paket_id', 'left')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->where('u.deleted_at', null)
            ->orderBy('u.mulai_at', 'DESC')
            ->get()->getResultArray();
    }

    public function beritaAcara()
    {
        return view('admin/cetak/berita_acara', [
            'title'     => 'Cetak Berita Acara',
            'pageTitle' => 'Cetak Berita Acara',
            'ujianList' => $this->getUjianList(),
        ]);
    }

    public function cetakBeritaAcara($ujianId = null)
    {
        $db = \Config\Database::connect();
        $ujian = $db->table('ujian u')
            ->select('u.*, m.nama AS nama_mapel')
            ->join('paket_soal ps', 'ps.id = u.paket_id', 'left')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->where('u.id', $ujianId)
            ->get()->getRowArray();

        if (!$ujian) {
            return redirect()->to(base_url('admin/cetak/berita-acara'))->with('error', 'Ujian tidak ditemukan.');
        }

        $peserta = $db->table('ujian_peserta up')
            ->select('up.*, u.nama AS nama_siswa, u.username AS nis')
            ->join('users u', 'u.id = up.user_id', 'left')
            ->where('up.ujian_id', $ujianId)
            ->orderBy('u.nama', 'ASC')
            ->get()->getResultArray();

        $sekolah = $db->table('pengaturan')->get()->getRowArray();

        return view('admin/cetak/print_berita_acara', [
            'title'   => 'Cetak Berita Acara',
            'ujian'   => $ujian,
            'peserta' => $peserta,
            'sekolah' => $sekolah,
        ]);
    }

    public function daftarHadir()
    {
        return view('admin/cetak/daftar_hadir', [
            'title'     => 'Cetak Daftar Hadir',
            'pageTitle' => 'Cetak Daftar Hadir',
            'ujianList' => $this->getUjianList(),
        ]);
    }

    public function cetakDaftarHadir($ujianId = null)
    {
        $db = \Config\Database::connect();
        $ujian = $db->table('ujian u')
            ->select('u.*, m.nama AS nama_mapel')
            ->join('paket_soal ps', 'ps.id = u.paket_id', 'left')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->where('u.id', $ujianId)
            ->get()->getRowArray();

        if (!$ujian) {
            return redirect()->to(base_url('admin/cetak/daftar-hadir'))->with('error', 'Ujian tidak ditemukan.');
        }

        $peserta = $db->table('ujian_peserta up')
            ->select('up.*, u.nama AS nama_siswa, u.username AS nis, k.nama AS nama_kelas')
            ->join('users u', 'u.id = up.user_id', 'left')
            ->join('siswa_kelas sk', 'sk.user_id = u.id', 'left')
            ->join('kelas k', 'k.id = sk.kelas_id', 'left')
            ->where('up.ujian_id', $ujianId)
            ->orderBy('u.nama', 'ASC')
            ->get()->getResultArray();

        $sekolah = $db->table('pengaturan')->get()->getRowArray();

        return view('admin/cetak/print_daftar_hadir', [
            'title'   => 'Cetak Daftar Hadir',
            'ujian'   => $ujian,
            'peserta' => $peserta,
            'sekolah' => $sekolah,
        ]);
    }

    public function hasilNilai()
    {
        return view('admin/cetak/hasil_nilai', [
            'title'     => 'Cetak Hasil Nilai',
            'pageTitle' => 'Cetak Hasil Nilai',
            'ujianList' => $this->getUjianList(),
        ]);
    }

    public function cetakHasilNilai($ujianId = null)
    {
        $db = \Config\Database::connect();
        $ujian = $db->table('ujian u')
            ->select('u.*, m.nama AS nama_mapel')
            ->join('paket_soal ps', 'ps.id = u.paket_id', 'left')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->where('u.id', $ujianId)
            ->get()->getRowArray();

        if (!$ujian) {
            return redirect()->to(base_url('admin/cetak/hasil-nilai'))->with('error', 'Ujian tidak ditemukan.');
        }

        $hasil = $db->table('ujian_peserta up')
            ->select('up.*, u.nama AS nama_siswa, u.username AS nis, k.nama AS nama_kelas,
                      h.nilai_akhir, h.benar, h.salah, h.kosong')
            ->join('users u', 'u.id = up.user_id', 'left')
            ->join('siswa_kelas sk', 'sk.user_id = u.id', 'left')
            ->join('kelas k', 'k.id = sk.kelas_id', 'left')
            ->join('hasil h', 'h.ujian_id = up.ujian_id AND h.siswa_id = up.user_id', 'left')
            ->where('up.ujian_id', $ujianId)
            ->orderBy('h.nilai_akhir', 'DESC')
            ->get()->getResultArray();

        $sekolah = $db->table('pengaturan')->get()->getRowArray();

        return view('admin/cetak/print_hasil_nilai', [
            'title'   => 'Cetak Hasil Nilai',
            'ujian'   => $ujian,
            'hasil'   => $hasil,
            'sekolah' => $sekolah,
        ]);
    }

    public function kartu()
    {
        $db = \Config\Database::connect();
        $ujianList = $this->getUjianList();

        // Get kelas list
        $kelasList = $db->table('kelas k')
            ->select('k.id, k.nama, k.tingkat, COUNT(sk.id) AS jumlah_siswa')
            ->join('siswa_kelas sk', 'sk.kelas_id = k.id', 'left')
            ->where('k.deleted_at', null)
            ->groupBy('k.id')
            ->orderBy('k.tingkat', 'ASC')
            ->orderBy('k.nama', 'ASC')
            ->get()->getResultArray();

        // Get settings
        $settings = [];
        $settingsRows = $db->table('pengaturan')->get()->getResultArray();
        foreach ($settingsRows as $row) {
            $settings[$row['kunci']] = $row['nilai'];
        }

        return view('admin/cetak/kartu', [
            'title'     => 'Cetak Kartu Ujian',
            'pageTitle' => 'Cetak Kartu Ujian (Peserta & Meja)',
            'ujianList' => $ujianList,
            'kelasList' => $kelasList,
            'settings'  => $settings,
            'tanggal'   => $this->formatTanggalIndo(),
        ]);
    }

    public function cetakKartu($ujianId = null)
    {
        $db = \Config\Database::connect();
        $req = $this->request;

        $ujianId = $ujianId ?: $req->getGet('ujian_id');
        $kelasId = $req->getGet('kelas_id');
        $formatNomor = $req->getGet('format_nomor') ?: 'tka';

        $ujian = null;
        if ($ujianId) {
            $ujian = $db->table('ujian u')
                ->select('u.*, m.nama AS nama_mapel')
                ->join('paket_soal ps', 'ps.id = u.paket_id', 'left')
                ->join('mapel m', 'm.id = ps.mapel_id', 'left')
                ->where('u.id', $ujianId)
                ->get()->getRowArray();
        }

        // Fetch students
        $builder = $db->table('users u')
            ->select('u.id, u.nama, u.username, u.nis, u.nisn, k.nama AS nama_kelas, r.nama AS nama_ruang')
            ->where('u.role', 'siswa')
            ->where('u.deleted_at', null);

        if ($ujianId) {
            $builder->join('ujian_peserta up', 'up.user_id = u.id', 'inner')
                ->join('ruang r', 'r.id = up.ruang_id', 'left')
                ->join('siswa_kelas sk', 'sk.user_id = u.id', 'left')
                ->join('kelas k', 'k.id = sk.kelas_id', 'left')
                ->where('up.ujian_id', $ujianId)
                ->orderBy('k.nama', 'ASC')
                ->orderBy('u.nama', 'ASC');
        } elseif ($kelasId) {
            $builder->join('siswa_kelas sk', 'sk.user_id = u.id', 'inner')
                ->join('kelas k', 'k.id = sk.kelas_id', 'left')
                ->join('ruang r', 'r.id = 1', 'left')
                ->where('sk.kelas_id', $kelasId)
                ->orderBy('u.nama', 'ASC');
        } else {
            $builder->join('siswa_kelas sk', 'sk.user_id = u.id', 'left')
                ->join('kelas k', 'k.id = sk.kelas_id', 'left')
                ->join('ruang r', 'r.id = 1', 'left')
                ->orderBy('k.nama', 'ASC')
                ->orderBy('u.nama', 'ASC');
        }

        $rawPeserta = $builder->get()->getResultArray();

        // Format peserta data
        $peserta = [];
        foreach ($rawPeserta as $idx => $p) {
            // Nomor peserta
            if ($formatNomor === 'nisn' && !empty($p['nisn'])) {
                $noPeserta = $p['nisn'];
            } elseif ($formatNomor === 'nis' && !empty($p['nis'])) {
                $noPeserta = $p['nis'];
            } else {
                // Format TKA matching kartu.pdf (e.g. T2-26-05-34-1040-0001-8)
                $seq = $idx + 1;
                $chk = (($seq * 7) + 1) % 10;
                $noPeserta = sprintf('T2-26-05-34-1040-%04d-%d', $seq, $chk);
            }

            $peserta[] = [
                'id'            => $p['id'],
                'nama'          => strtoupper($p['nama']),
                'nomor_peserta' => $noPeserta,
                'kelas'         => $p['nama_kelas'] ?: 'IX-A',
                'ruang'         => $p['nama_ruang'] ?: 'RUANG 01',
                'foto'          => null,
                'qr_data'       => $noPeserta,
            ];
        }

        // Settings
        $settings = [];
        $settingsRows = $db->table('pengaturan')->get()->getResultArray();
        foreach ($settingsRows as $row) {
            $settings[$row['kunci']] = $row['nilai'];
        }

        $judulBaris1 = $req->getGet('judul1') ?: 'KARTU PESERTA';
        $judulBaris2 = $req->getGet('judul2') ?: ($ujian ? strtoupper($ujian['nama']) : 'TES KEMAMPUAN AKADEMIK SMP/MTS');
        $judulBaris3 = $req->getGet('judul3') ?: 'TAHUN 2026';
        $kota        = $req->getGet('kota') ?: ($settings['kota'] ?? 'Banyuwangi');
        $tanggal     = $req->getGet('tanggal') ?: ($this->formatTanggalIndo());
        $kepala      = $req->getGet('kepala_sekolah') ?: ($settings['kepala_sekolah'] ?? 'DIDIK PURWANTO, S.Pd');
        $nip         = $req->getGet('nip_kepala_sekolah') ?: ($settings['nip_kepala_sekolah'] ?? '-');
        $logo        = $settings['logo_sekolah'] ?? $settings['logo'] ?? null;

        // Group into pages of 5 rows each
        $pages = array_chunk($peserta, 5);
        if (empty($pages)) {
            $pages = [[]]; // at least one page
        }

        return view('admin/cetak/print_kartu', [
            'title'         => 'Cetak Kartu Ujian',
            'ujian'         => $ujian,
            'pages'         => $pages,
            'totalPeserta'  => count($peserta),
            'judulBaris1'   => $judulBaris1,
            'judulBaris2'   => $judulBaris2,
            'judulBaris3'   => $judulBaris3,
            'kota'          => $kota,
            'tanggal'       => $tanggal,
            'kepalaSekolah' => $kepala,
            'nip'           => $nip,
            'logo'          => $logo,
        ]);
    }

    private function formatTanggalIndo(?string $date = null): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $time = $date ? strtotime($date) : time();
        $d = date('j', $time);
        $m = $bulan[(int)date('n', $time)];
        $y = date('Y', $time);
        return "{$d} {$m} {$y}";
    }
}

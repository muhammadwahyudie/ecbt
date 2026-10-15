<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\HasilModel;

class Nilai extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $db = \Config\Database::connect();

        $riwayatNilai = $db->query("
            SELECT u.*, ps.nama as nama_paket, m.nama as nama_mapel,
                   up.selesai_at, h.skor_objektif, h.skor_uraian, h.nilai_akhir, h.status_koreksi
            FROM ujian_peserta up
            JOIN ujian u ON u.id = up.ujian_id
            JOIN paket_soal ps ON ps.id = u.paket_id
            LEFT JOIN mapel m ON m.id = ps.mapel_id
            LEFT JOIN hasil h ON h.peserta_id = up.id
            WHERE up.user_id = {$userId} AND up.status = 'selesai'
            ORDER BY up.selesai_at DESC
        ")->getResultArray();

        return view('siswa/nilai/index', [
            'title'        => 'Riwayat & Hasil Nilai Ujian',
            'riwayatNilai' => $riwayatNilai,
        ]);
    }
}

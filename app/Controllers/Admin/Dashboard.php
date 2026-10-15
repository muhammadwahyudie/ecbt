<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Hitung statistik
        $pesertaOnline = $db->table('ujian_peserta')
            ->where('status', 'mengerjakan')
            ->where('last_seen_at >=', date('Y-m-d H:i:s', time() - 60))
            ->countAllResults();

        $ujianBerlangsung = $db->table('ujian')
            ->where('status', 'berlangsung')
            ->countAllResults();

        $paketSoal = $db->table('paket_soal')
            ->where('deleted_at', null)
            ->countAllResults();

        // Rata-rata nilai dari semua ujian selesai
        $rataRata = $db->table('hasil')
            ->selectAvg('nilai_akhir', 'rata')
            ->get()->getRow();
        $rataRataNilai = $rataRata ? round($rataRata->rata ?? 0, 1) : 0;

        // Ujian hari ini
        $ujianHariIni = $db->table('ujian')
            ->where('DATE(mulai_at)', date('Y-m-d'))
            ->whereIn('status', ['terjadwal', 'berlangsung'])
            ->get()->getResultArray();

        // Rata-rata nilai per mapel
        $nilaiPerMapel = $db->query("
            SELECT m.nama as mapel, AVG(h.nilai_akhir) as rata
            FROM hasil h
            JOIN ujian_peserta up ON h.peserta_id = up.id
            JOIN ujian u ON up.ujian_id = u.id
            JOIN paket_soal ps ON u.paket_id = ps.id
            JOIN mapel m ON ps.mapel_id = m.id
            GROUP BY m.id, m.nama
            ORDER BY rata DESC
            LIMIT 8
        ")->getResultArray();

        return view('admin/dashboard', [
            'title'            => 'Dashboard Admin',
            'pesertaOnline'    => $pesertaOnline,
            'ujianBerlangsung' => $ujianBerlangsung,
            'paketSoal'        => $paketSoal,
            'rataRataNilai'    => $rataRataNilai,
            'ujianHariIni'     => $ujianHariIni,
            'nilaiPerMapel'    => $nilaiPerMapel,
        ]);
    }
}

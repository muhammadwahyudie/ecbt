<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db     = \Config\Database::connect();
        $guruId = session()->get('user_id');

        $paketSoal = $db->table('paket_soal')
            ->where('deleted_at', null)
            ->countAllResults();

        $ujianAktif = $db->table('ujian')
            ->where('pembuat_id', $guruId)
            ->whereIn('status', ['terjadwal', 'berlangsung'])
            ->countAllResults();

        $menungguKoreksi = $db->table('hasil')
            ->join('ujian_peserta up', 'up.id = hasil.peserta_id')
            ->join('ujian u', 'u.id = up.ujian_id')
            ->where('u.pembuat_id', $guruId)
            ->whereIn('hasil.status_koreksi', ['belum', 'sebagian'])
            ->countAllResults();

        $rataRata = $db->query("
            SELECT AVG(h.nilai_akhir) as rata
            FROM hasil h
            JOIN ujian_peserta up ON h.peserta_id = up.id
            JOIN ujian u ON up.ujian_id = u.id
            WHERE u.pembuat_id = {$guruId}
        ")->getRow();
        $rataRataNilai = $rataRata ? round($rataRata->rata ?? 0, 1) : 0;

        // Daftar ujian milik guru
        $ujianSaya = $db->table('ujian u')
            ->select('u.*, ps.nama as nama_paket')
            ->join('paket_soal ps', 'ps.id = u.paket_id')
            ->where('u.pembuat_id', $guruId)
            ->where('u.deleted_at', null)
            ->orderBy('u.mulai_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        return view('guru/dashboard', [
            'title'            => 'Dashboard Guru',
            'paketSoal'        => $paketSoal,
            'ujianAktif'       => $ujianAktif,
            'menungguKoreksi'  => $menungguKoreksi,
            'rataRataNilai'    => $rataRataNilai,
            'ujianSaya'        => $ujianSaya,
        ]);
    }
}

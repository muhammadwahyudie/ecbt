<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db      = \Config\Database::connect();
        $userId  = session()->get('user_id');

        // Kelas siswa saat ini
        $ta = $db->table('tahun_ajaran')->where('aktif', 1)->get()->getRow();
        $kelasId = null;
        if ($ta) {
            $sk = $db->table('siswa_kelas')
                ->where('user_id', $userId)
                ->where('tahun_ajaran_id', $ta->id)
                ->get()->getRow();
            $kelasId = $sk ? $sk->kelas_id : null;
        }

        // Ujian hari ini
        $ujianHariIni = $kelasId ? $db->query("
            SELECT u.*, ps.nama as nama_paket, m.nama as nama_mapel,
                   up.id as peserta_id, up.status as status_peserta
            FROM ujian u
            JOIN ujian_kelas uk ON uk.ujian_id = u.id AND uk.kelas_id = {$kelasId}
            JOIN paket_soal ps ON ps.id = u.paket_id
            LEFT JOIN mapel m ON m.id = ps.mapel_id
            LEFT JOIN ujian_peserta up ON up.ujian_id = u.id AND up.user_id = {$userId}
            WHERE DATE(u.mulai_at) = CURDATE()
              AND u.status IN ('terjadwal','berlangsung')
              AND u.deleted_at IS NULL
            ORDER BY u.mulai_at ASC
        ")->getResultArray() : [];

        // Ujian mendatang
        $ujianMendatang = $kelasId ? $db->query("
            SELECT u.*, ps.nama as nama_paket, m.nama as nama_mapel
            FROM ujian u
            JOIN ujian_kelas uk ON uk.ujian_id = u.id AND uk.kelas_id = {$kelasId}
            JOIN paket_soal ps ON ps.id = u.paket_id
            LEFT JOIN mapel m ON m.id = ps.mapel_id
            WHERE DATE(u.mulai_at) > CURDATE()
              AND u.status IN ('terjadwal')
              AND u.deleted_at IS NULL
            ORDER BY u.mulai_at ASC
            LIMIT 5
        ")->getResultArray() : [];

        // Ujian selesai + nilai
        $ujianSelesai = $db->query("
            SELECT u.*, ps.nama as nama_paket, m.nama as nama_mapel,
                   h.nilai_akhir, h.status_koreksi, up.selesai_at
            FROM ujian_peserta up
            JOIN ujian u ON u.id = up.ujian_id
            JOIN paket_soal ps ON ps.id = u.paket_id
            LEFT JOIN mapel m ON m.id = ps.mapel_id
            LEFT JOIN hasil h ON h.peserta_id = up.id
            WHERE up.user_id = {$userId}
              AND up.status = 'selesai'
            ORDER BY up.selesai_at DESC
            LIMIT 10
        ")->getResultArray();

        return view('siswa/dashboard', [
            'title'          => 'Dashboard Siswa',
            'ujianHariIni'   => $ujianHariIni,
            'ujianMendatang' => $ujianMendatang,
            'ujianSelesai'   => $ujianSelesai,
        ]);
    }
}

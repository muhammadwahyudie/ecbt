<?php

namespace App\Controllers\Proktor;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $pesertaOnline = $db->table('ujian_peserta')
            ->where('status', 'mengerjakan')
            ->where('last_seen_at >=', date('Y-m-d H:i:s', time() - 60))
            ->countAllResults();

        $belumLogin = $db->table('ujian_peserta')
            ->where('status', 'belum_login')
            ->whereIn('ujian_id', function ($builder) {
                $builder->select('id')->from('ujian')->where('status', 'berlangsung');
            })
            ->countAllResults();

        $pelanggaran = $db->table('ujian_peserta')
            ->where('pelanggaran >', 0)
            ->where('status', 'mengerjakan')
            ->countAllResults();

        $ujianBerlangsung = $db->table('ujian')
            ->where('status', 'berlangsung')
            ->countAllResults();

        // Ujian yang sedang berlangsung untuk dipantau
        $ujianAktif = $db->table('ujian')
            ->where('status', 'berlangsung')
            ->get()->getResultArray();

        return view('proktor/dashboard', [
            'title'            => 'Dashboard Proktor',
            'pesertaOnline'    => $pesertaOnline,
            'belumLogin'       => $belumLogin,
            'pelanggaran'      => $pelanggaran,
            'ujianBerlangsung' => $ujianBerlangsung,
            'ujianAktif'       => $ujianAktif,
        ]);
    }
}

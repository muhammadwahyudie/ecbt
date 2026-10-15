<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MonitoringSoal extends BaseController
{
    public function index()
    {
        return view('admin/monitoring_soal/index', [
            'title'     => 'Monitoring Soal',
            'pageTitle' => 'Monitoring Soal',
        ]);
    }

    public function data()
    {
        $db = \Config\Database::connect();
        $data = $db->table('users u')
            ->select('u.id, u.nama, u.username,
                      COUNT(DISTINCT ps.id) AS jumlah_paket,
                      COUNT(DISTINCT s.id)  AS jumlah_soal,
                      MAX(ps.created_at)    AS terakhir_buat')
            ->join('paket_soal ps', 'ps.pemilik_id = u.id AND ps.deleted_at IS NULL', 'left')
            ->join('soal s', 's.paket_id = ps.id AND s.deleted_at IS NULL', 'left')
            ->where('u.role', 'guru')
            ->where('u.deleted_at', null)
            ->groupBy('u.id')
            ->orderBy('jumlah_soal', 'DESC')
            ->get()->getResultArray();

        return $this->response->setJSON(['data' => $data]);
    }
}

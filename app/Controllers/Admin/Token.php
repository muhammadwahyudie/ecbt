<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Token extends BaseController
{
    public function index()
    {
        return view('admin/token/index', [
            'title'     => 'Token Ujian',
            'pageTitle' => 'Token Ujian',
        ]);
    }

    public function data()
    {
        $db = \Config\Database::connect();
        $data = $db->table('ujian u')
            ->select('u.id, u.nama, u.token, u.mulai_at, u.selesai_at, u.status,
                      m.nama AS nama_mapel')
            ->join('paket_soal ps', 'ps.id = u.paket_id', 'left')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->where('u.deleted_at', null)
            ->orderBy('u.mulai_at', 'DESC')
            ->get()->getResultArray();

        return $this->response->setJSON(['data' => $data]);
    }

    public function regenerate($id = null)
    {
        $db = \Config\Database::connect();
        $ujian = $db->table('ujian')->where('id', $id)->get()->getRowArray();

        if (!$ujian) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ujian tidak ditemukan.']);
        }

        // Generate 6-char uppercase token
        $token = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $db->table('ujian')->where('id', $id)->update(['token' => $token]);

        return $this->response->setJSON(['success' => true, 'token' => $token]);
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LogAktivitasModel;

class Log extends BaseController
{
    protected LogAktivitasModel $logModel;

    public function __construct()
    {
        $this->logModel = new LogAktivitasModel();
    }

    public function index()
    {
        return view('admin/log/index', [
            'title' => 'Log Aktivitas Sistem (Audit Trail)',
        ]);
    }

    /**
     * Data JSON untuk DataTables Log Aktivitas
     */
    public function data()
    {
        $logs = $this->logModel->select('log_aktivitas.*, u.nama, u.username, u.role')
            ->join('users u', 'u.id = log_aktivitas.user_id', 'left')
            ->orderBy('log_aktivitas.id', 'DESC')
            ->limit(500)
            ->findAll();

        return $this->response->setJSON(['data' => $logs]);
    }
}

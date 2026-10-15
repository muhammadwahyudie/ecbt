<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\LogAktivitasModel;

class Pengaturan extends BaseController
{
    protected PengaturanModel $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $settings = $this->pengaturanModel->getAllAsMap();

        return view('admin/pengaturan/index', [
            'title'    => 'Pengaturan Sistem & Sekolah',
            'settings' => $settings,
        ]);
    }

    public function simpan()
    {
        $fields = [
            'nama_sekolah', 'alamat_sekolah', 'telepon_sekolah', 'email_sekolah',
            'kepala_sekolah', 'nip_kepala_sekolah', 'batas_login_gagal',
            'toleransi_terlambat_menit', 'maks_pelanggaran_default'
        ];

        foreach ($fields as $field) {
            $val = $this->request->getPost($field);
            if ($val !== null) {
                $this->pengaturanModel->setVal($field, trim($val));
            }
        }

        // Upload logo jika ada
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $mime = $logo->getMimeType();
            if (in_array($mime, ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])) {
                $newName = 'logo_sekolah.' . $logo->getExtension();
                $targetDir = FCPATH . 'uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $logo->move($targetDir, $newName, true);
                $this->pengaturanModel->setVal('logo_sekolah', 'uploads/' . $newName);
            }
        }

        LogAktivitasModel::catat('ubah_pengaturan', 'Pengaturan sistem & profil sekolah');

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}

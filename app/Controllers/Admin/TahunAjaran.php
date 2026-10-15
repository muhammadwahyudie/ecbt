<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;
use App\Models\LogAktivitasModel;

class TahunAjaran extends BaseController
{
    protected TahunAjaranModel $taModel;

    public function __construct()
    {
        $this->taModel = new TahunAjaranModel();
    }

    public function index()
    {
        $list = $this->taModel->orderBy('nama', 'DESC')->orderBy('semester', 'DESC')->findAll();

        return view('admin/tahun_ajaran/index', [
            'title'  => 'Manajemen Tahun Ajaran & Semester',
            'taList' => $list,
        ]);
    }

    /**
     * Tambah Tahun Ajaran Baru
     */
    public function simpan()
    {
        $rules = [
            'nama'     => 'required|min_length[4]|max_length[20]',
            'semester' => 'required|in_list[1,2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $aktif = $this->request->getPost('aktif') !== null ? 1 : 0;
        $id = $this->taModel->insert([
            'nama'     => trim($this->request->getPost('nama')),
            'semester' => $this->request->getPost('semester'),
            'aktif'    => $aktif,
        ]);

        if ($aktif) {
            $this->taModel->setAktif((int)$id);
        }

        LogAktivitasModel::catat('tambah_tahun_ajaran', "TA: {$this->request->getPost('nama')} Sem {$this->request->getPost('semester')}", ['id' => $id]);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Update Tahun Ajaran
     */
    public function update($id = null)
    {
        $ta = $this->taModel->find($id);
        if (!$ta) {
            return redirect()->back()->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $rules = [
            'nama'     => 'required|min_length[4]|max_length[20]',
            'semester' => 'required|in_list[1,2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->taModel->update($id, [
            'nama'     => trim($this->request->getPost('nama')),
            'semester' => $this->request->getPost('semester'),
        ]);

        LogAktivitasModel::catat('ubah_tahun_ajaran', "TA ID: {$id}", ['id' => $id]);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Aktifkan Tahun Ajaran Tertentu
     */
    public function aktifkan($id = null)
    {
        $ta = $this->taModel->find($id);
        if (!$ta) {
            return redirect()->back()->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $this->taModel->setAktif((int)$id);
        LogAktivitasModel::catat('aktifkan_tahun_ajaran', "Aktifkan TA: {$ta['nama']} Sem {$ta['semester']}", ['id' => $id]);

        return redirect()->back()->with('success', "Tahun ajaran {$ta['nama']} Semester {$ta['semester']} kini aktif.");
    }

    /**
     * Hapus Tahun Ajaran
     */
    public function hapus($id = null)
    {
        $ta = $this->taModel->find($id);
        if (!$ta) {
            return redirect()->back()->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        if ($ta['aktif']) {
            return redirect()->back()->with('error', 'Tahun ajaran yang sedang aktif tidak dapat dihapus.');
        }

        $this->taModel->delete($id);
        LogAktivitasModel::catat('hapus_tahun_ajaran', "TA: {$ta['nama']} Sem {$ta['semester']}", ['id' => $id]);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}

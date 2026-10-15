<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RuangModel;
use App\Models\LogAktivitasModel;

class Ruang extends BaseController
{
    protected RuangModel $ruangModel;

    public function __construct()
    {
        $this->ruangModel = new RuangModel();
    }

    public function index()
    {
        return view('admin/ruang/index', [
            'title' => 'Manajemen Ruang Ujian',
        ]);
    }

    /**
     * Data JSON untuk DataTables Ruang
     */
    public function data()
    {
        $ruang = $this->ruangModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();
        return $this->response->setJSON(['data' => $ruang]);
    }

    /**
     * Tambah Ruang
     */
    public function simpan()
    {
        $rules = [
            'nama'      => 'required|min_length[2]|max_length[100]',
            'kapasitas' => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $id = $this->ruangModel->insert([
            'nama'      => trim($this->request->getPost('nama')),
            'kapasitas' => (int)$this->request->getPost('kapasitas'),
        ]);

        LogAktivitasModel::catat('tambah_ruang', "Ruang: {$this->request->getPost('nama')}", ['ruang_id' => $id]);

        return redirect()->back()->with('success', 'Ruang ujian berhasil ditambahkan.');
    }

    /**
     * Update Ruang
     */
    public function update($id = null)
    {
        $ruang = $this->ruangModel->find($id);
        if (!$ruang) {
            return redirect()->back()->with('error', 'Ruang tidak ditemukan.');
        }

        $rules = [
            'nama'      => 'required|min_length[2]|max_length[100]',
            'kapasitas' => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->ruangModel->update($id, [
            'nama'      => trim($this->request->getPost('nama')),
            'kapasitas' => (int)$this->request->getPost('kapasitas'),
        ]);

        LogAktivitasModel::catat('ubah_ruang', "Ruang ID: {$id}", ['ruang_id' => $id]);

        return redirect()->back()->with('success', 'Ruang ujian berhasil diperbarui.');
    }

    /**
     * Hapus Ruang (Soft Delete)
     */
    public function hapus($id = null)
    {
        $ruang = $this->ruangModel->find($id);
        if (!$ruang) {
            return redirect()->back()->with('error', 'Ruang tidak ditemukan.');
        }

        $this->ruangModel->delete($id);
        LogAktivitasModel::catat('hapus_ruang', "Ruang: {$ruang['nama']}", ['ruang_id' => $id]);

        return redirect()->back()->with('success', 'Ruang berhasil dihapus.');
    }
}

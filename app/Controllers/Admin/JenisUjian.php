<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JenisUjianModel;
use App\Models\LogAktivitasModel;

class JenisUjian extends BaseController
{
    protected JenisUjianModel $model;

    public function __construct()
    {
        $this->model = new JenisUjianModel();
    }

    public function index()
    {
        return view('admin/jenis_ujian/index', [
            'title'     => 'Jenis Ujian',
            'pageTitle' => 'Jenis Ujian',
        ]);
    }

    public function data()
    {
        $data = $this->model->orderBy('nama', 'ASC')->findAll();
        return $this->response->setJSON(['data' => $data]);
    }

    public function simpan()
    {
        $rules = [
            'nama'      => 'required|min_length[2]|max_length[100]',
            'deskripsi' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $id = $this->model->insert([
            'nama'      => trim($this->request->getPost('nama')),
            'deskripsi' => trim($this->request->getPost('deskripsi') ?? ''),
        ]);

        LogAktivitasModel::catat('tambah_jenis_ujian', "Jenis: {$this->request->getPost('nama')}", ['id' => $id]);

        return redirect()->back()->with('success', 'Jenis ujian berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Jenis ujian tidak ditemukan.');
        }

        $rules = [
            'nama'      => 'required|min_length[2]|max_length[100]',
            'deskripsi' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->model->update($id, [
            'nama'      => trim($this->request->getPost('nama')),
            'deskripsi' => trim($this->request->getPost('deskripsi') ?? ''),
        ]);

        LogAktivitasModel::catat('ubah_jenis_ujian', "Jenis ID: {$id}", ['id' => $id]);

        return redirect()->back()->with('success', 'Jenis ujian berhasil diperbarui.');
    }

    public function hapus($id = null)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Jenis ujian tidak ditemukan.');
        }

        $this->model->delete($id);
        LogAktivitasModel::catat('hapus_jenis_ujian', "Jenis: {$item['nama']}", ['id' => $id]);

        return redirect()->back()->with('success', 'Jenis ujian berhasil dihapus.');
    }
}

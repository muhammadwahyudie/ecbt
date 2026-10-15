<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MapelModel;
use App\Models\GuruMapelKelasModel;
use App\Models\UserModel;
use App\Models\KelasModel;
use App\Models\TahunAjaranModel;
use App\Models\LogAktivitasModel;

class Mapel extends BaseController
{
    protected MapelModel $mapelModel;
    protected GuruMapelKelasModel $penugasanModel;
    protected UserModel $userModel;
    protected KelasModel $kelasModel;
    protected TahunAjaranModel $taModel;

    public function __construct()
    {
        $this->mapelModel      = new MapelModel();
        $this->penugasanModel  = new GuruMapelKelasModel();
        $this->userModel       = new UserModel();
        $this->kelasModel      = new KelasModel();
        $this->taModel         = new TahunAjaranModel();
    }

    public function index()
    {
        $taAktif = $this->taModel->getAktif();
        $guruList  = $this->userModel->where('role', 'guru')->where('aktif', 1)->orderBy('nama', 'ASC')->findAll();
        $kelasList = $taAktif ? $this->kelasModel->where('tahun_ajaran_id', $taAktif['id'])->orderBy('tingkat', 'ASC')->orderBy('nama', 'ASC')->findAll() : [];
        $mapelList = $this->mapelModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();
        $penugasan = $this->penugasanModel->getPenugasan();

        return view('admin/mapel/index', [
            'title'     => 'Mata Pelajaran & Penugasan Guru',
            'guruList'  => $guruList,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
            'penugasan' => $penugasan,
        ]);
    }

    /**
     * Data JSON untuk DataTables Mapel
     */
    public function data()
    {
        $mapels = $this->mapelModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();
        return $this->response->setJSON(['data' => $mapels]);
    }

    /**
     * Tambah Mapel
     */
    public function simpan()
    {
        $rules = [
            'kode' => 'required|min_length[1]|max_length[20]',
            'nama' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $id = $this->mapelModel->insert([
            'kode' => strtoupper(trim($this->request->getPost('kode'))),
            'nama' => trim($this->request->getPost('nama')),
        ]);

        LogAktivitasModel::catat('tambah_mapel', "Mapel: {$this->request->getPost('nama')}", ['mapel_id' => $id]);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Update Mapel
     */
    public function update($id = null)
    {
        $mapel = $this->mapelModel->find($id);
        if (!$mapel) {
            return redirect()->back()->with('error', 'Mata pelajaran tidak ditemukan.');
        }

        $rules = [
            'kode' => 'required|min_length[1]|max_length[20]',
            'nama' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->mapelModel->update($id, [
            'kode' => strtoupper(trim($this->request->getPost('kode'))),
            'nama' => trim($this->request->getPost('nama')),
        ]);

        LogAktivitasModel::catat('ubah_mapel', "Mapel ID: {$id}", ['mapel_id' => $id]);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Hapus Mapel (Soft Delete)
     */
    public function hapus($id = null)
    {
        $mapel = $this->mapelModel->find($id);
        if (!$mapel) {
            return redirect()->back()->with('error', 'Mata pelajaran tidak ditemukan.');
        }

        $this->mapelModel->delete($id);
        LogAktivitasModel::catat('hapus_mapel', "Mapel: {$mapel['nama']}", ['mapel_id' => $id]);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * Tambah Penugasan Guru ke Mapel & Kelas
     */
    public function simpanPenugasan()
    {
        $rules = [
            'guru_id'  => 'required|integer',
            'mapel_id' => 'required|integer',
            'kelas_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $guruId  = (int)$this->request->getPost('guru_id');
        $mapelId = (int)$this->request->getPost('mapel_id');
        $kelasId = (int)$this->request->getPost('kelas_id');

        $exists = $this->penugasanModel->where('guru_id', $guruId)
            ->where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Penugasan guru pada mapel dan kelas ini sudah ada.');
        }

        $this->penugasanModel->insert([
            'guru_id'    => $guruId,
            'mapel_id'   => $mapelId,
            'kelas_id'   => $kelasId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        LogAktivitasModel::catat('tambah_penugasan_guru', "Guru {$guruId} - Mapel {$mapelId} - Kelas {$kelasId}");

        return redirect()->back()->with('success', 'Penugasan guru berhasil ditambahkan.');
    }

    /**
     * Hapus Penugasan Guru
     */
    public function hapusPenugasan($id = null)
    {
        $p = $this->penugasanModel->find($id);
        if (!$p) {
            return redirect()->back()->with('error', 'Penugasan tidak ditemukan.');
        }

        $this->penugasanModel->delete($id);
        LogAktivitasModel::catat('hapus_penugasan_guru', "Penugasan ID {$id}");

        return redirect()->back()->with('success', 'Penugasan guru berhasil dihapus.');
    }
}

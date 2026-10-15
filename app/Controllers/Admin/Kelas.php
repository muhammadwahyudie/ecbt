<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\SiswaKelasModel;
use App\Models\TahunAjaranModel;
use App\Models\UserModel;
use App\Models\LogAktivitasModel;

class Kelas extends BaseController
{
    protected KelasModel $kelasModel;
    protected SiswaKelasModel $siswaKelasModel;
    protected TahunAjaranModel $taModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->kelasModel      = new KelasModel();
        $this->siswaKelasModel = new SiswaKelasModel();
        $this->taModel         = new TahunAjaranModel();
        $this->userModel       = new UserModel();
    }

    public function index()
    {
        $taList  = $this->taModel->orderBy('id', 'DESC')->findAll();
        $taAktif = $this->taModel->getAktif();

        return view('admin/kelas/index', [
            'title'   => 'Manajemen Kelas & Rombel',
            'taList'  => $taList,
            'taAktif' => $taAktif,
        ]);
    }

    /**
     * Data AJAX JSON untuk DataTables
     */
    public function data()
    {
        $taParam = $this->request->getGet('ta_id');
        $taId = null;
        if (!empty($taParam)) {
            if (is_numeric($taParam)) {
                $taId = (int)$taParam;
            } else {
                $ta = $this->taModel->where('nama', $taParam)->first();
                $taId = $ta ? (int)$ta['id'] : null;
            }
        }
        $kelasList = $this->kelasModel->getWithDetails($taId);

        return $this->response->setJSON(['data' => $kelasList]);
    }

    /**
     * Tambah Kelas Baru
     */
    public function simpan()
    {
        $rules = [
            'nama'            => 'required|min_length[1]|max_length[50]',
            'tingkat'         => 'required|integer',
            'tahun_ajaran_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $id = $this->kelasModel->insert([
            'nama'            => trim($this->request->getPost('nama')),
            'tingkat'         => (int)$this->request->getPost('tingkat'),
            'tahun_ajaran_id' => (int)$this->request->getPost('tahun_ajaran_id'),
        ]);

        LogAktivitasModel::catat('tambah_kelas', "Kelas: {$this->request->getPost('nama')}", ['kelas_id' => $id]);

        return redirect()->back()->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    /**
     * Update Kelas
     */
    public function update($id = null)
    {
        $kelas = $this->kelasModel->find($id);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        $rules = [
            'nama'            => 'required|min_length[1]|max_length[50]',
            'tingkat'         => 'required|integer',
            'tahun_ajaran_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->kelasModel->update($id, [
            'nama'            => trim($this->request->getPost('nama')),
            'tingkat'         => (int)$this->request->getPost('tingkat'),
            'tahun_ajaran_id' => (int)$this->request->getPost('tahun_ajaran_id'),
        ]);

        LogAktivitasModel::catat('ubah_kelas', "Kelas: {$this->request->getPost('nama')}", ['kelas_id' => $id]);

        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Hapus Kelas (Soft Delete)
     */
    public function hapus($id = null)
    {
        $kelas = $this->kelasModel->find($id);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        $this->kelasModel->delete($id);
        LogAktivitasModel::catat('hapus_kelas', "Kelas: {$kelas['nama']}", ['kelas_id' => $id]);

        return redirect()->back()->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Halaman Kelola Siswa di Kelas Tertentu
     */
    public function siswa($kelasId = null)
    {
        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            return redirect()->to(base_url('admin/kelas'))->with('error', 'Kelas tidak ditemukan.');
        }

        $ta = $this->taModel->find($kelas['tahun_ajaran_id']);
        $siswaInKelas = $this->siswaKelasModel->getSiswaByKelas((int)$kelasId);

        // Ambil siswa yang belum punya kelas pada tahun ajaran ini
        $assignedUserIds = $this->siswaKelasModel
            ->where('tahun_ajaran_id', $kelas['tahun_ajaran_id'])
            ->findColumn('user_id') ?: [0];

        $unassignedSiswa = $this->userModel
            ->where('role', 'siswa')
            ->where('aktif', 1)
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('nama', 'ASC')
            ->findAll();

        return view('admin/kelas/siswa', [
            'title'           => "Kelola Siswa — {$kelas['nama']}",
            'kelas'           => $kelas,
            'ta'              => $ta,
            'siswaInKelas'    => $siswaInKelas,
            'unassignedSiswa' => $unassignedSiswa,
        ]);
    }

    /**
     * Tambah Siswa ke Kelas
     */
    public function tambahSiswa()
    {
        $kelasId = (int)$this->request->getPost('kelas_id');
        $userId  = (int)$this->request->getPost('user_id');

        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        $this->siswaKelasModel->assignSiswa($userId, $kelasId, (int)$kelas['tahun_ajaran_id']);
        LogAktivitasModel::catat('tambah_siswa_kelas', "User ID {$userId} ke Kelas {$kelas['nama']}");

        return redirect()->back()->with('success', 'Siswa berhasil dimasukkan ke kelas.');
    }

    /**
     * Keluarkan Siswa dari Kelas
     */
    public function hapusSiswa($id = null)
    {
        $sk = $this->siswaKelasModel->find($id);
        if (!$sk) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $this->siswaKelasModel->delete($id);
        LogAktivitasModel::catat('keluarkan_siswa_kelas', "Hapus siswa_kelas ID {$id}");

        return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }
}

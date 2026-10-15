<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaketSoalModel;
use App\Models\UserModel;
use App\Models\MapelModel;
use App\Models\LogAktivitasModel;

class BankSoal extends BaseController
{
    protected PaketSoalModel $paketModel;
    protected UserModel $userModel;
    protected MapelModel $mapelModel;

    public function __construct()
    {
        $this->paketModel = new PaketSoalModel();
        $this->userModel  = new UserModel();
        $this->mapelModel = new MapelModel();
    }

    public function index()
    {
        $mapelList = $this->mapelModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();
        $guruList  = $this->userModel->where('deleted_at', null)
            ->whereIn('role', ['guru', 'admin'])
            ->orderBy('nama', 'ASC')
            ->findAll();

        return view('admin/bank_soal/index', [
            'title'     => 'Bank Soal',
            'pageTitle' => 'Bank Soal',
            'mapelList' => $mapelList,
            'guruList'  => $guruList,
        ]);
    }

    public function data()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('paket_soal ps')
            ->select('ps.id, ps.nama, ps.tingkat, ps.mapel_id, ps.pemilik_id, ps.created_at,
                      u.nama AS nama_pemilik, m.nama AS nama_mapel,
                      COUNT(s.id) AS jumlah_soal')
            ->join('users u', 'u.id = ps.pemilik_id', 'left')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->join('soal s', 's.paket_id = ps.id AND s.deleted_at IS NULL', 'left')
            ->where('ps.deleted_at', null);

        $guruId = $this->request->getGet('guru');
        if ($guruId) {
            $builder->where('ps.pemilik_id', (int)$guruId);
        }

        $data = $builder->groupBy('ps.id')
            ->orderBy('ps.created_at', 'DESC')
            ->get()->getResultArray();

        return $this->response->setJSON(['data' => $data]);
    }

    public function simpan()
    {
        $nama      = trim($this->request->getPost('nama'));
        $mapelId   = (int)$this->request->getPost('mapel_id');
        $tingkat   = $this->request->getPost('tingkat') ?: null;
        $pemilikId = (int)$this->request->getPost('pemilik_id');

        if (empty($nama)) {
            return redirect()->back()->withInput()->with('error', 'Nama paket bank soal wajib diisi.');
        }

        if (!$mapelId) {
            return redirect()->back()->withInput()->with('error', 'Mata pelajaran wajib dipilih.');
        }

        if (!$pemilikId) {
            // Default to logged in user if not selected
            $pemilikId = (int)session()->get('user_id');
        }

        $id = $this->paketModel->insert([
            'nama'       => $nama,
            'mapel_id'   => $mapelId,
            'tingkat'    => $tingkat,
            'pemilik_id' => $pemilikId,
        ]);

        LogAktivitasModel::catat('tambah_bank_soal', "Admin membuat Bank Soal: {$nama} (ID: {$id})");

        return redirect()->to(base_url('admin/bank-soal'))->with('success', 'Bank soal berhasil dibuat dan kini tersedia di halaman guru.');
    }

    public function update($id = null)
    {
        $paket = $this->paketModel->find($id);
        if (!$paket) {
            return redirect()->to(base_url('admin/bank-soal'))->with('error', 'Bank soal tidak ditemukan.');
        }

        $nama      = trim($this->request->getPost('nama'));
        $mapelId   = (int)$this->request->getPost('mapel_id');
        $tingkat   = $this->request->getPost('tingkat') ?: null;
        $pemilikId = (int)$this->request->getPost('pemilik_id');

        if (empty($nama)) {
            return redirect()->back()->withInput()->with('error', 'Nama paket bank soal wajib diisi.');
        }

        if (!$mapelId) {
            return redirect()->back()->withInput()->with('error', 'Mata pelajaran wajib dipilih.');
        }

        if (!$pemilikId) {
            $pemilikId = (int)$paket['pemilik_id'];
        }

        $this->paketModel->update($id, [
            'nama'       => $nama,
            'mapel_id'   => $mapelId,
            'tingkat'    => $tingkat,
            'pemilik_id' => $pemilikId,
        ]);

        LogAktivitasModel::catat('ubah_bank_soal', "Admin memperbarui Bank Soal ID: {$id}");

        return redirect()->to(base_url('admin/bank-soal'))->with('success', 'Data bank soal berhasil diperbarui.');
    }

    public function hapus($id = null)
    {
        $paket = $this->paketModel->find($id);
        if (!$paket) {
            return redirect()->to(base_url('admin/bank-soal'))->with('error', 'Bank soal tidak ditemukan.');
        }

        $this->paketModel->delete($id);
        LogAktivitasModel::catat('hapus_bank_soal', "Admin menghapus Bank Soal: {$paket['nama']}");

        return redirect()->to(base_url('admin/bank-soal'))->with('success', 'Bank soal berhasil dihapus.');
    }

    public function detail($id = null)
    {
        $db = \Config\Database::connect();
        $paket = $db->table('paket_soal ps')
            ->select('ps.*, u.nama AS nama_pemilik, m.nama AS nama_mapel')
            ->join('users u', 'u.id = ps.pemilik_id', 'left')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->where('ps.id', $id)
            ->where('ps.deleted_at', null)
            ->get()->getRowArray();

        if (!$paket) {
            return redirect()->to(base_url('admin/bank-soal'))->with('error', 'Paket soal tidak ditemukan.');
        }

        $soal = $db->table('soal s')
            ->where('s.paket_id', $id)
            ->where('s.deleted_at', null)
            ->orderBy('s.urutan', 'ASC')
            ->orderBy('s.id', 'ASC')
            ->get()->getResultArray();

        // Ambil opsi untuk setiap butir soal
        foreach ($soal as &$s) {
            $s['opsi'] = $db->table('soal_opsi')
                ->where('soal_id', $s['id'])
                ->orderBy('urutan', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()->getResultArray();
        }

        return view('admin/bank_soal/detail', [
            'title'     => 'Detail Bank Soal',
            'pageTitle' => 'Detail Paket Soal',
            'paket'     => $paket,
            'soal'      => $soal,
        ]);
    }
}

<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\PaketSoalModel;
use App\Models\SoalModel;
use App\Models\MapelModel;

class PaketSoal extends BaseController
{
    protected PaketSoalModel $paketModel;
    protected SoalModel $soalModel;
    protected MapelModel $mapelModel;

    public function __construct()
    {
        $this->paketModel = new PaketSoalModel();
        $this->soalModel  = new SoalModel();
        $this->mapelModel = new MapelModel();
    }

    public function index()
    {
        // Guru hanya melihat semua paket soal (bank soal dikelola admin)
        $paketList = $this->paketModel->getWithDetails(null, 'admin');
        $mapelList = $this->mapelModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();

        return view('guru/paket_soal/index', [
            'title'     => 'Bank Soal',
            'paketList' => $paketList,
            'mapelList' => $mapelList,
            'readOnly'  => true,
        ]);
    }

    /** Membuat paket soal hanya boleh dilakukan admin */
    public function buat()
    {
        return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Paket Bank Soal dibuat oleh Admin. Guru bertugas membuat dan mengelola butir soal di dalamnya.');
    }

    /** Menyimpan paket soal baru hanya boleh dilakukan admin */
    public function simpan()
    {
        return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Paket Bank Soal dibuat oleh Admin. Guru bertugas membuat dan mengelola butir soal di dalamnya.');
    }

    public function detail($id = null)
    {
        $paket = $this->paketModel->select('paket_soal.*, m.nama as nama_mapel, u.nama as nama_pemilik')
            ->join('mapel m', 'm.id = paket_soal.mapel_id', 'left')
            ->join('users u', 'u.id = paket_soal.pemilik_id', 'left')
            ->where('paket_soal.id', $id)
            ->where('paket_soal.deleted_at', null)
            ->first();

        if (!$paket) {
            return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Paket soal tidak ditemukan.');
        }

        $soalList  = $this->soalModel->getAllByPaket((int)$id);
        $mapelList = $this->mapelModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();

        return view('guru/paket_soal/detail', [
            'title'     => "Bank Soal: {$paket['nama']}",
            'paket'     => $paket,
            'soalList'  => $soalList,
            'mapelList' => $mapelList,
        ]);
    }

    /** Update paket soal hanya boleh dilakukan admin */
    public function update($id = null)
    {
        return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Identitas paket hanya dapat diubah oleh Admin.');
    }

    /** Hapus paket soal hanya boleh dilakukan admin */
    public function hapus($id = null)
    {
        return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Penghapusan paket soal hanya dapat dilakukan oleh Admin.');
    }

    /** Duplikat paket soal hanya boleh dilakukan admin */
    public function duplikat($id = null)
    {
        return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Duplikasi paket soal hanya dapat dilakukan oleh Admin.');
    }
}

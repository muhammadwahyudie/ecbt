<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\UjianModel;
use App\Models\UjianKelasModel;
use App\Models\PaketSoalModel;
use App\Models\KelasModel;
use App\Models\TahunAjaranModel;
use App\Models\LogAktivitasModel;

class Ujian extends BaseController
{
    protected UjianModel $ujianModel;
    protected UjianKelasModel $ujianKelasModel;
    protected PaketSoalModel $paketModel;
    protected KelasModel $kelasModel;
    protected TahunAjaranModel $taModel;

    public function __construct()
    {
        $this->ujianModel      = new UjianModel();
        $this->ujianKelasModel = new UjianKelasModel();
        $this->paketModel      = new PaketSoalModel();
        $this->kelasModel      = new KelasModel();
        $this->taModel         = new TahunAjaranModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $this->ujianModel->syncStatusWaktu();
        $ujianList = $this->ujianModel->getWithDetails($userId, 'guru');

        return view('guru/ujian/index', [
            'title'     => 'Kelola Ujian Saya',
            'ujianList' => $ujianList,
        ]);
    }

    public function buat()
    {
        $userId    = session()->get('user_id');
        $taAktif   = $this->taModel->getAktif();
        $paketList = $this->paketModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();
        $kelasList = $taAktif ? $this->kelasModel->where('tahun_ajaran_id', $taAktif['id'])->orderBy('tingkat', 'ASC')->orderBy('nama', 'ASC')->findAll() : [];
        $token     = UjianModel::generateToken();

        return view('guru/ujian/buat', [
            'title'     => 'Jadwalkan Ujian Baru',
            'paketList' => $paketList,
            'kelasList' => $kelasList,
            'token'     => $token,
        ]);
    }

    public function simpan()
    {
        $rules = [
            'nama'         => 'required|min_length[3]|max_length[200]',
            'paket_id'     => 'required|integer',
            'kelas_ids'    => 'required',
            'mulai_at'     => 'required',
            'selesai_at'   => 'required',
            'durasi_menit' => 'required|integer|greater_than[0]',
            'token'        => 'required|exact_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $userId = session()->get('user_id');
        $token  = strtoupper(trim($this->request->getPost('token')));

        $mulaiAt = date('Y-m-d H:i:s', strtotime($this->request->getPost('mulai_at')));
        $selesaiAt = date('Y-m-d H:i:s', strtotime($this->request->getPost('selesai_at')));
        if ($selesaiAt <= $mulaiAt) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai harus lebih besar dari waktu mulai.');
        }

        $now = date('Y-m-d H:i:s');
        $status = 'terjadwal';
        if ($now >= $mulaiAt && $now <= $selesaiAt) {
            $status = 'berlangsung';
        }

        $tokenMasaMenit = (int)$this->request->getPost('token_masa_menit') ?: 120;
        $tokenBerlaku = date('Y-m-d H:i:s', strtotime($selesaiAt) + ($tokenMasaMenit * 60));

        $ujianId = $this->ujianModel->insert([
            'nama'                 => trim($this->request->getPost('nama')),
            'paket_id'             => (int)$this->request->getPost('paket_id'),
            'pembuat_id'           => $userId,
            'mulai_at'             => $mulaiAt,
            'selesai_at'           => $selesaiAt,
            'durasi_menit'         => (int)$this->request->getPost('durasi_menit'),
            'acak_soal'            => $this->request->getPost('acak_soal') ? 1 : 0,
            'acak_opsi'            => $this->request->getPost('acak_opsi') ? 1 : 0,
            'tampil_hasil'         => $this->request->getPost('tampil_hasil') ?: 'tidak',
            'nilai_minimal'        => (float)$this->request->getPost('nilai_minimal') ?: 0.00,
            'batas_terlambat'      => (int)$this->request->getPost('batas_terlambat') ?: 0,
            'batas_pelanggaran'    => (int)$this->request->getPost('batas_pelanggaran') ?: 3,
            'token'                => $token,
            'token_berlaku_sampai' => $tokenBerlaku,
            'status'               => $status,
        ]);

        $kelasIds = (array)$this->request->getPost('kelas_ids');
        $this->ujianKelasModel->syncKelasDanPeserta((int)$ujianId, $kelasIds);

        LogAktivitasModel::catat('guru_buat_ujian', "Ujian: {$this->request->getPost('nama')}, Token: {$token}", ['ujian_id' => $ujianId]);

        return redirect()->to(base_url("guru/ujian/{$ujianId}"))->with('success', 'Ujian berhasil dijadwalkan.');
    }

    public function detail($id = null)
    {
        $userId = session()->get('user_id');
        $this->ujianModel->syncStatusWaktu();
        $ujian = $this->ujianModel->select('ujian.*, ps.nama as nama_paket, m.nama as nama_mapel')
            ->join('paket_soal ps', 'ps.id = ujian.paket_id')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->where('ujian.id', $id)
            ->where('ujian.pembuat_id', $userId)
            ->where('ujian.deleted_at', null)
            ->first();

        if (!$ujian) {
            return redirect()->to(base_url('guru/ujian'))->with('error', 'Ujian tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $kelasList = $db->table('ujian_kelas uk')
            ->select('k.nama, k.tingkat')
            ->join('kelas k', 'k.id = uk.kelas_id')
            ->where('uk.ujian_id', $id)
            ->get()->getResultArray();

        $pesertaStat = $db->table('ujian_peserta')
            ->select('status, COUNT(id) as jml')
            ->where('ujian_id', $id)
            ->groupBy('status')
            ->get()->getResultArray();

        $stats = ['belum_login' => 0, 'mengerjakan' => 0, 'selesai' => 0, 'terkunci' => 0];
        foreach ($pesertaStat as $ps) {
            $stats[$ps['status']] = (int)$ps['jml'];
        }

        return view('guru/ujian/detail', [
            'title'     => "Detail Ujian — {$ujian['nama']}",
            'ujian'     => $ujian,
            'kelasList' => $kelasList,
            'stats'     => $stats,
        ]);
    }

    public function update($id = null)
    {
        $userId = session()->get('user_id');
        $ujian = $this->ujianModel->where('id', $id)->where('pembuat_id', $userId)->first();
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        $mulaiAt = date('Y-m-d H:i:s', strtotime($this->request->getPost('mulai_at')));
        $selesaiAt = date('Y-m-d H:i:s', strtotime($this->request->getPost('selesai_at')));

        $this->ujianModel->update($id, [
            'nama'              => trim($this->request->getPost('nama')),
            'mulai_at'          => $mulaiAt,
            'selesai_at'        => $selesaiAt,
            'durasi_menit'      => (int)$this->request->getPost('durasi_menit'),
            'acak_soal'         => $this->request->getPost('acak_soal') ? 1 : 0,
            'acak_opsi'         => $this->request->getPost('acak_opsi') ? 1 : 0,
            'tampil_hasil'      => $this->request->getPost('tampil_hasil') ?: 'tidak',
            'nilai_minimal'     => (float)$this->request->getPost('nilai_minimal') ?: 0.00,
            'batas_terlambat'   => (int)$this->request->getPost('batas_terlambat') ?: 0,
            'batas_pelanggaran' => (int)$this->request->getPost('batas_pelanggaran') ?: 3,
        ]);

        LogAktivitasModel::catat('guru_ubah_ujian', "Ujian ID: {$id}");

        return redirect()->back()->with('success', 'Ujian berhasil diperbarui.');
    }

    public function hapus($id = null)
    {
        $userId = session()->get('user_id');
        $ujian = $this->ujianModel->where('id', $id)->where('pembuat_id', $userId)->first();
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        if ($ujian['status'] === 'berlangsung') {
            return redirect()->back()->with('error', 'Ujian berlangsung tidak dapat dihapus.');
        }

        $this->ujianModel->delete($id);
        LogAktivitasModel::catat('guru_hapus_ujian', "Ujian: {$ujian['nama']}");

        return redirect()->to(base_url('guru/ujian'))->with('success', 'Ujian berhasil dihapus.');
    }

    public function regenerateToken($id = null)
    {
        $userId = session()->get('user_id');
        $ujian = $this->ujianModel->where('id', $id)->where('pembuat_id', $userId)->first();
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        $newToken = UjianModel::generateToken();
        $this->ujianModel->update($id, [
            'token' => $newToken,
            'token_berlaku_sampai' => date('Y-m-d H:i:s', time() + (2 * 3600)),
        ]);

        LogAktivitasModel::catat('guru_regenerate_token', "Ujian {$ujian['nama']} token: {$newToken}");

        return redirect()->back()->with('success', "Token baru berhasil dibuat: <strong>{$newToken}</strong>");
    }
}

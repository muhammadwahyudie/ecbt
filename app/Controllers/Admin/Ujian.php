<?php

namespace App\Controllers\Admin;

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
        $this->ujianModel->syncStatusWaktu();
        $ujianList = $this->ujianModel->getWithDetails();

        return view('admin/ujian/index', [
            'title'     => 'Jadwal & Manajemen Ujian',
            'ujianList' => $ujianList,
        ]);
    }

    public function buat()
    {
        $taAktif   = $this->taModel->getAktif();
        $paketList = $this->paketModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();
        $kelasList = $taAktif ? $this->kelasModel->where('tahun_ajaran_id', $taAktif['id'])->orderBy('tingkat', 'ASC')->orderBy('nama', 'ASC')->findAll() : [];
        $token     = UjianModel::generateToken();

        return view('admin/ujian/buat', [
            'title'     => 'Jadwalkan Ujian Baru',
            'paketList' => $paketList,
            'kelasList' => $kelasList,
            'token'     => $token,
        ]);
    }

    public function simpan()
    {
        $rules = [
            'nama'            => 'required|min_length[3]|max_length[200]',
            'paket_id'        => 'required|integer',
            'kelas_ids'       => 'required',
            'mulai_at'        => 'required',
            'selesai_at'      => 'required',
            'durasi_menit'    => 'required|integer|greater_than[0]',
            'token'           => 'required|exact_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $userId = session()->get('user_id');
        $token  = strtoupper(trim($this->request->getPost('token')));

        // Validasi waktu selesai harus setelah waktu mulai
        $mulaiAt = date('Y-m-d H:i:s', strtotime($this->request->getPost('mulai_at')));
        $selesaiAt = date('Y-m-d H:i:s', strtotime($this->request->getPost('selesai_at')));
        if ($selesaiAt <= $mulaiAt) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai ujian harus lebih besar dari waktu mulai.');
        }

        // Tentukan status awal
        $now = date('Y-m-d H:i:s');
        $status = 'terjadwal';
        if ($now >= $mulaiAt && $now <= $selesaiAt) {
            $status = 'berlangsung';
        } elseif ($now > $selesaiAt) {
            $status = 'selesai';
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

        // Hubungkan ke kelas-kelas peserta
        $kelasIds = (array)$this->request->getPost('kelas_ids');
        $this->ujianKelasModel->syncKelasDanPeserta((int)$ujianId, $kelasIds);

        LogAktivitasModel::catat('buat_ujian', "Ujian: {$this->request->getPost('nama')}, Token: {$token}", ['ujian_id' => $ujianId]);

        return redirect()->to(base_url("admin/ujian/{$ujianId}"))->with('success', 'Ujian berhasil dijadwalkan dan peserta terdaftar.');
    }

    public function detail($id = null)
    {
        $this->ujianModel->syncStatusWaktu();
        $ujian = $this->ujianModel->select('ujian.*, ps.nama as nama_paket, m.nama as nama_mapel, u.nama as nama_pembuat')
            ->join('paket_soal ps', 'ps.id = ujian.paket_id')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->join('users u', 'u.id = ujian.pembuat_id', 'left')
            ->where('ujian.id', $id)
            ->where('ujian.deleted_at', null)
            ->first();

        if (!$ujian) {
            return redirect()->to(base_url('admin/ujian'))->with('error', 'Ujian tidak ditemukan.');
        }

        // Ambil kelas peserta
        $db = \Config\Database::connect();
        $kelasList = $db->table('ujian_kelas uk')
            ->select('k.nama, k.tingkat')
            ->join('kelas k', 'k.id = uk.kelas_id')
            ->where('uk.ujian_id', $id)
            ->get()->getResultArray();

        // Ambil rekap status peserta
        $pesertaStat = $db->table('ujian_peserta')
            ->select('status, COUNT(id) as jml')
            ->where('ujian_id', $id)
            ->groupBy('status')
            ->get()->getResultArray();

        $stats = ['belum_login' => 0, 'mengerjakan' => 0, 'selesai' => 0, 'terkunci' => 0];
        foreach ($pesertaStat as $ps) {
            $stats[$ps['status']] = (int)$ps['jml'];
        }

        return view('admin/ujian/detail', [
            'title'     => "Detail Ujian — {$ujian['nama']}",
            'ujian'     => $ujian,
            'kelasList' => $kelasList,
            'stats'     => $stats,
        ]);
    }

    public function update($id = null)
    {
        $ujian = $this->ujianModel->find($id);
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        $rules = [
            'nama'         => 'required|min_length[3]|max_length[200]',
            'mulai_at'     => 'required',
            'selesai_at'   => 'required',
            'durasi_menit' => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
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

        LogAktivitasModel::catat('ubah_ujian', "Ujian ID: {$id}");

        return redirect()->back()->with('success', 'Pengaturan ujian berhasil diperbarui.');
    }

    public function hapus($id = null)
    {
        $ujian = $this->ujianModel->find($id);
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        if ($ujian['status'] === 'berlangsung') {
            return redirect()->back()->with('error', 'Ujian yang sedang berlangsung tidak dapat dihapus.');
        }

        $this->ujianModel->delete($id);
        LogAktivitasModel::catat('hapus_ujian', "Ujian: {$ujian['nama']}", ['ujian_id' => $id]);

        return redirect()->to(base_url('admin/ujian'))->with('success', 'Ujian berhasil dihapus.');
    }

    /**
     * Regenerate Token Ujian
     */
    public function regenerateToken($id = null)
    {
        $ujian = $this->ujianModel->find($id);
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        $newToken = UjianModel::generateToken();
        $this->ujianModel->update($id, [
            'token' => $newToken,
            'token_berlaku_sampai' => date('Y-m-d H:i:s', time() + (2 * 3600)), // 2 jam dari sekarang
        ]);

        LogAktivitasModel::catat('regenerate_token', "Ujian {$ujian['nama']} token baru: {$newToken}", ['ujian_id' => $id]);

        return redirect()->back()->with('success', "Token baru berhasil dibuat: <strong>{$newToken}</strong>");
    }
}

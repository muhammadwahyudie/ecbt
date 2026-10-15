<?php

namespace App\Controllers\Proktor;

use App\Controllers\BaseController;
use App\Models\UjianModel;
use App\Models\UjianPesertaModel;
use App\Models\RuangModel;
use App\Models\UserModel;
use App\Models\HasilModel;
use App\Models\LogAktivitasModel;

class Monitoring extends BaseController
{
    protected UjianModel $ujianModel;
    protected UjianPesertaModel $pesertaModel;
    protected RuangModel $ruangModel;
    protected UserModel $userModel;
    protected HasilModel $hasilModel;

    public function __construct()
    {
        $this->ujianModel   = new UjianModel();
        $this->pesertaModel = new UjianPesertaModel();
        $this->ruangModel   = new RuangModel();
        $this->userModel    = new UserModel();
        $this->hasilModel   = new HasilModel();
    }

    public function index($ujianId = null)
    {
        $this->ujianModel->syncStatusWaktu();

        // Ambil daftar ujian aktif & terjadwal
        $ujianList = $this->ujianModel->where('deleted_at', null)
            ->whereIn('status', ['berlangsung', 'terjadwal', 'selesai'])
            ->orderBy("FIELD(status, 'berlangsung', 'terjadwal', 'selesai')", '', false)
            ->orderBy('mulai_at', 'DESC')
            ->findAll();

        if (empty($ujianList)) {
            return view('proktor/monitoring/kosong', [
                'title' => 'Monitoring Ujian Proktor',
            ]);
        }

        $selectedUjianId = $ujianId ?: ($ujianList[0]['id'] ?? 0);
        $selectedUjian   = $this->ujianModel->find($selectedUjianId);
        $ruangList       = $this->ruangModel->where('deleted_at', null)->orderBy('nama', 'ASC')->findAll();

        return view('proktor/monitoring/index', [
            'title'         => 'Live Monitoring Pengawasan Ujian',
            'ujianList'     => $ujianList,
            'selectedUjian' => $selectedUjian,
            'ruangList'     => $ruangList,
        ]);
    }

    /**
     * Data JSON Polling untuk Live Monitoring (Setiap 10 Detik)
     */
    public function data()
    {
        $ujianId = (int)$this->request->getGet('ujian_id');
        $ruangId = $this->request->getGet('ruang_id') ? (int)$this->request->getGet('ruang_id') : null;
        $status  = $this->request->getGet('status');

        if (!$ujianId) {
            return $this->response->setJSON(['data' => []]);
        }

        $peserta = $this->pesertaModel->getMonitoringData($ujianId, $ruangId, $status);

        // Hitung persentase progres & status online (heartbeat < 60 detik)
        $now = time();
        foreach ($peserta as &$p) {
            $total = (int)($p['total_soal'] ?? 0);
            $jawab = (int)($p['dijawab'] ?? 0);
            $p['persen'] = ($total > 0) ? round(($jawab / $total) * 100) : 0;

            $lastSeenTs = $p['last_seen_at'] ? strtotime($p['last_seen_at']) : 0;
            $p['is_online'] = ($p['status'] === 'mengerjakan' && ($now - $lastSeenTs) <= 60);
        }

        return $this->response->setJSON(['data' => $peserta]);
    }

    /**
     * Reset Login Peserta
     */
    public function resetLogin($pesertaId = null)
    {
        $peserta = $this->pesertaModel->find($pesertaId);
        if (!$peserta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Peserta tidak ditemukan.']);
        }

        // Hapus session_token pada user
        $this->userModel->clearSessionToken((int)$peserta['user_id']);
        $this->userModel->resetLoginGagal((int)$peserta['user_id']);

        LogAktivitasModel::catat('proktor_reset_login', "Reset login peserta ID {$pesertaId}");

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Login peserta berhasil direset. Siswa dapat login kembali.'
        ]);
    }

    /**
     * Buka Kunci Peserta
     */
    public function bukaKunci($pesertaId = null)
    {
        $peserta = $this->pesertaModel->find($pesertaId);
        if (!$peserta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Peserta tidak ditemukan.']);
        }

        $this->pesertaModel->update($pesertaId, [
            'terkunci'    => 0,
            'status'      => 'mengerjakan',
            'pelanggaran' => 0, // Reset pelanggaran agar siswa bisa lanjut
        ]);

        LogAktivitasModel::catat('proktor_buka_kunci', "Buka kunci peserta ID {$pesertaId}");

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Kunci ujian peserta berhasil dibuka.'
        ]);
    }

    /**
     * Tambah Waktu Pengerjaan Peserta
     */
    public function tambahWaktu($pesertaId = null)
    {
        $peserta = $this->pesertaModel->find($pesertaId);
        if (!$peserta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Peserta tidak ditemukan.']);
        }

        $menit = (int)$this->request->getPost('menit') ?: 10;
        $totalTambahan = ($peserta['tambahan_menit'] ?? 0) + $menit;

        $this->pesertaModel->update($pesertaId, [
            'tambahan_menit' => $totalTambahan,
        ]);

        LogAktivitasModel::catat('proktor_tambah_waktu', "Tambah {$menit} menit peserta ID {$pesertaId}");

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "Waktu berhasil ditambah {$menit} menit untuk peserta."
        ]);
    }

    /**
     * Paksa Selesai Peserta
     */
    public function paksaSelesai($pesertaId = null)
    {
        $peserta = $this->pesertaModel->find($pesertaId);
        if (!$peserta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Peserta tidak ditemukan.']);
        }

        $this->pesertaModel->update($pesertaId, [
            'status'     => 'selesai',
            'selesai_at' => date('Y-m-d H:i:s'),
        ]);

        // Hitung hasil otomatis
        $this->hasilModel->hitungHasilUjian((int)$pesertaId);

        LogAktivitasModel::catat('proktor_paksa_selesai', "Paksa selesai peserta ID {$pesertaId}");

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Ujian peserta telah dipaksa selesai dan dinilai.'
        ]);
    }
}

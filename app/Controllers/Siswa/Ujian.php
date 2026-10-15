<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\UjianModel;
use App\Models\UjianPesertaModel;
use App\Models\UrutanSoalModel;
use App\Models\JawabanModel;
use App\Models\PelanggaranModel;
use App\Models\HasilModel;
use App\Models\LogAktivitasModel;

class Ujian extends BaseController
{
    protected UjianModel $ujianModel;
    protected UjianPesertaModel $pesertaModel;
    protected UrutanSoalModel $urutanModel;
    protected JawabanModel $jawabanModel;
    protected PelanggaranModel $pelanggaranModel;
    protected HasilModel $hasilModel;

    public function __construct()
    {
        $this->ujianModel       = new UjianModel();
        $this->pesertaModel     = new UjianPesertaModel();
        $this->urutanModel      = new UrutanSoalModel();
        $this->jawabanModel     = new JawabanModel();
        $this->pelanggaranModel = new PelanggaranModel();
        $this->hasilModel       = new HasilModel();
    }

    public function index()
    {
        return redirect()->to(base_url('siswa/dashboard'));
    }

    /**
     * Masuk / Konfirmasi Token Ujian
     */
    public function masuk()
    {
        $token   = strtoupper(trim($this->request->getPost('token') ?? ''));
        $ujianId = $this->request->getPost('ujian_id');
        $userId  = session()->get('user_id');

        $this->ujianModel->syncStatusWaktu();

        // Cari ujian berdasarkan ID atau Token
        $builder = $this->ujianModel->where('deleted_at', null);
        if (!empty($ujianId)) {
            $builder->where('id', (int)$ujianId);
        }
        if (!empty($token)) {
            $builder->where('token', $token);
        }

        $ujian = $builder->first();
        if (!$ujian) {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Token atau jadwal ujian tidak valid.');
        }

        // Cek masa berlaku token
        if ($ujian['token_berlaku_sampai'] && strtotime($ujian['token_berlaku_sampai']) < time()) {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Token ujian telah kadaluarsa. Silakan minta token baru kepada proktor/pengawas.');
        }

        // Cek apakah siswa terdaftar sebagai peserta
        $peserta = $this->pesertaModel->where('ujian_id', $ujian['id'])->where('user_id', $userId)->first();
        if (!$peserta) {
            // Cek apakah kelas siswa terdaftar di ujian_kelas
            $db = \Config\Database::connect();
            $sk = $db->table('siswa_kelas')->where('user_id', $userId)->orderBy('id', 'DESC')->get()->getRowArray();
            $kelasTerdaftar = false;
            if ($sk) {
                $uk = $db->table('ujian_kelas')->where('ujian_id', $ujian['id'])->where('kelas_id', $sk['kelas_id'])->get()->getRowArray();
                if ($uk) {
                    $kelasTerdaftar = true;
                    $pesertaId = $this->pesertaModel->insert([
                        'ujian_id'       => $ujian['id'],
                        'user_id'        => $userId,
                        'status'         => 'belum_login',
                        'tambahan_menit' => 0,
                        'pelanggaran'    => 0,
                        'terkunci'       => 0,
                        'created_at'     => date('Y-m-d H:i:s'),
                    ]);
                    $peserta = $this->pesertaModel->find($pesertaId);
                }
            }

            if (!$kelasTerdaftar) {
                return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Anda tidak terdaftar sebagai peserta dalam sesi ujian ini.');
            }
        }

        if ($peserta['status'] === 'selesai') {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Anda telah menyelesaikan ujian ini.');
        }

        if ($peserta['terkunci'] == 1 || $peserta['status'] === 'terkunci') {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Akun ujian Anda terkunci karena pelanggaran. Silakan hubungi proktor/pengawas ruang.');
        }

        // Jika sudah sedang mengerjakan, langsung arahkan ke layar pengerjaan
        if ($peserta['status'] === 'mengerjakan') {
            return redirect()->to(base_url('siswa/ujian/kerjakan/' . $ujian['id']));
        }

        return redirect()->to(base_url('siswa/ujian/tata-tertib/' . $ujian['id']));
    }

    /**
     * Halaman Tata Tertib Ujian
     */
    public function tataTertib($ujianId = null)
    {
        $userId = session()->get('user_id');
        $ujian  = $this->ujianModel->find($ujianId);
        $peserta = $this->pesertaModel->where('ujian_id', $ujianId)->where('user_id', $userId)->first();

        if (!$ujian || !$peserta) {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Sesi ujian tidak valid.');
        }

        if ($peserta['status'] === 'selesai') {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Anda telah menyelesaikan ujian ini.');
        }

        if ($peserta['terkunci'] == 1) {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Akun ujian Anda terkunci. Hubungi proktor.');
        }

        return view('siswa/ujian/tata_tertib', [
            'title'   => 'Tata Tertib Ujian',
            'ujian'   => $ujian,
            'peserta' => $peserta,
        ]);
    }

    /**
     * Mulai Mengerjakan Ujian
     */
    public function mulai($ujianId = null)
    {
        $userId = session()->get('user_id');
        $ujian  = $this->ujianModel->find($ujianId);
        $peserta = $this->pesertaModel->where('ujian_id', $ujianId)->where('user_id', $userId)->first();

        if (!$ujian || !$peserta) {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Ujian tidak valid.');
        }

        if ($peserta['status'] === 'selesai') {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Anda telah menyelesaikan ujian ini.');
        }

        $now = date('Y-m-d H:i:s');

        // Jika baru pertama kali masuk
        if ($peserta['status'] === 'belum_login') {
            $this->pesertaModel->update($peserta['id'], [
                'status'       => 'mengerjakan',
                'mulai_at'     => $now,
                'last_seen_at' => $now,
            ]);

            // Acak urutan soal dan opsi untuk siswa ini
            $this->urutanModel->generateUrutanPeserta(
                (int)$peserta['id'],
                (int)$ujian['paket_id'],
                (bool)$ujian['acak_soal'],
                (bool)$ujian['acak_opsi']
            );

            LogAktivitasModel::catat('siswa_mulai_ujian', "Siswa {$userId} mulai ujian {$ujian['nama']}");
        }

        return redirect()->to(base_url('siswa/ujian/kerjakan/' . $ujianId));
    }

    /**
     * Layar Utama Pengerjaan Ujian
     */
    public function kerjakan($ujianId = null)
    {
        $userId  = session()->get('user_id');
        $ujian   = $this->ujianModel->find($ujianId);
        $peserta = $this->pesertaModel->where('ujian_id', $ujianId)->where('user_id', $userId)->first();

        if (!$ujian || !$peserta) {
            return redirect()->to(base_url('siswa/dashboard'))->with('error', 'Ujian tidak ditemukan.');
        }

        if ($peserta['status'] === 'selesai') {
            return redirect()->to(base_url('siswa/ujian/ringkasan/' . $ujianId));
        }

        if ($peserta['terkunci'] == 1 || $peserta['status'] === 'terkunci') {
            return view('siswa/ujian/terkunci', [
                'title'   => 'Ujian Terkunci',
                'ujian'   => $ujian,
                'peserta' => $peserta,
            ]);
        }

        // Hitung sisa waktu dalam detik
        $nowTs = time();
        $mulaiTs = strtotime($peserta['mulai_at'] ?? date('Y-m-d H:i:s'));
        $durasiTotalDetik = (($ujian['durasi_menit'] + ($peserta['tambahan_menit'] ?? 0)) * 60);
        $waktuHabisPesertaTs = $mulaiTs + $durasiTotalDetik;
        $waktuTutupUjianTs   = strtotime($ujian['selesai_at']);

        $deadlineTs = min($waktuHabisPesertaTs, $waktuTutupUjianTs);
        $sisaDetik  = max(0, $deadlineTs - $nowTs);

        // Jika waktu sudah habis otomatis selesaikan
        if ($sisaDetik <= 0) {
            return $this->selesai($ujianId);
        }

        // Ambil daftar nomor & soal yang sudah diacak untuk peserta ini
        $db = \Config\Database::connect();
        $urutanList = $this->urutanModel->where('peserta_id', $peserta['id'])->orderBy('urutan', 'ASC')->findAll();

        if (empty($urutanList)) {
            // Generate jika belum ada
            $urutanList = $this->urutanModel->generateUrutanPeserta(
                (int)$peserta['id'],
                (int)$ujian['paket_id'],
                (bool)$ujian['acak_soal'],
                (bool)$ujian['acak_opsi']
            );
        }

        // Ambil data soal dan opsi sesuai urutan
        $soalList = [];
        $jawabanSaved = $this->jawabanModel->where('peserta_id', $peserta['id'])->findAll();
        $jawabanMap = [];
        foreach ($jawabanSaved as $jw) {
            $jawabanMap[$jw['soal_id']] = $jw;
        }

        foreach ($urutanList as $uItem) {
            $s = $db->table('soal')->where('id', $uItem['soal_id'])->get()->getRowArray();
            if (!$s) continue;

            $s['urutan_tampil'] = $uItem['urutan'];
            $s['jawaban_siswa'] = $jawabanMap[$s['id']]['jawaban'] ?? null;
            $s['ragu_siswa']    = $jawabanMap[$s['id']]['ragu'] ?? 0;

            // Load opsi
            $opsiList = $db->table('soal_opsi')->where('soal_id', $s['id'])->get()->getResultArray();

            // Jika ada urutan opsi acak
            if (!empty($uItem['urutan_opsi'])) {
                $opsiOrder = json_decode($uItem['urutan_opsi'], true);
                if (is_array($opsiOrder)) {
                    $sortedOpsi = [];
                    $opsiById = [];
                    foreach ($opsiList as $op) { $opsiById[$op['id']] = $op; }
                    foreach ($opsiOrder as $opId) {
                        if (isset($opsiById[$opId])) {
                            $sortedOpsi[] = $opsiById[$opId];
                        }
                    }
                    $opsiList = $sortedOpsi;
                }
            }

            $s['opsi'] = $opsiList;
            $soalList[] = $s;
        }

        // Update heartbeat last seen
        $this->pesertaModel->update($peserta['id'], ['last_seen_at' => date('Y-m-d H:i:s')]);

        return view('siswa/ujian/kerjakan', [
            'title'     => $ujian['nama'],
            'ujian'     => $ujian,
            'peserta'   => $peserta,
            'soalList'  => $soalList,
            'sisaDetik' => $sisaDetik,
        ]);
    }

    /**
     * AJAX Autosave Jawaban Siswa
     */
    public function simpanJawaban()
    {
        $userId  = session()->get('user_id');
        $ujianId = (int)$this->request->getPost('ujian_id');
        $soalId  = (int)$this->request->getPost('soal_id');
        $jawaban = $this->request->getPost('jawaban');
        $ragu    = (int)$this->request->getPost('ragu') ?: 0;

        $peserta = $this->pesertaModel->where('ujian_id', $ujianId)->where('user_id', $userId)->first();
        if (!$peserta || $peserta['status'] === 'selesai' || $peserta['terkunci'] == 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Status ujian tidak valid.']);
        }

        $this->jawabanModel->simpanJawaban((int)$peserta['id'], $soalId, $jawaban, $ragu);
        $this->pesertaModel->update($peserta['id'], ['last_seen_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Jawaban berhasil disimpan'
        ]);
    }

    /**
     * AJAX Catat Pelanggaran (Pindah Tab / Keluar Fullscreen)
     */
    public function catatPelanggaran()
    {
        $userId     = session()->get('user_id');
        $ujianId    = (int)$this->request->getPost('ujian_id');
        $jenis      = $this->request->getPost('jenis') ?: 'keluar_tab';
        $keterangan = $this->request->getPost('keterangan');

        $peserta = $this->pesertaModel->where('ujian_id', $ujianId)->where('user_id', $userId)->first();
        if (!$peserta) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $result = $this->pelanggaranModel->catatPelanggaran((int)$peserta['id'], $jenis, $keterangan);

        return $this->response->setJSON([
            'status'   => 'success',
            'total'    => $result['total'],
            'terkunci' => $result['terkunci'],
            'sisa'     => $result['sisa'],
        ]);
    }

    /**
     * Selesaikan Ujian & Hitung Nilai Otomatis
     */
    public function selesai($ujianId = null)
    {
        $userId  = session()->get('user_id');
        $peserta = $this->pesertaModel->where('ujian_id', $ujianId)->where('user_id', $userId)->first();

        if (!$peserta) {
            return redirect()->to(base_url('siswa/dashboard'));
        }

        if ($peserta['status'] !== 'selesai') {
            $now = date('Y-m-d H:i:s');
            $this->pesertaModel->update($peserta['id'], [
                'status'     => 'selesai',
                'selesai_at' => $now,
            ]);

            // Hitung hasil otomatis
            $this->hasilModel->hitungHasilUjian((int)$peserta['id']);

            LogAktivitasModel::catat('siswa_selesai_ujian', "Siswa {$userId} menyelesaikan ujian {$ujianId}");
        }

        return redirect()->to(base_url('siswa/ujian/ringkasan/' . $ujianId));
    }

    /**
     * Halaman Ringkasan Setelah Ujian Selesai
     */
    public function ringkasan($ujianId = null)
    {
        $userId  = session()->get('user_id');
        $ujian   = $this->ujianModel->find($ujianId);
        $peserta = $this->pesertaModel->where('ujian_id', $ujianId)->where('user_id', $userId)->first();

        if (!$ujian || !$peserta) {
            return redirect()->to(base_url('siswa/dashboard'));
        }

        $hasil = $this->hasilModel->where('peserta_id', $peserta['id'])->first();

        return view('siswa/ujian/ringkasan', [
            'title'   => 'Ujian Selesai',
            'ujian'   => $ujian,
            'peserta' => $peserta,
            'hasil'   => $hasil,
        ]);
    }
}

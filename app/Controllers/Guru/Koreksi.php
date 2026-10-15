<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\UjianModel;
use App\Models\UjianPesertaModel;
use App\Models\HasilModel;
use App\Models\JawabanModel;
use App\Models\SoalModel;
use App\Models\LogAktivitasModel;

class Koreksi extends BaseController
{
    protected UjianModel $ujianModel;
    protected UjianPesertaModel $pesertaModel;
    protected HasilModel $hasilModel;
    protected JawabanModel $jawabanModel;
    protected SoalModel $soalModel;

    public function __construct()
    {
        $this->ujianModel   = new UjianModel();
        $this->pesertaModel = new UjianPesertaModel();
        $this->hasilModel   = new HasilModel();
        $this->jawabanModel = new JawabanModel();
        $this->soalModel    = new SoalModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Ujian milik guru yang memiliki soal uraian
        $ujianList = $db->query("
            SELECT u.*, ps.nama as nama_paket, m.nama as nama_mapel,
                   COUNT(DISTINCT up.id) as total_peserta,
                   SUM(CASE WHEN h.status_koreksi IN ('belum', 'sebagian') THEN 1 ELSE 0 END) as belum_koreksi
            FROM ujian u
            JOIN paket_soal ps ON ps.id = u.paket_id
            LEFT JOIN mapel m ON m.id = ps.mapel_id
            JOIN soal s ON s.paket_id = ps.id AND s.tipe = 'uraian' AND s.deleted_at IS NULL
            LEFT JOIN ujian_peserta up ON up.ujian_id = u.id AND up.status = 'selesai'
            LEFT JOIN hasil h ON h.peserta_id = up.id
            WHERE u.pembuat_id = {$userId} AND u.deleted_at IS NULL
            GROUP BY u.id
            ORDER BY u.mulai_at DESC
        ")->getResultArray();

        return view('guru/koreksi/index', [
            'title'     => 'Koreksi Nilai Uraian',
            'ujianList' => $ujianList,
        ]);
    }

    /**
     * Daftar Peserta Ujian yang Memerlukan Koreksi Uraian
     */
    public function ujian($ujianId = null)
    {
        $userId = session()->get('user_id');
        $ujian  = $this->ujianModel->where('id', $ujianId)->where('pembuat_id', $userId)->first();
        if (!$ujian) {
            return redirect()->to(base_url('guru/koreksi'))->with('error', 'Ujian tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $pesertaList = $db->query("
            SELECT up.*, u.nama, u.username, u.nis, k.nama as nama_kelas,
                   h.skor_objektif, h.skor_uraian, h.nilai_akhir, h.status_koreksi
            FROM ujian_peserta up
            JOIN users u ON u.id = up.user_id
            LEFT JOIN siswa_kelas sk ON sk.user_id = u.id
            LEFT JOIN kelas k ON k.id = sk.kelas_id
            LEFT JOIN hasil h ON h.peserta_id = up.id
            WHERE up.ujian_id = {$ujianId} AND up.status = 'selesai'
            ORDER BY k.nama ASC, u.nama ASC
        ")->getResultArray();

        return view('guru/koreksi/peserta_list', [
            'title'       => "Antrean Koreksi — {$ujian['nama']}",
            'ujian'       => $ujian,
            'pesertaList' => $pesertaList,
        ]);
    }

    /**
     * Lembar Koreksi Jawaban Uraian Satu Peserta
     */
    public function peserta($pesertaId = null)
    {
        $db = \Config\Database::connect();
        $peserta = $this->pesertaModel->find($pesertaId);
        if (!$peserta) {
            return redirect()->back()->with('error', 'Peserta tidak ditemukan.');
        }

        $ujian = $this->ujianModel->find($peserta['ujian_id']);
        $siswa = (new \App\Models\UserModel())->find($peserta['user_id']);
        $hasil = $this->hasilModel->where('peserta_id', $pesertaId)->first();

        // Ambil butir-butir soal uraian dan jawaban siswa
        $soalUraian = $db->query("
            SELECT s.*, j.id as jawaban_id, j.jawaban, j.skor, j.dinilai_oleh
            FROM soal s
            LEFT JOIN jawaban j ON j.soal_id = s.id AND j.peserta_id = {$pesertaId}
            WHERE s.paket_id = {$ujian['paket_id']}
              AND s.tipe = 'uraian'
              AND s.deleted_at IS NULL
            ORDER BY s.urutan ASC
        ")->getResultArray();

        return view('guru/koreksi/lembar_koreksi', [
            'title'      => "Koreksi Jawaban — {$siswa['nama']}",
            'ujian'      => $ujian,
            'peserta'    => $peserta,
            'siswa'      => $siswa,
            'hasil'      => $hasil,
            'soalUraian' => $soalUraian,
        ]);
    }

    /**
     * Simpan Hasil Koreksi Jawaban Uraian
     */
    public function simpan()
    {
        $pesertaId = (int)$this->request->getPost('peserta_id');
        $skorInputs = $this->request->getPost('skor') ?: []; // [soal_id => skor]
        $userId     = session()->get('user_id');

        $peserta = $this->pesertaModel->find($pesertaId);
        if (!$peserta) {
            return redirect()->back()->with('error', 'Peserta tidak ditemukan.');
        }

        $ujian = $this->ujianModel->find($peserta['ujian_id']);
        $db = \Config\Database::connect();
        $db->transStart();

        $totalSkorUraian = 0.0;
        foreach ($skorInputs as $soalId => $skorNilai) {
            $skorVal = (float)$skorNilai;
            $totalSkorUraian += $skorVal;

            $existing = $this->jawabanModel->where('peserta_id', $pesertaId)->where('soal_id', $soalId)->first();
            if ($existing) {
                $this->jawabanModel->update($existing['id'], [
                    'skor'         => $skorVal,
                    'dinilai_oleh' => $userId,
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->jawabanModel->insert([
                    'peserta_id'   => $pesertaId,
                    'soal_id'      => $soalId,
                    'jawaban'      => null,
                    'skor'         => $skorVal,
                    'dinilai_oleh' => $userId,
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Hitung ulang nilai akhir
        $soalList = $db->table('soal')->where('paket_id', $ujian['paket_id'])->where('deleted_at', null)->get()->getResultArray();
        $totalBobot = 0.0;
        foreach ($soalList as $s) {
            $totalBobot += (float)$s['bobot'];
        }

        $hasil = $this->hasilModel->where('peserta_id', $pesertaId)->first();
        $skorObjektif = (float)($hasil['skor_objektif'] ?? 0.0);
        $totalSkor = $skorObjektif + $totalSkorUraian;
        $nilaiAkhir = $totalBobot > 0 ? round(($totalSkor / $totalBobot) * 100, 2) : 0.00;

        $this->hasilModel->update($hasil['id'], [
            'skor_uraian'    => $totalSkorUraian,
            'nilai_akhir'    => $nilaiAkhir,
            'status_koreksi' => 'selesai',
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        LogAktivitasModel::catat('koreksi_uraian', "Koreksi peserta ID {$pesertaId}, Nilai Akhir: {$nilaiAkhir}");

        return redirect()->to(base_url('guru/koreksi/ujian/' . $ujian['id']))->with('success', 'Nilai uraian berhasil disimpan dan nilai akhir telah diperbarui.');
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UjianModel;
use App\Models\UjianPesertaModel;
use App\Models\HasilModel;
use App\Models\PengaturanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class Nilai extends BaseController
{
    protected UjianModel $ujianModel;
    protected UjianPesertaModel $pesertaModel;
    protected HasilModel $hasilModel;
    protected PengaturanModel $pengaturanModel;

    public function __construct()
    {
        $this->ujianModel      = new UjianModel();
        $this->pesertaModel    = new UjianPesertaModel();
        $this->hasilModel      = new HasilModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $ujianList = $this->ujianModel->getWithDetails();

        return view('admin/nilai/index', [
            'title'     => 'Rekapitulasi Nilai Seluruh Ujian',
            'ujianList' => $ujianList,
        ]);
    }

    public function ujian($ujianId = null)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian) {
            return redirect()->to(base_url('admin/nilai'))->with('error', 'Ujian tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $rekap = $db->query("
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

        $scores = array_filter(array_column($rekap, 'nilai_akhir'), fn($v) => $v !== null);
        $totalSiswa = count($scores);
        $tertinggi = !empty($scores) ? max($scores) : 0;
        $terendah  = !empty($scores) ? min($scores) : 0;
        $rataRata  = $totalSiswa > 0 ? round(array_sum($scores) / $totalSiswa, 1) : 0;

        $kkm = (float)$ujian['nilai_minimal'];
        $lulusCount = count(array_filter($scores, fn($s) => (float)$s >= $kkm));
        $persenLulus = $totalSiswa > 0 ? round(($lulusCount / $totalSiswa) * 100) : 0;

        // Analisis butir soal
        $analisisSoal = $db->query("
            SELECT s.id, s.urutan, s.tipe, s.bobot,
                   COUNT(j.id) as total_jawab,
                   SUM(CASE WHEN j.skor > 0 THEN 1 ELSE 0 END) as total_benar
            FROM soal s
            LEFT JOIN jawaban j ON j.soal_id = s.id AND j.peserta_id IN (
                SELECT id FROM ujian_peserta WHERE ujian_id = {$ujianId} AND status = 'selesai'
            )
            WHERE s.paket_id = {$ujian['paket_id']} AND s.deleted_at IS NULL
            GROUP BY s.id
            ORDER BY s.urutan ASC
        ")->getResultArray();

        foreach ($analisisSoal as &$as) {
            $tj = (int)$as['total_jawab'];
            $tb = (int)$as['total_benar'];
            $as['persen_benar'] = ($tj > 0) ? round(($tb / $tj) * 100) : 0;
        }

        return view('admin/nilai/detail', [
            'title'        => "Rekap Nilai — {$ujian['nama']}",
            'ujian'        => $ujian,
            'rekap'        => $rekap,
            'stats'        => [
                'total'     => $totalSiswa,
                'tertinggi' => $tertinggi,
                'terendah'  => $terendah,
                'rata'      => $rataRata,
                'lulus'     => $lulusCount,
                'persen'    => $persenLulus,
            ],
            'analisisSoal' => $analisisSoal,
        ]);
    }

    public function eksporExcel($ujianId = null)
    {
        $guruNilai = new \App\Controllers\Guru\Nilai();
        return $guruNilai->eksporExcel($ujianId);
    }

    public function eksporPdf($ujianId = null)
    {
        $guruNilai = new \App\Controllers\Guru\Nilai();
        return $guruNilai->eksporPdf($ujianId);
    }
}

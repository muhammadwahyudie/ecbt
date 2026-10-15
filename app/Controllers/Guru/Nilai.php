<?php

namespace App\Controllers\Guru;

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
        $userId = session()->get('user_id');
        $ujianList = $this->ujianModel->getWithDetails($userId, 'guru');

        return view('guru/nilai/index', [
            'title'     => 'Rekap & Nilai Ujian',
            'ujianList' => $ujianList,
        ]);
    }

    public function ujian($ujianId = null)
    {
        $userId = session()->get('user_id');
        $ujian  = $this->ujianModel->where('id', $ujianId)->where('pembuat_id', $userId)->first();
        if (!$ujian) {
            return redirect()->to(base_url('guru/nilai'))->with('error', 'Ujian tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        // Rekap nilai semua peserta
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

        // Hitung statistik (Tertinggi, Terendah, Rata-rata, Jumlah Lulus)
        $scores = array_filter(array_column($rekap, 'nilai_akhir'), fn($v) => $v !== null);
        $totalSiswa = count($scores);
        $tertinggi = !empty($scores) ? max($scores) : 0;
        $terendah  = !empty($scores) ? min($scores) : 0;
        $rataRata  = $totalSiswa > 0 ? round(array_sum($scores) / $totalSiswa, 1) : 0;

        $kkm = (float)$ujian['nilai_minimal'];
        $lulusCount = count(array_filter($scores, fn($s) => (float)$s >= $kkm));
        $persenLulus = $totalSiswa > 0 ? round(($lulusCount / $totalSiswa) * 100) : 0;

        // Analisis Butir Soal (Persentase benar per soal)
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

        return view('guru/nilai/detail', [
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

    /**
     * Ekspor Rekap Nilai ke Excel (.xlsx)
     */
    public function eksporExcel($ujianId = null)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $rekap = $db->query("
            SELECT up.*, u.nama, u.username, u.nis, k.nama as nama_kelas,
                   h.skor_objektif, h.skor_uraian, h.nilai_akhir
            FROM ujian_peserta up
            JOIN users u ON u.id = up.user_id
            LEFT JOIN siswa_kelas sk ON sk.user_id = u.id
            LEFT JOIN kelas k ON k.id = sk.kelas_id
            LEFT JOIN hasil h ON h.peserta_id = up.id
            WHERE up.ujian_id = {$ujianId} AND up.status = 'selesai'
            ORDER BY k.nama ASC, u.nama ASC
        ")->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai');

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI NILAI UJIAN');
        $sheet->setCellValue('A2', 'Nama Ujian: ' . $ujian['nama']);
        $sheet->setCellValue('A3', 'Tanggal Cetak: ' . date('d F Y, H:i'));

        // Table Header
        $headers = ['No', 'NIS', 'Nama Lengkap', 'Kelas / Rombel', 'Skor Objektif', 'Skor Uraian', 'Nilai Akhir', 'Keterangan'];
        $sheet->fromArray($headers, NULL, 'A5');

        $kkm = (float)$ujian['nilai_minimal'];
        $rowNum = 6;
        foreach ($rekap as $idx => $r) {
            $na = (float)($r['nilai_akhir'] ?? 0);
            $ket = ($na >= $kkm) ? 'Tuntas / Lulus' : 'Belum Tuntas';

            $dataRow = [
                $idx + 1,
                $r['nis'] ?: '-',
                $r['nama'],
                $r['nama_kelas'] ?? '-',
                $r['skor_objektif'] ?? '0.00',
                $r['skor_uraian'] ?? '0.00',
                $na,
                $ket
            ];
            $sheet->fromArray($dataRow, NULL, 'A' . $rowNum);
            $rowNum++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Rekap_Nilai_' . url_title($ujian['nama'], '_', true) . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Ekspor Rekap Nilai ke PDF
     */
    public function eksporPdf($ujianId = null)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian) {
            return redirect()->back()->with('error', 'Ujian tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $rekap = $db->query("
            SELECT up.*, u.nama, u.username, u.nis, k.nama as nama_kelas,
                   h.skor_objektif, h.skor_uraian, h.nilai_akhir
            FROM ujian_peserta up
            JOIN users u ON u.id = up.user_id
            LEFT JOIN siswa_kelas sk ON sk.user_id = u.id
            LEFT JOIN kelas k ON k.id = sk.kelas_id
            LEFT JOIN hasil h ON h.peserta_id = up.id
            WHERE up.ujian_id = {$ujianId} AND up.status = 'selesai'
            ORDER BY k.nama ASC, u.nama ASC
        ")->getResultArray();

        $settings = $this->pengaturanModel->getAllAsMap();

        $html = view('guru/nilai/pdf', [
            'ujian'    => $ujian,
            'rekap'    => $rekap,
            'settings' => $settings,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('Rekap_Nilai_' . url_title($ujian['nama'], '_', true) . '.pdf', [
            'Attachment' => true
        ]);
        exit;
    }
}

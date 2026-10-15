<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\PaketSoalModel;
use App\Models\SoalModel;
use App\Models\SoalOpsiModel;
use App\Models\LogAktivitasModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Soal extends BaseController
{
    protected PaketSoalModel $paketModel;
    protected SoalModel $soalModel;
    protected SoalOpsiModel $opsiModel;

    public function __construct()
    {
        $this->paketModel = new PaketSoalModel();
        $this->soalModel  = new SoalModel();
        $this->opsiModel  = new SoalOpsiModel();
    }

    private function getPaket($paketId): ?array
    {
        return $this->paketModel->select('paket_soal.*, m.nama as nama_mapel, m.kode as kode_mapel, u.nama as nama_pemilik')
            ->join('mapel m', 'm.id = paket_soal.mapel_id', 'left')
            ->join('users u', 'u.id = paket_soal.pemilik_id', 'left')
            ->where('paket_soal.id', $paketId)
            ->where('paket_soal.deleted_at', null)
            ->first();
    }

    /**
     * Halaman Penuh Tambah Soal
     */
    public function tambah($paketId = null)
    {
        $paket = $this->getPaket($paketId);
        if (!$paket) {
            return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Paket soal tidak ditemukan.');
        }

        // Hitung urutan berikutnya
        $maxUrutan = $this->soalModel->where('paket_id', $paketId)->selectMax('urutan')->first();
        $nextUrutan = ($maxUrutan['urutan'] ?? 0) + 1;

        return view('guru/soal/editor', [
            'title'      => 'Tambah Soal Baru',
            'paket'      => $paket,
            'soal'       => null,
            'opsi'       => [],
            'nextUrutan' => $nextUrutan,
        ]);
    }

    /**
     * Simpan Soal Baru
     */
    public function simpan($paketId = null)
    {
        $paket = $this->paketModel->find($paketId);
        if (!$paket) {
            return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Paket soal tidak ditemukan.');
        }

        $tipe       = $this->request->getPost('tipe');
        $pertanyaan = $this->request->getPost('pertanyaan');
        $bobot      = (float)$this->request->getPost('bobot') ?: 1.0;
        $pedoman    = $this->request->getPost('pedoman');
        $urutan     = (int)$this->request->getPost('urutan') ?: 1;

        if (empty(trim(strip_tags($pertanyaan)))) {
            return redirect()->back()->withInput()->with('error', 'Isi pertanyaan tidak boleh kosong.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $soalId = $this->soalModel->insert([
            'paket_id'   => $paketId,
            'tipe'       => $tipe,
            'pertanyaan' => $pertanyaan,
            'bobot'      => $bobot,
            'pedoman'    => $pedoman ?: null,
            'urutan'     => $urutan,
            'aktif'      => 1,
        ]);

        // Simpan Opsi berdasarkan tipe soal
        $this->simpanOpsiJawaban((int)$soalId, $tipe);

        $db->transComplete();

        LogAktivitasModel::catat('tambah_soal', "Soal ID {$soalId} pada Paket {$paket['nama']}");

        return redirect()->to(base_url("guru/paket-soal/{$paketId}"))->with('success', 'Butir soal berhasil disimpan.');
    }

    /**
     * Halaman Penuh Ubah Soal
     */
    public function edit($paketId = null, $soalId = null)
    {
        $paket = $this->getPaket($paketId);
        $soal  = $this->soalModel->getSoalWithOpsi((int)$soalId);

        if (!$paket || !$soal || $soal['paket_id'] != $paketId) {
            return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Soal tidak ditemukan.');
        }

        return view('guru/soal/editor', [
            'title' => 'Ubah Butir Soal',
            'paket' => $paket,
            'soal'  => $soal,
            'opsi'  => $soal['opsi'] ?? [],
        ]);
    }

    /**
     * Update Soal
     */
    public function update($paketId = null, $soalId = null)
    {
        $paket = $this->paketModel->find($paketId);
        $soal  = $this->soalModel->find($soalId);

        if (!$paket || !$soal) {
            return redirect()->to(base_url('guru/paket-soal'))->with('error', 'Soal tidak ditemukan.');
        }

        $tipe       = $this->request->getPost('tipe');
        $pertanyaan = $this->request->getPost('pertanyaan');
        $bobot      = (float)$this->request->getPost('bobot') ?: 1.0;
        $pedoman    = $this->request->getPost('pedoman');
        $urutan     = (int)$this->request->getPost('urutan') ?: 1;

        $db = \Config\Database::connect();
        $db->transStart();

        $this->soalModel->update($soalId, [
            'tipe'       => $tipe,
            'pertanyaan' => $pertanyaan,
            'bobot'      => $bobot,
            'pedoman'    => $pedoman ?: null,
            'urutan'     => $urutan,
        ]);

        // Hapus opsi lama dan simpan opsi baru
        $this->opsiModel->where('soal_id', $soalId)->delete();
        $this->simpanOpsiJawaban((int)$soalId, $tipe);

        $db->transComplete();

        LogAktivitasModel::catat('ubah_soal', "Soal ID {$soalId} pada Paket {$paket['nama']}");

        return redirect()->to(base_url("guru/paket-soal/{$paketId}"))->with('success', 'Butir soal berhasil diperbarui.');
    }

    /**
     * Helper simpan opsi jawaban dari input POST
     */
    private function simpanOpsiJawaban(int $soalId, string $tipe): void
    {
        if ($tipe === 'pilihan_ganda') {
            $opsiTeks = $this->request->getPost('pg_opsi') ?: [];
            $kunci    = $this->request->getPost('pg_kunci'); // label: A, B, C, ...

            $labels = ['A', 'B', 'C', 'D', 'E'];
            foreach ($opsiTeks as $idx => $teks) {
                if (trim($teks) === '') continue;
                $label = $labels[$idx] ?? chr(65 + $idx);
                $isBenar = ($kunci === $label) ? 1 : 0;
                $this->opsiModel->insert([
                    'soal_id' => $soalId,
                    'label'   => $label,
                    'isi'     => $teks,
                    'benar'   => $isBenar,
                    'urutan'  => $idx + 1,
                ]);
            }
        } elseif ($tipe === 'pilihan_ganda_kompleks') {
            $opsiTeks   = $this->request->getPost('kompleks_opsi') ?: [];
            $kunciArray = $this->request->getPost('kompleks_kunci') ?: []; // array of labels

            $labels = ['A', 'B', 'C', 'D', 'E'];
            foreach ($opsiTeks as $idx => $teks) {
                if (trim($teks) === '') continue;
                $label = $labels[$idx] ?? chr(65 + $idx);
                $isBenar = in_array($label, $kunciArray) ? 1 : 0;
                $this->opsiModel->insert([
                    'soal_id' => $soalId,
                    'label'   => $label,
                    'isi'     => $teks,
                    'benar'   => $isBenar,
                    'urutan'  => $idx + 1,
                ]);
            }
        } elseif ($tipe === 'benar_salah') {
            $pernyataanList = $this->request->getPost('bs_pernyataan') ?: [];
            $kunciBS        = $this->request->getPost('bs_kunci') ?: []; // array: 1 for benar, 0 for salah

            foreach ($pernyataanList as $idx => $teks) {
                if (trim($teks) === '') continue;
                $nilaiBenar = isset($kunciBS[$idx]) && $kunciBS[$idx] == '1' ? 1 : 0;
                $this->opsiModel->insert([
                    'soal_id' => $soalId,
                    'label'   => (string)($idx + 1),
                    'isi'     => $teks,
                    'benar'   => $nilaiBenar,
                    'urutan'  => $idx + 1,
                ]);
            }
        } elseif ($tipe === 'isian_singkat') {
            $kunciIsian = $this->request->getPost('isian_kunci') ?: [];
            foreach ($kunciIsian as $idx => $teks) {
                if (trim($teks) === '') continue;
                $this->opsiModel->insert([
                    'soal_id' => $soalId,
                    'label'   => 'KUNCI',
                    'isi'     => trim($teks),
                    'benar'   => 1,
                    'urutan'  => $idx + 1,
                ]);
            }
        }
    }

    /**
     * Hapus Soal (Soft delete)
     */
    public function hapus($soalId = null)
    {
        $soal = $this->soalModel->find($soalId);
        if (!$soal) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }

        $this->soalModel->delete($soalId);
        LogAktivitasModel::catat('hapus_soal', "Soal ID {$soalId}");

        return redirect()->back()->with('success', 'Butir soal berhasil dihapus.');
    }

    /**
     * Duplikat Soal
     */
    public function duplikat($soalId = null)
    {
        $soal = $this->soalModel->find($soalId);
        if (!$soal) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $maxUrutan = $this->soalModel->where('paket_id', $soal['paket_id'])->selectMax('urutan')->first();
        $nextUrutan = ($maxUrutan['urutan'] ?? 0) + 1;

        $newSoalId = $this->soalModel->insert([
            'paket_id'   => $soal['paket_id'],
            'tipe'       => $soal['tipe'],
            'pertanyaan' => $soal['pertanyaan'] . ' (Salinan)',
            'bobot'      => $soal['bobot'],
            'pedoman'    => $soal['pedoman'],
            'urutan'     => $nextUrutan,
            'aktif'      => 1,
        ]);

        $opsi = $this->opsiModel->where('soal_id', $soalId)->findAll();
        foreach ($opsi as $op) {
            $this->opsiModel->insert([
                'soal_id' => $newSoalId,
                'label'   => $op['label'],
                'isi'     => $op['isi'],
                'benar'   => $op['benar'],
                'urutan'  => $op['urutan'],
            ]);
        }

        $db->transComplete();

        LogAktivitasModel::catat('duplikat_soal', "Duplikat Soal {$soalId} ke {$newSoalId}");

        return redirect()->back()->with('success', 'Soal berhasil diduplikasi.');
    }

    /**
     * Pratinjau Tampilan Siswa
     */
    public function pratinjau($soalId = null)
    {
        $soal = $this->soalModel->getSoalWithOpsi((int)$soalId);
        if (!$soal) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }

        $paket = $this->getPaket($soal['paket_id']);

        return view('guru/soal/pratinjau', [
            'title' => 'Pratinjau Soal',
            'soal'  => $soal,
            'paket' => $paket,
        ]);
    }

    /**
     * Download Template Impor Excel Soal
     */
    public function template($paketId = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bank Soal');

        $headers = [
            'Tipe (pilihan_ganda/uraian/isian_singkat)',
            'Pertanyaan (Teks atau HTML)',
            'Bobot Nilai',
            'Opsi A',
            'Opsi B',
            'Opsi C',
            'Opsi D',
            'Opsi E',
            'Kunci Jawaban (A/B/C/D/E atau Kata Isian)',
            'Rubrik Pedoman Penilaian (Uraian)'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        $examples = [
            ['pilihan_ganda', 'Berapa hasil dari 5 + 7?', '1', '10', '11', '12', '13', '14', 'C', ''],
            ['isian_singkat', 'Ibukota negara Indonesia saat ini adalah...', '2', '', '', '', '', '', 'Jakarta', ''],
            ['uraian', 'Jelaskan perbedaan antara mitosis dan meiosis!', '5', '', '', '', '', '', '', 'Jawaban lengkap mencakup tahap dan sel anakan.'],
        ];
        $sheet->fromArray($examples, NULL, 'A2');

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Impor_Soal.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Impor Soal dari File Excel
     */
    public function impor($paketId = null)
    {
        $paket = $this->paketModel->find($paketId);
        if (!$paket) {
            return redirect()->back()->with('error', 'Paket soal tidak ditemukan.');
        }

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) <= 1) {
                return redirect()->back()->with('error', 'File Excel kosong.');
            }

            $sukses = 0;
            $maxUrutan = $this->soalModel->where('paket_id', $paketId)->selectMax('urutan')->first();
            $curUrutan = ($maxUrutan['urutan'] ?? 0);

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $tipe       = trim(strtolower($row[0] ?? ''));
                $pertanyaan = trim($row[1] ?? '');
                $bobot      = (float)($row[2] ?? 1.0) ?: 1.0;
                $kunci      = trim($row[8] ?? '');
                $pedoman    = trim($row[9] ?? '');

                if (empty($pertanyaan)) continue;
                if (!in_array($tipe, ['pilihan_ganda', 'uraian', 'isian_singkat'])) {
                    $tipe = 'pilihan_ganda';
                }

                $curUrutan++;
                $soalId = $this->soalModel->insert([
                    'paket_id'   => $paketId,
                    'tipe'       => $tipe,
                    'pertanyaan' => $pertanyaan,
                    'bobot'      => $bobot,
                    'pedoman'    => $pedoman ?: null,
                    'urutan'     => $curUrutan,
                    'aktif'      => 1,
                ]);

                if ($tipe === 'pilihan_ganda') {
                    $opsiHuruf = ['A', 'B', 'C', 'D', 'E'];
                    foreach ($opsiHuruf as $idx => $huruf) {
                        $colIdx = 3 + $idx; // Col D is index 3
                        $teksOpsi = trim($row[$colIdx] ?? '');
                        if (empty($teksOpsi)) continue;

                        $this->opsiModel->insert([
                            'soal_id' => $soalId,
                            'label'   => $huruf,
                            'isi'     => $teksOpsi,
                            'benar'   => (strtoupper($kunci) === $huruf) ? 1 : 0,
                            'urutan'  => $idx + 1,
                        ]);
                    }
                } elseif ($tipe === 'isian_singkat' && !empty($kunci)) {
                    $this->opsiModel->insert([
                        'soal_id' => $soalId,
                        'label'   => 'KUNCI',
                        'isi'     => $kunci,
                        'benar'   => 1,
                        'urutan'  => 1,
                    ]);
                }

                $sukses++;
            }

            LogAktivitasModel::catat('impor_soal', "Impor berhasil {$sukses} butir soal ke Paket {$paket['nama']}");

            return redirect()->back()->with('success', "Berhasil mengimpor {$sukses} butir soal ke paket ini.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
}

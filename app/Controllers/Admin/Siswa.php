<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KelasModel;
use App\Models\SiswaKelasModel;
use App\Models\TahunAjaranModel;
use App\Models\LogAktivitasModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Siswa extends BaseController
{
    protected UserModel $userModel;
    protected KelasModel $kelasModel;
    protected SiswaKelasModel $siswaKelasModel;
    protected TahunAjaranModel $taModel;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->kelasModel      = new KelasModel();
        $this->siswaKelasModel = new SiswaKelasModel();
        $this->taModel         = new TahunAjaranModel();
    }

    // ─── Index ────────────────────────────────────────────────────────────────
    public function index()
    {
        $ta        = $this->taModel->getAktif();
        $kelasList = $ta ? $this->kelasModel->where('tahun_ajaran_id', $ta['id'])->findAll() : [];

        return view('admin/siswa/index', [
            'title'     => 'Data Siswa',
            'pageTitle' => 'Data Siswa',
            'kelasList' => $kelasList,
            'taAktif'   => $ta,
        ]);
    }

    // ─── Data AJAX (DataTables server-side) ───────────────────────────────────
    public function data()
    {
        $search   = $this->request->getGet('search')['value'] ?? '';
        $start    = (int) ($this->request->getGet('start') ?? 0);
        $length   = (int) ($this->request->getGet('length') ?? 10);
        $kelasId  = $this->request->getGet('kelas_id');

        $ta = $this->taModel->getAktif();

        $db = \Config\Database::connect();
        $builder = $db->table('users u')
            ->select('u.id, u.nama, u.username, u.nis, u.nisn, u.email, u.aktif, k.nama AS nama_kelas, sk.kelas_id')
            ->join('siswa_kelas sk', "sk.user_id = u.id AND sk.tahun_ajaran_id = " . ($ta ? $ta['id'] : 0), 'left')
            ->join('kelas k', 'k.id = sk.kelas_id', 'left')
            ->where('u.role', 'siswa')
            ->where('u.deleted_at', null);

        if (!empty($kelasId)) {
            $builder->where('sk.kelas_id', $kelasId);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('u.nama', $search)
                ->orLike('u.username', $search)
                ->orLike('u.nis', $search)
                ->orLike('u.nisn', $search)
                ->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);
        $rows  = $builder->orderBy('u.nama', 'ASC')->limit($length, $start)->get()->getResultArray();

        return $this->response->setJSON([
            'draw'            => (int) ($this->request->getGet('draw') ?? 1),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $rows,
        ]);
    }

    // ─── Simpan (Tambah) ──────────────────────────────────────────────────────
    public function simpan()
    {
        $rules = [
            'nama'     => 'required|min_length[3]|max_length[150]',
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'nama'          => trim($this->request->getPost('nama')),
            'username'      => trim($this->request->getPost('username')),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'nis'           => trim($this->request->getPost('nis')) ?: null,
            'nisn'          => trim($this->request->getPost('nisn')) ?: null,
            'email'         => trim($this->request->getPost('email')) ?: null,
            'role'          => 'siswa',
            'aktif'         => $this->request->getPost('aktif') !== null ? 1 : 0,
        ];

        $userId  = $this->userModel->insert($data);
        $kelasId = $this->request->getPost('kelas_id');
        if ($kelasId) {
            $ta = $this->taModel->getAktif();
            if ($ta) {
                $this->siswaKelasModel->assignSiswa((int) $userId, (int) $kelasId, (int) $ta['id']);
            }
        }

        LogAktivitasModel::catat('tambah_siswa', "Siswa: {$data['username']}", ['user_id' => $userId]);

        return $this->response->setJSON(['success' => true, 'message' => 'Siswa berhasil ditambahkan.']);
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update($id = null)
    {
        $siswa = $this->userModel->where('id', $id)->where('role', 'siswa')->first();
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
        }

        $rules = [
            'nama'     => 'required|min_length[3]|max_length[150]',
            'username' => "required|min_length[3]|max_length[100]|is_unique[users.username,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'nama'     => trim($this->request->getPost('nama')),
            'username' => trim($this->request->getPost('username')),
            'nis'      => trim($this->request->getPost('nis')) ?: null,
            'nisn'     => trim($this->request->getPost('nisn')) ?: null,
            'email'    => trim($this->request->getPost('email')) ?: null,
            'aktif'    => $this->request->getPost('aktif') !== null ? 1 : 0,
        ];

        $this->userModel->update($id, $data);

        $kelasId = $this->request->getPost('kelas_id');
        if ($kelasId) {
            $ta = $this->taModel->getAktif();
            if ($ta) {
                $this->siswaKelasModel->assignSiswa((int) $id, (int) $kelasId, (int) $ta['id']);
            }
        }

        LogAktivitasModel::catat('ubah_siswa', "Siswa: {$data['username']}", ['user_id' => $id]);

        return $this->response->setJSON(['success' => true, 'message' => 'Data siswa berhasil diperbarui.']);
    }

    // ─── Hapus (Soft Delete) ──────────────────────────────────────────────────
    public function hapus($id = null)
    {
        $siswa = $this->userModel->where('id', $id)->where('role', 'siswa')->first();
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
        }

        $this->userModel->delete($id);
        LogAktivitasModel::catat('hapus_siswa', "Siswa ID: {$id} ({$siswa['username']})");

        return $this->response->setJSON(['success' => true, 'message' => 'Siswa berhasil dihapus dari sistem.']);
    }

    // ─── Reset Password ───────────────────────────────────────────────────────
    public function resetPassword($id = null)
    {
        $siswa = $this->userModel->where('id', $id)->where('role', 'siswa')->first();
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
        }

        $newPassword = $this->request->getPost('new_password');
        if (empty($newPassword) || strlen($newPassword) < 6) {
            $newPassword = 'siswa123';
        }

        $this->userModel->update($id, [
            'password_hash'  => password_hash($newPassword, PASSWORD_BCRYPT),
            'login_gagal'    => 0,
            'dikunci_sampai' => null,
            'session_token'  => null,
        ]);

        LogAktivitasModel::catat('reset_password_siswa', "Siswa: {$siswa['username']}");

        return $this->response->setJSON([
            'success'  => true,
            'message'  => "Password untuk {$siswa['nama']} berhasil direset.",
            'password' => $newPassword,
        ]);
    }

    // ─── Download Template Excel ──────────────────────────────────────────────
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        $headers = ['Nama Lengkap', 'Username', 'NIS', 'NISN', 'Password', 'Nama Kelas'];
        $sheet->fromArray($headers, NULL, 'A1');

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0E7C86']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        $examples = [
            ['Ahmad Fauzi', 'ahmad123', '1001', '0051234567', 'siswa123', 'X RPL 1'],
            ['Siti Aisyah', 'siti123', '1002', '0051234568', 'siswa123', 'X RPL 1'],
        ];
        $sheet->fromArray($examples, NULL, 'A2');

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'Template_Impor_Siswa.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // ─── Impor dari Excel ─────────────────────────────────────────────────────
    public function impor()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid atau tidak ditemukan.']);
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $rows        = $spreadsheet->getActiveSheet()->toArray();

            if (count($rows) <= 1) {
                return $this->response->setJSON(['success' => false, 'message' => 'File Excel kosong atau tidak memiliki data.']);
            }

            $ta     = $this->taModel->getAktif();
            $sukses = 0;
            $gagal  = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row       = $rows[$i];
                $nama      = trim($row[0] ?? '');
                $username  = trim($row[1] ?? '');
                $nis       = trim($row[2] ?? '');
                $nisn      = trim($row[3] ?? '');
                $password  = trim($row[4] ?? '') ?: 'siswa123';
                $namaKelas = trim($row[5] ?? '');

                if (empty($nama) || empty($username)) {
                    continue;
                }

                if ($this->userModel->where('username', $username)->first()) {
                    $gagal[] = "Baris " . ($i + 1) . ": Username '{$username}' sudah digunakan.";
                    continue;
                }

                $userData = [
                    'nama'          => $nama,
                    'username'      => $username,
                    'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                    'nis'           => $nis ?: null,
                    'nisn'          => $nisn ?: null,
                    'role'          => 'siswa',
                    'aktif'         => 1,
                ];

                $userId = $this->userModel->insert($userData);
                if ($userId) {
                    $sukses++;
                    if (!empty($namaKelas) && $ta) {
                        $kelas = $this->kelasModel->where('nama', $namaKelas)->where('tahun_ajaran_id', $ta['id'])->first();
                        if ($kelas) {
                            $this->siswaKelasModel->assignSiswa((int) $userId, (int) $kelas['id'], (int) $ta['id']);
                        }
                    }
                }
            }

            LogAktivitasModel::catat('impor_siswa', "Impor berhasil {$sukses} siswa.");

            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil mengimpor {$sukses} data siswa.",
                'gagal'   => $gagal,
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses file: ' . $e->getMessage()]);
        }
    }
}

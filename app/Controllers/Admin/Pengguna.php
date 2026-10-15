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

class Pengguna extends BaseController
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

    public function index()
    {
        $ta = $this->taModel->getAktif();
        $kelasList = $ta ? $this->kelasModel->where('tahun_ajaran_id', $ta['id'])->findAll() : [];

        return view('admin/pengguna/index', [
            'title'     => 'Manajemen Pengguna',
            'kelasList' => $kelasList,
            'taAktif'   => $ta,
        ]);
    }

    /**
     * Data AJAX untuk DataTables
     */
    public function data()
    {
        $role = $this->request->getGet('role');
        $builder = $this->userModel->select('users.*, k.nama as nama_kelas')
            ->join('siswa_kelas sk', 'sk.user_id = users.id', 'left')
            ->join('kelas k', 'k.id = sk.kelas_id', 'left')
            ->where('users.deleted_at', null);

        if (!empty($role) && in_array($role, ['admin', 'proktor', 'guru', 'siswa'])) {
            $builder->where('users.role', $role);
        }

        $users = $builder->orderBy('users.id', 'DESC')->findAll();

        return $this->response->setJSON(['data' => $users]);
    }

    /**
     * Tambah Pengguna Baru
     */
    public function simpan()
    {
        $rules = [
            'nama'     => 'required|min_length[3]|max_length[150]',
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,proktor,guru,siswa]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $role = $this->request->getPost('role');
        $data = [
            'nama'          => trim($this->request->getPost('nama')),
            'username'      => trim($this->request->getPost('username')),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'          => $role,
            'nis'           => $role === 'siswa' ? trim($this->request->getPost('nis')) : null,
            'nisn'          => $role === 'siswa' ? trim($this->request->getPost('nisn')) : null,
            'email'         => trim($this->request->getPost('email')) ?: null,
            'aktif'         => $this->request->getPost('aktif') !== null ? 1 : 0,
        ];

        $userId = $this->userModel->insert($data);

        // Jika siswa dan ada pilihan kelas
        $kelasId = $this->request->getPost('kelas_id');
        if ($role === 'siswa' && $kelasId) {
            $ta = $this->taModel->getAktif();
            if ($ta) {
                $this->siswaKelasModel->assignSiswa((int)$userId, (int)$kelasId, (int)$ta['id']);
            }
        }

        LogAktivitasModel::catat('tambah_pengguna', "User: {$data['username']} ({$data['role']})", ['user_id' => $userId]);

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Update Pengguna
     */
    public function update($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'nama'     => 'required|min_length[3]|max_length[150]',
            'username' => "required|min_length[3]|max_length[100]|is_unique[users.username,id,{$id}]",
            'role'     => 'required|in_list[admin,proktor,guru,siswa]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $role = $this->request->getPost('role');
        $data = [
            'nama'     => trim($this->request->getPost('nama')),
            'username' => trim($this->request->getPost('username')),
            'role'     => $role,
            'nis'      => $role === 'siswa' ? trim($this->request->getPost('nis')) : null,
            'nisn'     => $role === 'siswa' ? trim($this->request->getPost('nisn')) : null,
            'email'    => trim($this->request->getPost('email')) ?: null,
            'aktif'    => $this->request->getPost('aktif') !== null ? 1 : 0,
        ];

        $this->userModel->update($id, $data);

        $kelasId = $this->request->getPost('kelas_id');
        if ($role === 'siswa' && $kelasId) {
            $ta = $this->taModel->getAktif();
            if ($ta) {
                $this->siswaKelasModel->assignSiswa((int)$id, (int)$kelasId, (int)$ta['id']);
            }
        }

        LogAktivitasModel::catat('ubah_pengguna', "User: {$data['username']}", ['user_id' => $id]);

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Soft Delete Pengguna
     */
    public function hapus($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($user['id'] == session()->get('user_id')) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $this->userModel->delete($id);
        LogAktivitasModel::catat('hapus_pengguna', "User ID: {$id} ({$user['username']})");

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Reset Password Pengguna
     */
    public function resetPassword($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $newPassword = $this->request->getPost('new_password');
        if (empty($newPassword) || strlen($newPassword) < 6) {
            $newPassword = 'password123'; // Password default jika tidak diisi
        }

        $this->userModel->update($id, [
            'password_hash'  => password_hash($newPassword, PASSWORD_BCRYPT),
            'login_gagal'    => 0,
            'dikunci_sampai' => null,
            'session_token'  => null,
        ]);

        LogAktivitasModel::catat('reset_password', "User: {$user['username']}");

        return redirect()->back()->with('success', "Password untuk {$user['nama']} berhasil direset menjadi: {$newPassword}");
    }

    /**
     * Download Template Excel Siswa
     */
    public function templateSiswa()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        $headers = ['Nama Lengkap', 'Username', 'NIS', 'NISN', 'Password', 'Nama Kelas'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Contoh baris
        $example = [
            ['Ahmad Fauzi', 'ahmad123', '1001', '0051234567', 'siswa123', 'X RPL 1'],
            ['Siti Aisyah', 'siti123', '1002', '0051234568', 'siswa123', 'X RPL 1'],
        ];
        $sheet->fromArray($example, NULL, 'A2');

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Impor_Siswa.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Download Template Excel Guru
     */
    public function templateGuru()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Guru');

        $headers = ['Nama Lengkap', 'Username', 'Email', 'Password'];
        $sheet->fromArray($headers, NULL, 'A1');

        $example = [
            ['Budi Santoso, S.Pd', 'budi_guru', 'budi@sekolah.sch.id', 'guru123'],
            ['Dewi Sartika, M.Pd', 'dewi_guru', 'dewi@sekolah.sch.id', 'guru123'],
        ];
        $sheet->fromArray($example, NULL, 'A2');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Impor_Guru.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Impor Siswa dari Excel
     */
    public function imporSiswa()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) <= 1) {
                return redirect()->back()->with('error', 'File Excel kosong atau tidak memiliki data.');
            }

            $ta = $this->taModel->getAktif();
            $sukses = 0;
            $gagal  = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $nama      = trim($row[0] ?? '');
                $username  = trim($row[1] ?? '');
                $nis       = trim($row[2] ?? '');
                $nisn      = trim($row[3] ?? '');
                $password  = trim($row[4] ?? '') ?: 'siswa123';
                $namaKelas = trim($row[5] ?? '');

                if (empty($nama) || empty($username)) {
                    continue; // Skip baris kosong
                }

                // Cek username unik
                $existing = $this->userModel->where('username', $username)->first();
                if ($existing) {
                    $gagal[] = "Baris " . ($i + 1) . ": Username '{$username}' sudah digunakan.";
                    continue;
                }

                $userData = [
                    'nama'          => $nama,
                    'username'      => $username,
                    'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                    'role'          => 'siswa',
                    'nis'           => $nis ?: null,
                    'nisn'          => $nisn ?: null,
                    'aktif'         => 1,
                ];

                $userId = $this->userModel->insert($userData);
                if ($userId) {
                    $sukses++;
                    // Hubungkan ke kelas jika kelas ditemukan
                    if (!empty($namaKelas) && $ta) {
                        $kelas = $this->kelasModel->where('nama', $namaKelas)
                            ->where('tahun_ajaran_id', $ta['id'])
                            ->first();
                        if ($kelas) {
                            $this->siswaKelasModel->assignSiswa((int)$userId, (int)$kelas['id'], (int)$ta['id']);
                        }
                    }
                }
            }

            LogAktivitasModel::catat('impor_siswa', "Impor berhasil {$sukses} siswa.");

            $pesan = "Berhasil mengimpor {$sukses} data siswa.";
            if (!empty($gagal)) {
                $pesan .= "<br><br><strong>Catatan Gagal:</strong><br>" . implode("<br>", array_slice($gagal, 0, 5));
                if (count($gagal) > 5) {
                    $pesan .= "<br>... dan " . (count($gagal) - 5) . " baris lainnya.";
                }
            }

            return redirect()->back()->with('success', $pesan);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Impor Guru dari Excel
     */
    public function imporGuru()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) <= 1) {
                return redirect()->back()->with('error', 'File Excel kosong atau tidak memiliki data.');
            }

            $sukses = 0;
            $gagal  = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $nama     = trim($row[0] ?? '');
                $username = trim($row[1] ?? '');
                $email    = trim($row[2] ?? '');
                $password = trim($row[3] ?? '') ?: 'guru123';

                if (empty($nama) || empty($username)) {
                    continue;
                }

                $existing = $this->userModel->where('username', $username)->first();
                if ($existing) {
                    $gagal[] = "Baris " . ($i + 1) . ": Username '{$username}' sudah digunakan.";
                    continue;
                }

                $userData = [
                    'nama'          => $nama,
                    'username'      => $username,
                    'email'         => $email ?: null,
                    'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                    'role'          => 'guru',
                    'aktif'         => 1,
                ];

                $userId = $this->userModel->insert($userData);
                if ($userId) {
                    $sukses++;
                }
            }

            LogAktivitasModel::catat('impor_guru', "Impor berhasil {$sukses} guru.");

            $pesan = "Berhasil mengimpor {$sukses} data guru.";
            if (!empty($gagal)) {
                $pesan .= "<br><br><strong>Catatan Gagal:</strong><br>" . implode("<br>", array_slice($gagal, 0, 5));
            }

            return redirect()->back()->with('success', $pesan);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
}

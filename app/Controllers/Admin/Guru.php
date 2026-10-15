<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LogAktivitasModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Guru extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ─── Index ────────────────────────────────────────────────────────────────
    public function index()
    {
        return view('admin/guru/index', [
            'title'     => 'Data Guru',
            'pageTitle' => 'Data Guru',
        ]);
    }

    // ─── Data AJAX (DataTables server-side) ───────────────────────────────────
    public function data()
    {
        $search = $this->request->getGet('search')['value'] ?? '';
        $start  = (int) ($this->request->getGet('start') ?? 0);
        $length = (int) ($this->request->getGet('length') ?? 10);

        $builder = $this->userModel
            ->where('role', 'guru')
            ->where('deleted_at', null);

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama', $search)
                ->orLike('username', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);
        $rows  = $builder->orderBy('nama', 'ASC')->limit($length, $start)->findAll();

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
            'email'         => trim($this->request->getPost('email')) ?: null,
            'role'          => 'guru',
            'aktif'         => $this->request->getPost('aktif') !== null ? 1 : 0,
        ];

        $userId = $this->userModel->insert($data);
        LogAktivitasModel::catat('tambah_guru', "Guru: {$data['username']}", ['user_id' => $userId]);

        return $this->response->setJSON(['success' => true, 'message' => 'Guru berhasil ditambahkan.']);
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update($id = null)
    {
        $guru = $this->userModel->where('id', $id)->where('role', 'guru')->first();
        if (!$guru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data guru tidak ditemukan.']);
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
            'email'    => trim($this->request->getPost('email')) ?: null,
            'aktif'    => $this->request->getPost('aktif') !== null ? 1 : 0,
        ];

        $this->userModel->update($id, $data);
        LogAktivitasModel::catat('ubah_guru', "Guru: {$data['username']}", ['user_id' => $id]);

        return $this->response->setJSON(['success' => true, 'message' => 'Data guru berhasil diperbarui.']);
    }

    // ─── Hapus (Soft Delete) ──────────────────────────────────────────────────
    public function hapus($id = null)
    {
        $guru = $this->userModel->where('id', $id)->where('role', 'guru')->first();
        if (!$guru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data guru tidak ditemukan.']);
        }

        if ($guru['id'] == session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        $this->userModel->delete($id);
        LogAktivitasModel::catat('hapus_guru', "Guru ID: {$id} ({$guru['username']})");

        return $this->response->setJSON(['success' => true, 'message' => 'Guru berhasil dihapus dari sistem.']);
    }

    // ─── Reset Password ───────────────────────────────────────────────────────
    public function resetPassword($id = null)
    {
        $guru = $this->userModel->where('id', $id)->where('role', 'guru')->first();
        if (!$guru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data guru tidak ditemukan.']);
        }

        $newPassword = $this->request->getPost('new_password');
        if (empty($newPassword) || strlen($newPassword) < 6) {
            $newPassword = 'guru123';
        }

        $this->userModel->update($id, [
            'password_hash'  => password_hash($newPassword, PASSWORD_BCRYPT),
            'login_gagal'    => 0,
            'dikunci_sampai' => null,
            'session_token'  => null,
        ]);

        LogAktivitasModel::catat('reset_password_guru', "Guru: {$guru['username']}");

        return $this->response->setJSON([
            'success'  => true,
            'message'  => "Password untuk {$guru['nama']} berhasil direset.",
            'password' => $newPassword,
        ]);
    }

    // ─── Download Template Excel ──────────────────────────────────────────────
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Guru');

        $headers = ['Nama Lengkap', 'Username', 'Email', 'Password'];
        $sheet->fromArray($headers, NULL, 'A1');

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0E7C86']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        $examples = [
            ['Budi Santoso, S.Pd', 'budi_guru', 'budi@sekolah.sch.id', 'guru123'],
            ['Dewi Sartika, M.Pd', 'dewi_guru', 'dewi@sekolah.sch.id', 'guru123'],
        ];
        $sheet->fromArray($examples, NULL, 'A2');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'Template_Impor_Guru.xlsx';

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

            $sukses = 0;
            $gagal  = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row      = $rows[$i];
                $nama     = trim($row[0] ?? '');
                $username = trim($row[1] ?? '');
                $email    = trim($row[2] ?? '');
                $password = trim($row[3] ?? '') ?: 'guru123';

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
                    'email'         => $email ?: null,
                    'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                    'role'          => 'guru',
                    'aktif'         => 1,
                ];

                if ($this->userModel->insert($userData)) {
                    $sukses++;
                }
            }

            LogAktivitasModel::catat('impor_guru', "Impor berhasil {$sukses} guru.");

            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil mengimpor {$sukses} data guru.",
                'gagal'   => $gagal,
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses file: ' . $e->getMessage()]);
        }
    }
}

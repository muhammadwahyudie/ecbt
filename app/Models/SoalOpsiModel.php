<?php

namespace App\Models;

use CodeIgniter\Model;

class SoalOpsiModel extends Model
{
    protected $table         = 'soal_opsi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'soal_id', 'label', 'isi', 'benar', 'urutan'
    ];

    protected $validationRules = [
        'soal_id' => 'required|integer',
        'label'   => 'required|max_length[5]',
        'isi'     => 'required',
        'benar'   => 'in_list[0,1]',
    ];
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class MapelModel extends Model
{
    protected $table         = 'mapel';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'kode', 'nama'
    ];

    protected $validationRules = [
        'kode' => 'required|min_length[1]|max_length[20]',
        'nama' => 'required|min_length[2]|max_length[100]',
    ];
}

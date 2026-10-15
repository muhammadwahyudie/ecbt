<?php

namespace App\Models;

use CodeIgniter\Model;

class RuangModel extends Model
{
    protected $table         = 'ruang';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'nama', 'kapasitas'
    ];

    protected $validationRules = [
        'nama'      => 'required|min_length[2]|max_length[100]',
        'kapasitas' => 'required|integer|greater_than[0]',
    ];
}

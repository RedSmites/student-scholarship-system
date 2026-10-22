<?php

namespace App\Models;

use CodeIgniter\Model;

class ScholarshipModel extends Model
{
    protected $table            = 'scholarships';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'title',
        'provider',
        'description',
        'eligibility',
        'benefits',
        'deadline',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

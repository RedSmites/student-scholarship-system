<?php

namespace App\Models;

use CodeIgniter\Model;

class ScholarshipRequirementModel extends Model
{
    protected $table            = 'scholarship_requirements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'scholarship_id',
        'requirement_name',
        'description',
        'is_required',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

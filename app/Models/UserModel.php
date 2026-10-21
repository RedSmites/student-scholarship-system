<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'password_hash',
        'role',
    ];

    protected $useTimestamps = false;
}

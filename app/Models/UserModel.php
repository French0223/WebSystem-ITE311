<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'name',
        'email',
        'hashed_password',
        'role',
    ];

    protected $useTimestamps = false; // We removed timestamps per lab spec
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Basic validation rules (controller already validates, but keep for safety)
    protected $validationRules    = [
        'name'            => 'required|min_length[2]|max_length[100]',
        'email'           => 'required|valid_email',
        'hashed_password' => 'required|min_length[6]',
        'role'            => 'required|in_list[student,instructor,admin]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
}


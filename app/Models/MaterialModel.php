<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table            = 'materials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['course_id', 'file_name', 'file_path', 'created_at'];
    protected $useTimestamps    = false;

    public function insertMaterial(array $data): bool
    {
        return (bool) $this->insert($data, false);
    }

    public function getMaterialsByCourse(int $course_id): array
    {
        return $this->where('course_id', $course_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['title', 'description', 'instructor_id', 'category', 'level', 'duration', 'price', 'status', 'created_at', 'updated_at'];

    public function getActiveCourses(): array
    {
        return $this->where('status', 'active')->orderBy('title', 'ASC')->findAll();
    }

    public function findActive(int $id): ?array
    {
        return $this->where('status', 'active')->find($id);
    }
}
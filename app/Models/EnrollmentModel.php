<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table            = 'enrollments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'course_id', 'enrollment_date'];
    protected $useTimestamps    = false;

    public function enrollUser(array $data): bool
    {
        // expects user_id, course_id, enrollment_date
        return (bool) $this->insert($data, false);
    }

    public function isAlreadyEnrolled(int $userId, int $courseId): bool
    {
        return $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->countAllResults() > 0;
    }

    public function getUserEnrollments(int $userId): array
    {
        // Join with courses to get course details
        return $this->select('courses.id, courses.title, courses.description, enrollments.enrollment_date')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->where('enrollments.user_id', $userId)
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->findAll();
    }
}
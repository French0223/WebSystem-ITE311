<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'student_id' => 4, // student1
                'course_id' => 1, // Introduction to Web Development
                'enrollment_date' => '2024-01-15 10:00:00',
                'completion_date' => null,
                'progress' => 75.00,
                'status' => 'in_progress',
                'grade' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 4, // student1
                'course_id' => 2, // Advanced PHP Programming
                'enrollment_date' => '2024-02-01 14:30:00',
                'completion_date' => null,
                'progress' => 25.00,
                'status' => 'in_progress',
                'grade' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 5, // student2
                'course_id' => 1, // Introduction to Web Development
                'enrollment_date' => '2024-01-20 09:15:00',
                'completion_date' => '2024-03-15 16:45:00',
                'progress' => 100.00,
                'status' => 'completed',
                'grade' => 'A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 5, // student2
                'course_id' => 3, // Database Design Fundamentals
                'enrollment_date' => '2024-02-10 11:20:00',
                'completion_date' => null,
                'progress' => 50.00,
                'status' => 'in_progress',
                'grade' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 6, // student3
                'course_id' => 1, // Introduction to Web Development
                'enrollment_date' => '2024-01-25 13:45:00',
                'completion_date' => null,
                'progress' => 0.00,
                'status' => 'enrolled',
                'grade' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 6, // student3
                'course_id' => 4, // Mobile App Development with React Native
                'enrollment_date' => '2024-02-05 15:30:00',
                'completion_date' => null,
                'progress' => 10.00,
                'status' => 'in_progress',
                'grade' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('enrollments')->insertBatch($data);
    }
}

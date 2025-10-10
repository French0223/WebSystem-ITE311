<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        // Try to find an instructor; fall back to any existing user ID
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $instructor = $builder->where('role', 'instructor')->get(1)->getRowArray();
        $anyUser    = $builder->get(1)->getRowArray();

        $instructorId = $instructor['id'] ?? ($anyUser['id'] ?? 1); // safe fallback

        $now = date('Y-m-d H:i:s');

        $courses = [
            [
                'title'         => 'Introduction to Programming',
                'description'   => 'Basics of programming with problem solving.',
                'instructor_id' => $instructorId,
                'category'      => 'Computer Science',
                'level'         => 'beginner',
                'duration'      => 40,
                'price'         => 0.00,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Web Development Fundamentals',
                'description'   => 'HTML, CSS, and JavaScript essentials.',
                'instructor_id' => $instructorId,
                'category'      => 'Web',
                'level'         => 'beginner',
                'duration'      => 30,
                'price'         => 0.00,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Database Systems',
                'description'   => 'Relational databases and SQL basics.',
                'instructor_id' => $instructorId,
                'category'      => 'Database',
                'level'         => 'intermediate',
                'duration'      => 35,
                'price'         => 0.00,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Data Structures',
                'description'   => 'Arrays, lists, stacks, queues, trees, graphs.',
                'instructor_id' => $instructorId,
                'category'      => 'Computer Science',
                'level'         => 'intermediate',
                'duration'      => 45,
                'price'         => 0.00,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Software Engineering',
                'description'   => 'SDLC, requirements, testing, deployment.',
                'instructor_id' => $instructorId,
                'category'      => 'Software',
                'level'         => 'advanced',
                'duration'      => 50,
                'price'         => 0.00,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Networking Basics',
                'description'   => 'OSI model, TCP/IP, routing, switching.',
                'instructor_id' => $instructorId,
                'category'      => 'Networking',
                'level'         => 'beginner',
                'duration'      => 25,
                'price'         => 0.00,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('courses')->insertBatch($courses);
    }
}
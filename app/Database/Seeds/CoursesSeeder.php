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
                'course_code'   => 'CS101',
                'term'          => 'Academic Year 2025-2026',
                'semester'      => '1st Semester',
                'description'   => 'Basics of programming with problem solving.',
                'start_date'    => '2025-08-05',
                'end_date'      => '2025-12-15',
                'instructor_id' => $instructorId,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Web Development Fundamentals',
                'course_code'   => 'WEB110',
                'term'          => 'Academic Year 2025-2026',
                'semester'      => '1st Semester',
                'description'   => 'HTML, CSS, and JavaScript essentials.',
                'start_date'    => '2025-08-05',
                'end_date'      => '2025-12-15',
                'instructor_id' => $instructorId,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Database Systems',
                'course_code'   => 'DB201',
                'term'          => 'Academic Year 2025-2026',
                'semester'      => '2nd Semester',
                'description'   => 'Relational databases and SQL basics.',
                'start_date'    => '2026-01-10',
                'end_date'      => '2026-05-20',
                'instructor_id' => $instructorId,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Data Structures',
                'course_code'   => 'CS210',
                'term'          => 'Academic Year 2025-2026',
                'semester'      => '2nd Semester',
                'description'   => 'Arrays, lists, stacks, queues, trees, graphs.',
                'start_date'    => '2026-01-10',
                'end_date'      => '2026-05-20',
                'instructor_id' => $instructorId,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Software Engineering',
                'course_code'   => 'SE300',
                'term'          => 'Academic Year 2025-2026',
                'semester'      => 'Short Term',
                'description'   => 'SDLC, requirements, testing, deployment.',
                'start_date'    => '2026-06-01',
                'end_date'      => '2026-07-31',
                'instructor_id' => $instructorId,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'title'         => 'Networking Basics',
                'course_code'   => 'NET120',
                'term'          => 'Academic Year 2025-2026',
                'semester'      => 'Short Term',
                'description'   => 'OSI model, TCP/IP, routing, switching.',
                'start_date'    => '2026-06-01',
                'end_date'      => '2026-07-31',
                'instructor_id' => $instructorId,
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('courses')->insertBatch($courses);
    }
}
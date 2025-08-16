<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the basics of HTML, CSS, and JavaScript to create modern websites.',
                'instructor_id' => 2, // instructor1
                'category' => 'Web Development',
                'level' => 'beginner',
                'duration' => 20,
                'price' => 49.99,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Advanced PHP Programming',
                'description' => 'Master PHP programming with advanced concepts, frameworks, and best practices.',
                'instructor_id' => 2, // instructor1
                'category' => 'Programming',
                'level' => 'advanced',
                'duration' => 30,
                'price' => 79.99,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Database Design Fundamentals',
                'description' => 'Learn database design principles, normalization, and SQL optimization.',
                'instructor_id' => 3, // instructor2
                'category' => 'Database',
                'level' => 'intermediate',
                'duration' => 25,
                'price' => 59.99,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Mobile App Development with React Native',
                'description' => 'Build cross-platform mobile applications using React Native framework.',
                'instructor_id' => 3, // instructor2
                'category' => 'Mobile Development',
                'level' => 'intermediate',
                'duration' => 35,
                'price' => 89.99,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}

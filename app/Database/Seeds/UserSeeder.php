<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@lms.com',
                'hashed_password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
            ],
            [
                'name' => 'John Smith',
                'email' => 'instructor1@lms.com',
                'hashed_password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'role' => 'teacher',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'instructor2@lms.com',
                'hashed_password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'role' => 'teacher',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'student1@lms.com',
                'hashed_password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student',
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
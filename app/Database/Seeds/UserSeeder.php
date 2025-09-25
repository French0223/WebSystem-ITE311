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
                'name' => 'Jim jamero',
                'email' => 'teacher@lms.com',
                'hashed_password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role' => 'teacher',
            ],
            [
                'name' => 'Precious Autida',
                'email' => 'teacher1@lms.com',
                'hashed_password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'role' => 'teacher',
            ],
            [
                'name' => 'Frenchie RM M. Labasa',
                'email' => 'student@lms.com',
                'hashed_password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student',
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
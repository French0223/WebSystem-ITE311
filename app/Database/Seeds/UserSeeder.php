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
                'role' => 'instructor',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'instructor2@lms.com',
                'hashed_password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'role' => 'instructor',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'student1@lms.com',
                'hashed_password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'student2@lms.com',
                'hashed_password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
            [
                'name' => 'David Wilson',
                'email' => 'student3@lms.com',
                'hashed_password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
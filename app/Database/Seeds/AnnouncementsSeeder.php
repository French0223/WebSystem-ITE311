<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementsSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'title'      => 'Welcome to the Student Portal',
                'content'    => 'Explore features and stay updated.',
                'created_at' => $now,
            ],
            [
                'title'      => 'Midterm Schedule Posted',
                'content'    => 'Check the Academics section for your schedule.',
                'created_at' => $now,
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
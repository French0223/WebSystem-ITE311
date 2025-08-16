<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Quiz for HTML Basics lesson (lesson_id = 1)
            [
                'lesson_id' => 1,
                'title' => 'HTML Elements Quiz',
                'description' => 'Test your knowledge of HTML elements and tags',
                'question' => 'What does HTML stand for?',
                'option_a' => 'HyperText Markup Language',
                'option_b' => 'High Tech Modern Language',
                'option_c' => 'Home Tool Markup Language',
                'option_d' => 'Hyperlink and Text Markup Language',
                'correct_answer' => 'A',
                'points' => 5,
                'time_limit' => 30,
                'order_number' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 1,
                'title' => 'HTML Tags Quiz',
                'description' => 'Test your understanding of HTML tags',
                'question' => 'Which HTML tag is used to define a paragraph?',
                'option_a' => '<paragraph>',
                'option_b' => '<p>',
                'option_c' => '<text>',
                'option_d' => '<para>',
                'correct_answer' => 'B',
                'points' => 5,
                'time_limit' => 30,
                'order_number' => 2,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Quiz for CSS Styling lesson (lesson_id = 2)
            [
                'lesson_id' => 2,
                'title' => 'CSS Selectors Quiz',
                'description' => 'Test your knowledge of CSS selectors',
                'question' => 'Which CSS selector targets elements with a specific class?',
                'option_a' => '#classname',
                'option_b' => '.classname',
                'option_c' => '@classname',
                'option_d' => '&classname',
                'correct_answer' => 'B',
                'points' => 5,
                'time_limit' => 30,
                'order_number' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Quiz for JavaScript Fundamentals lesson (lesson_id = 3)
            [
                'lesson_id' => 3,
                'title' => 'JavaScript Variables Quiz',
                'description' => 'Test your understanding of JavaScript variables',
                'question' => 'Which keyword is used to declare a variable in JavaScript?',
                'option_a' => 'var',
                'option_b' => 'let',
                'option_c' => 'const',
                'option_d' => 'All of the above',
                'correct_answer' => 'D',
                'points' => 5,
                'time_limit' => 30,
                'order_number' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Quiz for PHP OOP lesson (lesson_id = 4)
            [
                'lesson_id' => 4,
                'title' => 'PHP OOP Concepts Quiz',
                'description' => 'Test your knowledge of PHP Object-Oriented Programming',
                'question' => 'What is the process of creating an object from a class called?',
                'option_a' => 'Instantiation',
                'option_b' => 'Inheritance',
                'option_c' => 'Encapsulation',
                'option_d' => 'Polymorphism',
                'correct_answer' => 'A',
                'points' => 5,
                'time_limit' => 30,
                'order_number' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('quizzes')->insertBatch($data);
    }
}

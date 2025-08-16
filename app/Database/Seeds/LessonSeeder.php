<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Lessons for Introduction to Web Development (course_id = 1)
            [
                'course_id' => 1,
                'title' => 'HTML Basics',
                'description' => 'Learn the fundamentals of HTML markup language',
                'content' => '<h1>HTML Basics</h1><p>HTML (HyperText Markup Language) is the standard markup language for creating web pages.</p><h2>Key Concepts:</h2><ul><li>HTML elements</li><li>Tags and attributes</li><li>Document structure</li></ul>',
                'video_url' => 'https://example.com/videos/html-basics.mp4',
                'duration' => 45,
                'order_number' => 1,
                'is_free' => true,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 1,
                'title' => 'CSS Styling',
                'description' => 'Learn how to style HTML elements with CSS',
                'content' => '<h1>CSS Styling</h1><p>CSS (Cascading Style Sheets) is used to style and layout web pages.</p><h2>Topics Covered:</h2><ul><li>Selectors</li><li>Properties and values</li><li>Box model</li><li>Flexbox and Grid</li></ul>',
                'video_url' => 'https://example.com/videos/css-styling.mp4',
                'duration' => 60,
                'order_number' => 2,
                'is_free' => false,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 1,
                'title' => 'JavaScript Fundamentals',
                'description' => 'Introduction to JavaScript programming',
                'content' => '<h1>JavaScript Fundamentals</h1><p>JavaScript is a programming language that adds interactivity to web pages.</p><h2>Learning Objectives:</h2><ul><li>Variables and data types</li><li>Functions and scope</li><li>DOM manipulation</li><li>Event handling</li></ul>',
                'video_url' => 'https://example.com/videos/javascript-fundamentals.mp4',
                'duration' => 75,
                'order_number' => 3,
                'is_free' => false,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Lessons for Advanced PHP Programming (course_id = 2)
            [
                'course_id' => 2,
                'title' => 'PHP Object-Oriented Programming',
                'description' => 'Advanced OOP concepts in PHP',
                'content' => '<h1>PHP Object-Oriented Programming</h1><p>Learn advanced OOP concepts including inheritance, polymorphism, and encapsulation.</p>',
                'video_url' => 'https://example.com/videos/php-oop.mp4',
                'duration' => 90,
                'order_number' => 1,
                'is_free' => true,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 2,
                'title' => 'PHP Design Patterns',
                'description' => 'Common design patterns in PHP applications',
                'content' => '<h1>PHP Design Patterns</h1><p>Explore common design patterns like Singleton, Factory, and Observer patterns.</p>',
                'video_url' => 'https://example.com/videos/php-design-patterns.mp4',
                'duration' => 120,
                'order_number' => 2,
                'is_free' => false,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Lessons for Database Design Fundamentals (course_id = 3)
            [
                'course_id' => 3,
                'title' => 'Database Normalization',
                'description' => 'Learn database normalization techniques',
                'content' => '<h1>Database Normalization</h1><p>Understand the principles of database normalization to eliminate data redundancy.</p>',
                'video_url' => 'https://example.com/videos/database-normalization.mp4',
                'duration' => 80,
                'order_number' => 1,
                'is_free' => true,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('lessons')->insertBatch($data);
    }
}

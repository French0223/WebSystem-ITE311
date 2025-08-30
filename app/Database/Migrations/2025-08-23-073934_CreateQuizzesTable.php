<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizzesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'lesson_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'question' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'option_a' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
            ],
            'option_b' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
            ],
            'option_c' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'option_d' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'correct_answer' => [
                'type'       => 'ENUM',
                'constraint' => ['A', 'B', 'C', 'D'],
                'null'       => false,
            ],
            'points' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'time_limit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => 'Time limit in seconds',
                'null'       => true,
            ],
            'order_number' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);

        
        $this->forge->addForeignKey('lesson_id', 'lessons', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('quizzes');
    }

    public function down()
    {
        $this->forge->dropTable('quizzes');
    }
}

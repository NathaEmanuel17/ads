<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableAdvertsQuestions extends Migration
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

            'advert_id' => [ 
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],

            'user_question_id' => [ 
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'comment'        => 'Usuário que fez a pergunta'
            ],

            'question' => [
                'type'       => 'TEXT'
            ],

            'answer' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],

            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null
            ],

            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null
            ],

        ]);

        $this->forge->addKey('id', true);   //primary key

        $this->forge->addForeignKey('advert_id', 'adverts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_question_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('adverts_questions');
    }

    public function down()
    {
        $this->forge->dropTable('adverts_questions');
    }
}

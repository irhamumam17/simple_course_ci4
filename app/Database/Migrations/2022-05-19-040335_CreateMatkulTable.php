<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatkulTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'grade' => [
                'type' => 'INT',
                'constraint' => '2',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => '5',
                'unsigned' => true
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'casacade', 'restrict');
        $this->forge->createTable('matkuls');
    }

    public function down()
    {
        $this->forge->dropTable('matkuls');
    }
}

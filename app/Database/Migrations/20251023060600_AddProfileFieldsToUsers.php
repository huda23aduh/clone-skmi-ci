<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileFieldsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'profile_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'name'
            ],
            'language' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'english',
                'after' => 'isActive'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'profile_image');
        $this->forge->dropColumn('users', 'language');
    }
}
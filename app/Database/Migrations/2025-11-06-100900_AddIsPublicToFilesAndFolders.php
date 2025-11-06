<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsPublicToFilesAndFolders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('files', [
            'is_public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'size'
            ],
            'public_token' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
                'after' => 'is_public'
            ]
        ]);

        $this->forge->addColumn('folders', [
            'is_public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'path'
            ],
            'public_token' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
                'after' => 'is_public'
            ]
        ]);

        $db = \Config\Database::connect();
        $db->query("ALTER TABLE files ADD INDEX (public_token)");
        $db->query("ALTER TABLE folders ADD INDEX (public_token)");

    }


    public function down()
    {
        $this->forge->dropColumn('files', 'is_public');
        $this->forge->dropColumn('files', 'public_token');
        $this->forge->dropColumn('folders', 'is_public');
        $this->forge->dropColumn('folders', 'public_token');
    }
}
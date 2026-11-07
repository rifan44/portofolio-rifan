<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHobbies extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 80],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('hobbies');
    }

    public function down(): void
    {
        $this->forge->dropTable('hobbies', true);
    }
}

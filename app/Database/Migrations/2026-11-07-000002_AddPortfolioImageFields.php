<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPortfolioImageFields extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('profiles', [
            'profile_photo' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'email'],
            'logo_photo' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'profile_photo'],
        ]);

        $this->forge->addColumn('projects', [
            'cover_image' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'source_url'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('projects', 'cover_image');
        $this->forge->dropColumn('profiles', ['profile_photo', 'logo_photo']);
    }
}

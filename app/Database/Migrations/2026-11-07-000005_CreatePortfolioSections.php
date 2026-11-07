<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePortfolioSections extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'section_key' => ['type' => 'VARCHAR', 'constraint' => 40],
            'label' => ['type' => 'VARCHAR', 'constraint' => 80],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('section_key');
        $this->forge->createTable('portfolio_sections');

        $this->db->table('portfolio_sections')->insertBatch([
            ['section_key' => 'about', 'label' => 'Tentang Saya', 'sort_order' => 1],
            ['section_key' => 'projects', 'label' => 'Proyek', 'sort_order' => 2],
            ['section_key' => 'skills', 'label' => 'Keahlian & teknologi', 'sort_order' => 3],
            ['section_key' => 'experience', 'label' => 'Pendidikan & pengalaman', 'sort_order' => 4],
            ['section_key' => 'contact', 'label' => 'Kontak', 'sort_order' => 5],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('portfolio_sections', true);
    }
}

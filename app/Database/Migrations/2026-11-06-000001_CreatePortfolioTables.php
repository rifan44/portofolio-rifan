<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePortfolioTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'TINYINT', 'unsigned' => true],
            'full_name'    => ['type' => 'VARCHAR', 'constraint' => 120],
            'role'         => ['type' => 'VARCHAR', 'constraint' => 160],
            'headline'     => ['type' => 'VARCHAR', 'constraint' => 220],
            'summary'      => ['type' => 'TEXT'],
            'about'        => ['type' => 'TEXT'],
            'location'     => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'focus'        => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'availability'=> ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'email'        => ['type' => 'VARCHAR', 'constraint' => 190],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('profiles');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'platform'   => ['type' => 'VARCHAR', 'constraint' => 40],
            'url'        => ['type' => 'VARCHAR', 'constraint' => 500],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('social_links');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'number'        => ['type' => 'VARCHAR', 'constraint' => 8],
            'category'      => ['type' => 'VARCHAR', 'constraint' => 24],
            'category_label'=> ['type' => 'VARCHAR', 'constraint' => 80],
            'year'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 60],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 160],
            'description'   => ['type' => 'TEXT'],
            'details'       => ['type' => 'TEXT', 'null' => true],
            'demo_url'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'source_url'    => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'sort_order'    => ['type' => 'INT', 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('projects');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id' => ['type' => 'INT', 'unsigned' => true],
            'technology' => ['type' => 'VARCHAR', 'constraint' => 60],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_technologies');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 60],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('skills');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'type'         => ['type' => 'VARCHAR', 'constraint' => 24],
            'date_range'   => ['type' => 'VARCHAR', 'constraint' => 60],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 160],
            'organization' => ['type' => 'VARCHAR', 'constraint' => 160],
            'description'  => ['type' => 'TEXT'],
            'sort_order'   => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('experiences');
    }

    public function down(): void
    {
        $this->forge->dropTable('project_technologies', true);
        $this->forge->dropTable('experiences', true);
        $this->forge->dropTable('skills', true);
        $this->forge->dropTable('projects', true);
        $this->forge->dropTable('social_links', true);
        $this->forge->dropTable('profiles', true);
    }
}

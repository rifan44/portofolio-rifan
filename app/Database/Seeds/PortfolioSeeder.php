<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('profiles')->countAllResults() > 0) {
            return;
        }

        $this->db->transStart();

        $this->db->table('profiles')->insert([
            'id' => 1,
            'full_name' => 'Rifan Maulana',
            'role' => 'Informatics Engineering Student · Full-Stack Developer',
            'headline' => 'Building digital experiences with intention.',
            'summary' => 'Saya merancang dan membangun produk digital yang fungsional, terstruktur, dan punya karakter visual yang kuat.',
            'about' => 'Saya adalah mahasiswa Teknik Informatika yang tertarik pada pengembangan web, eksplorasi UI, dan membangun solusi digital dari ide hingga implementasi.',
            'location' => 'Sukabumi, Indonesia',
            'focus' => 'Full-Stack Development',
            'availability' => 'Terbuka untuk peluang kolaborasi',
            'email' => 'hello@example.com',
        ]);

        $projects = [
            [
                'number' => '01', 'category' => 'web', 'category_label' => 'WEB APPLICATION', 'year' => '2026', 'status' => 'IN DEVELOPMENT',
                'title' => 'Skincare POS System',
                'description' => 'Aplikasi Point of Sale berbasis web untuk membantu operasional toko skincare: transaksi, stok, dan laporan penjualan dalam satu alur.',
                'details' => 'Project POS berbasis web dengan pendekatan SaaS untuk operasional toko skincare. Ruang lingkupnya mencakup keranjang kasir, pencatatan transaksi, dukungan barcode, perhitungan kembalian, manajemen stok, laporan laba-rugi, dan struk thermal. Status dan detail implementasi dapat diperbarui seiring progres project.',
                'demo_url' => null, 'source_url' => null, 'sort_order' => 1,
                'stack' => ['Web Development', 'POS', 'Inventory', 'Dashboard'],
            ],
            [
                'number' => '02', 'category' => 'game', 'category_label' => 'GAME DEVELOPMENT', 'year' => '2026', 'status' => 'IN PROGRESS',
                'title' => '2D Adventure World',
                'description' => 'Eksplorasi game 2D dengan world-building, asset environment, karakter, dan interaksi bergaya cozy adventure.',
                'details' => 'Eksperimen pengembangan game 2D yang berfokus pada world-building dan pengalaman eksplorasi. Area yang dieksplorasi meliputi tile environment, asset props, karakter, movement, serta konsistensi visual antarelemen. Project ini masih dapat berkembang.',
                'demo_url' => null, 'source_url' => null, 'sort_order' => 2,
                'stack' => ['2D Game', 'World Building', 'Game Assets', 'UI/UX'],
            ],
            [
                'number' => '03', 'category' => 'infra', 'category_label' => 'INFRASTRUCTURE', 'year' => '2026', 'status' => 'EXPERIMENTAL',
                'title' => 'Personal Server & Backup',
                'description' => 'Eksperimen self-hosted server dan automated backup menggunakan Restic serta rclone dengan proses verifikasi restore.',
                'details' => 'Eksperimen pengelolaan personal server dan backup data. Workflow yang dieksplorasi menggunakan Restic untuk snapshot dan rclone sebagai remote storage. Fokus utamanya adalah konsistensi backup, pemeriksaan repository, dan pengujian restore.',
                'demo_url' => null, 'source_url' => null, 'sort_order' => 3,
                'stack' => ['Linux', 'Restic', 'rclone', 'Self-hosted'],
            ],
        ];

        foreach ($projects as $project) {
            $stack = $project['stack'];
            unset($project['stack']);
            $this->db->table('projects')->insert($project);
            $projectId = (int) $this->db->insertID();
            foreach ($stack as $sort => $technology) {
                $this->db->table('project_technologies')->insert([
                    'project_id' => $projectId,
                    'technology' => $technology,
                    'sort_order' => $sort + 1,
                ]);
            }
        }

        $this->db->table('skills')->insertBatch([
            ['name' => 'HTML & CSS', 'sort_order' => 1],
            ['name' => 'JavaScript', 'sort_order' => 2],
            ['name' => 'TypeScript', 'sort_order' => 3],
            ['name' => 'React', 'sort_order' => 4],
            ['name' => 'Node.js', 'sort_order' => 5],
            ['name' => 'PHP', 'sort_order' => 6],
            ['name' => 'MySQL', 'sort_order' => 7],
            ['name' => 'Git', 'sort_order' => 8],
        ]);

        $this->db->table('experiences')->insertBatch([
            ['type' => 'education', 'date_range' => '2024 — NOW', 'title' => 'Informatics Engineering Student', 'organization' => 'Universitas Muhammadiyah Sukabumi', 'description' => 'Mempelajari fondasi computer science dan software development, sekaligus mengembangkan project web dan eksplorasi teknologi secara mandiri.', 'sort_order' => 1],
            ['type' => 'project', 'date_range' => 'ONGOING', 'title' => 'Independent Projects & Exploration', 'organization' => 'Personal Development', 'description' => 'Membangun project untuk memperdalam full-stack development, UI implementation, game development, dan system administration.', 'sort_order' => 2],
        ]);

        $this->db->table('social_links')->insertBatch([
            ['platform' => 'GitHub', 'url' => 'https://github.com/', 'sort_order' => 1],
            ['platform' => 'LinkedIn', 'url' => 'https://www.linkedin.com/', 'sort_order' => 2],
        ]);

        $this->db->transComplete();
    }
}

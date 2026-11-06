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
            'role' => 'Mahasiswa Teknik Informatika',
            'headline' => 'Mahasiswa yang belajar teknologi dan membangun proyek.',
            'summary' => 'Saya mahasiswa Teknik Informatika yang sedang mempelajari pengembangan web. Portofolio ini berisi proyek kuliah dan eksperimen pribadi.',
            'about' => 'Saya tertarik pada pengembangan web, game, database, dan server. Hal-hal tersebut saya pelajari melalui perkuliahan dan proyek mandiri.',
            'location' => 'Sukabumi, Indonesia',
            'focus' => 'Pengembangan web',
            'availability' => null,
            'email' => 'hello@example.com',
        ]);

        $projects = [
            [
                'number' => '01', 'category' => 'web', 'category_label' => 'APLIKASI WEB', 'year' => '2026', 'status' => 'Dalam pengembangan',
                'title' => 'Skincare POS System',
                'description' => 'Proyek aplikasi kasir dan inventaris untuk toko skincare. Aplikasi ini masih dalam proses pengembangan.',
                'details' => 'Proyek ini saya kerjakan untuk mempelajari pembuatan aplikasi kasir dan pengelolaan stok. Fitur dan detailnya dapat diperbarui seiring proses pengerjaan.',
                'demo_url' => null, 'source_url' => null, 'sort_order' => 1,
                'stack' => ['Web Development', 'POS', 'Inventory', 'Dashboard'],
            ],
            [
                'number' => '02', 'category' => 'game', 'category_label' => 'PENGEMBANGAN GAME', 'year' => '2026', 'status' => 'Eksperimen',
                'title' => '2D Adventure World',
                'description' => 'Eksperimen game 2D untuk belajar membuat lingkungan, karakter, dan interaksi sederhana.',
                'details' => 'Saya mengeksplorasi pembuatan area permainan, karakter, dan kontrol. Proyek ini masih dapat berubah seiring proses belajar.',
                'demo_url' => null, 'source_url' => null, 'sort_order' => 2,
                'stack' => ['2D Game', 'World Building', 'Game Assets', 'UI/UX'],
            ],
            [
                'number' => '03', 'category' => 'infra', 'category_label' => 'INFRASTRUKTUR', 'year' => '2026', 'status' => 'Eksperimen',
                'title' => 'Personal Server & Backup',
                'description' => 'Eksperimen menyiapkan server pribadi dan mencoba pencadangan data menggunakan Restic dan rclone.',
                'details' => 'Saya mencoba mengelola server pribadi dan menyiapkan pencadangan data. Eksperimen ini mencakup pembuatan snapshot dan pemeriksaan proses pemulihan.',
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
            ['type' => 'education', 'date_range' => '2024 — sekarang', 'title' => 'Mahasiswa Teknik Informatika', 'organization' => 'Universitas Muhammadiyah Sukabumi', 'description' => 'Mempelajari dasar-dasar informatika dan mengerjakan proyek web sebagai bagian dari perkuliahan.', 'sort_order' => 1],
            ['type' => 'project', 'date_range' => 'Berjalan', 'title' => 'Proyek mandiri', 'organization' => 'Proyek pribadi', 'description' => 'Mengerjakan proyek kecil untuk berlatih dan mencoba teknologi yang sedang dipelajari.', 'sort_order' => 2],
        ]);

        $this->db->table('social_links')->insertBatch([
            ['platform' => 'GitHub', 'url' => 'https://github.com/rifan44', 'sort_order' => 1],
        ]);

        $this->db->transComplete();
    }
}

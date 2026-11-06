<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SimplifyPortfolioSeedCopy extends Migration
{
    public function up(): void
    {
        $this->db->table('profiles')
            ->where('id', 1)
            ->where('headline', 'Building digital experiences with intention.')
            ->update([
                'role' => 'Mahasiswa Teknik Informatika',
                'headline' => 'Mahasiswa yang belajar teknologi dan membangun proyek.',
                'summary' => 'Saya mahasiswa Teknik Informatika yang sedang mempelajari pengembangan web. Portofolio ini berisi proyek kuliah dan eksperimen pribadi.',
                'about' => 'Saya tertarik pada pengembangan web, game, database, dan server. Hal-hal tersebut saya pelajari melalui perkuliahan dan proyek mandiri.',
                'focus' => 'Pengembangan web',
                'availability' => null,
            ]);

        $this->updateProject(
            'Skincare POS System',
            'Aplikasi Point of Sale berbasis web untuk membantu operasional toko skincare: transaksi, stok, dan laporan penjualan dalam satu alur.',
            'Proyek aplikasi kasir dan inventaris untuk toko skincare. Aplikasi ini masih dalam proses pengembangan.',
            'Project POS berbasis web dengan pendekatan SaaS untuk operasional toko skincare. Ruang lingkupnya mencakup keranjang kasir, pencatatan transaksi, dukungan barcode, perhitungan kembalian, manajemen stok, laporan laba-rugi, dan struk thermal. Status dan detail implementasi dapat diperbarui seiring progres project.',
            'Proyek ini saya kerjakan untuk mempelajari pembuatan aplikasi kasir dan pengelolaan stok. Fitur dan detailnya dapat diperbarui seiring proses pengerjaan.',
            'IN DEVELOPMENT',
            'Dalam pengembangan',
            'WEB APPLICATION',
            'APLIKASI WEB'
        );

        $this->updateProject(
            '2D Adventure World',
            'Eksplorasi game 2D dengan world-building, asset environment, karakter, dan interaksi bergaya cozy adventure.',
            'Eksperimen game 2D untuk belajar membuat lingkungan, karakter, dan interaksi sederhana.',
            'Eksperimen pengembangan game 2D yang berfokus pada world-building dan pengalaman eksplorasi. Area yang dieksplorasi meliputi tile environment, asset props, karakter, movement, serta konsistensi visual antarelemen. Project ini masih dapat berkembang.',
            'Saya mengeksplorasi pembuatan area permainan, karakter, dan kontrol. Proyek ini masih dapat berubah seiring proses belajar.',
            'IN PROGRESS',
            'Eksperimen',
            'GAME DEVELOPMENT',
            'PENGEMBANGAN GAME'
        );

        $this->updateProject(
            'Personal Server & Backup',
            'Eksperimen self-hosted server dan automated backup menggunakan Restic serta rclone dengan proses verifikasi restore.',
            'Eksperimen menyiapkan server pribadi dan mencoba pencadangan data menggunakan Restic dan rclone.',
            'Eksperimen pengelolaan personal server dan backup data. Workflow yang dieksplorasi menggunakan Restic untuk snapshot dan rclone sebagai remote storage. Fokus utamanya adalah konsistensi backup, pemeriksaan repository, dan pengujian restore.',
            'Saya mencoba mengelola server pribadi dan menyiapkan pencadangan data. Eksperimen ini mencakup pembuatan snapshot dan pemeriksaan proses pemulihan.',
            'EXPERIMENTAL',
            'Eksperimen',
            'INFRASTRUCTURE',
            'INFRASTRUKTUR'
        );

        $this->db->table('experiences')
            ->where('type', 'education')
            ->where('title', 'Informatics Engineering Student')
            ->update([
                'date_range' => '2024 — sekarang',
                'title' => 'Mahasiswa Teknik Informatika',
                'description' => 'Mempelajari dasar-dasar informatika dan mengerjakan proyek web sebagai bagian dari perkuliahan.',
            ]);

        $this->db->table('experiences')
            ->where('type', 'project')
            ->where('title', 'Independent Projects & Exploration')
            ->update([
                'date_range' => 'Berjalan',
                'title' => 'Proyek mandiri',
                'organization' => 'Proyek pribadi',
                'description' => 'Mengerjakan proyek kecil untuk berlatih dan mencoba teknologi yang sedang dipelajari.',
            ]);

        $this->db->table('social_links')
            ->where('platform', 'GitHub')
            ->where('url', 'https://github.com/')
            ->update(['url' => 'https://github.com/rifan44']);

        $this->db->table('social_links')
            ->where('platform', 'LinkedIn')
            ->where('url', 'https://www.linkedin.com/')
            ->delete();
    }

    public function down(): void
    {
        $this->db->table('profiles')
            ->where('id', 1)
            ->where('headline', 'Mahasiswa yang belajar teknologi dan membangun proyek.')
            ->update([
                'role' => 'Informatics Engineering Student · Full-Stack Developer',
                'headline' => 'Building digital experiences with intention.',
                'summary' => 'Saya merancang dan membangun produk digital yang fungsional, terstruktur, dan punya karakter visual yang kuat.',
                'about' => 'Saya adalah mahasiswa Teknik Informatika yang tertarik pada pengembangan web, eksplorasi UI, dan membangun solusi digital dari ide hingga implementasi.',
                'focus' => 'Full-Stack Development',
                'availability' => 'Terbuka untuk peluang kolaborasi',
            ]);

        $projects = [
            ['Skincare POS System', 'Proyek aplikasi kasir dan inventaris untuk toko skincare. Aplikasi ini masih dalam proses pengembangan.', 'Aplikasi Point of Sale berbasis web untuk membantu operasional toko skincare: transaksi, stok, dan laporan penjualan dalam satu alur.', 'Proyek ini saya kerjakan untuk mempelajari pembuatan aplikasi kasir dan pengelolaan stok. Fitur dan detailnya dapat diperbarui seiring proses pengerjaan.', 'Project POS berbasis web dengan pendekatan SaaS untuk operasional toko skincare. Ruang lingkupnya mencakup keranjang kasir, pencatatan transaksi, dukungan barcode, perhitungan kembalian, manajemen stok, laporan laba-rugi, dan struk thermal. Status dan detail implementasi dapat diperbarui seiring progres project.', 'Dalam pengembangan', 'IN DEVELOPMENT', 'APLIKASI WEB', 'WEB APPLICATION'],
            ['2D Adventure World', 'Eksperimen game 2D untuk belajar membuat lingkungan, karakter, dan interaksi sederhana.', 'Eksplorasi game 2D dengan world-building, asset environment, karakter, dan interaksi bergaya cozy adventure.', 'Saya mengeksplorasi pembuatan area permainan, karakter, dan kontrol. Proyek ini masih dapat berubah seiring proses belajar.', 'Eksperimen pengembangan game 2D yang berfokus pada world-building dan pengalaman eksplorasi. Area yang dieksplorasi meliputi tile environment, asset props, karakter, movement, serta konsistensi visual antarelemen. Project ini masih dapat berkembang.', 'Eksperimen', 'IN PROGRESS', 'PENGEMBANGAN GAME', 'GAME DEVELOPMENT'],
            ['Personal Server & Backup', 'Eksperimen menyiapkan server pribadi dan mencoba pencadangan data menggunakan Restic dan rclone.', 'Eksperimen self-hosted server dan automated backup menggunakan Restic serta rclone dengan proses verifikasi restore.', 'Saya mencoba mengelola server pribadi dan menyiapkan pencadangan data. Eksperimen ini mencakup pembuatan snapshot dan pemeriksaan proses pemulihan.', 'Eksperimen pengelolaan personal server dan backup data. Workflow yang dieksplorasi menggunakan Restic untuk snapshot dan rclone sebagai remote storage. Fokus utamanya adalah konsistensi backup, pemeriksaan repository, dan pengujian restore.', 'Eksperimen', 'EXPERIMENTAL', 'INFRASTRUKTUR', 'INFRASTRUCTURE'],
        ];

        foreach ($projects as [$title, $newDescription, $oldDescription, $newDetails, $oldDetails, $newStatus, $oldStatus, $newLabel, $oldLabel]) {
            $this->db->table('projects')
                ->where('title', $title)
                ->where('description', $newDescription)
                ->update([
                    'description' => $oldDescription,
                    'details' => $oldDetails,
                    'status' => $oldStatus,
                    'category_label' => $oldLabel,
                ]);
        }

        $this->db->table('experiences')
            ->where('type', 'education')
            ->where('title', 'Mahasiswa Teknik Informatika')
            ->where('description', 'Mempelajari dasar-dasar informatika dan mengerjakan proyek web sebagai bagian dari perkuliahan.')
            ->update([
                'date_range' => '2024 — NOW',
                'title' => 'Informatics Engineering Student',
                'description' => 'Mempelajari fondasi computer science dan software development, sekaligus mengembangkan project web dan eksplorasi teknologi secara mandiri.',
            ]);

        $this->db->table('experiences')
            ->where('type', 'project')
            ->where('title', 'Proyek mandiri')
            ->where('description', 'Mengerjakan proyek kecil untuk berlatih dan mencoba teknologi yang sedang dipelajari.')
            ->update([
                'date_range' => 'ONGOING',
                'title' => 'Independent Projects & Exploration',
                'organization' => 'Personal Development',
                'description' => 'Membangun project untuk memperdalam full-stack development, UI implementation, game development, dan system administration.',
            ]);

        $this->db->table('social_links')
            ->where('platform', 'GitHub')
            ->where('url', 'https://github.com/rifan44')
            ->update(['url' => 'https://github.com/']);

        if ($this->db->table('social_links')->where('platform', 'LinkedIn')->countAllResults() === 0) {
            $this->db->table('social_links')->insert([
                'platform' => 'LinkedIn',
                'url' => 'https://www.linkedin.com/',
                'sort_order' => 2,
            ]);
        }
    }

    private function updateProject(
        string $title,
        string $oldDescription,
        string $newDescription,
        string $oldDetails,
        string $newDetails,
        string $oldStatus,
        string $newStatus,
        string $oldLabel,
        string $newLabel
    ): void {
        $this->db->table('projects')
            ->where('title', $title)
            ->where('description', $oldDescription)
            ->update([
                'description' => $newDescription,
                'details' => $newDetails,
                'status' => $newStatus,
                'category_label' => $newLabel,
            ]);
    }
}

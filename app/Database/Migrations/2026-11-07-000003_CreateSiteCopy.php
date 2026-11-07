<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiteCopy extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'copy_key' => ['type' => 'VARCHAR', 'constraint' => 80],
            'section' => ['type' => 'VARCHAR', 'constraint' => 60],
            'label' => ['type' => 'VARCHAR', 'constraint' => 140],
            'value' => ['type' => 'TEXT'],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('copy_key');
        $this->forge->createTable('site_copy');

        $defaults = [
            ['Metadata dan aksesibilitas', 'page_title_suffix', 'Akhiran judul halaman', 'Portofolio Pribadi'],
            ['Metadata dan aksesibilitas', 'nav_aria_label', 'Label aksesibilitas navigasi', 'Navigasi utama'],
            ['Metadata dan aksesibilitas', 'home_link_aria', 'Label aksesibilitas tautan beranda', 'Kembali ke beranda'],
            ['Navigasi', 'nav_about', 'Menu: Tentang', 'Tentang'],
            ['Navigasi', 'nav_projects', 'Menu: Proyek', 'Proyek'],
            ['Navigasi', 'nav_experience', 'Menu: Pengalaman', 'Pengalaman'],
            ['Navigasi', 'nav_contact', 'Menu: Kontak', 'Kontak'],
            ['Navigasi', 'nav_admin', 'Menu: Admin', 'Admin'],
            ['Hero dan ilustrasi kode', 'hero_eyebrow', 'Label utama', 'PORTOFOLIO'],
            ['Hero dan ilustrasi kode', 'hero_button_projects', 'Tombol: lihat proyek', 'Lihat proyek'],
            ['Hero dan ilustrasi kode', 'hero_button_contact', 'Tombol: lihat kontak', 'Lihat kontak'],
            ['Hero dan ilustrasi kode', 'hero_location_label', 'Label lokasi', 'LOKASI'],
            ['Hero dan ilustrasi kode', 'hero_focus_label', 'Label minat', 'MINAT'],
            ['Hero dan ilustrasi kode', 'hero_visual_aria', 'Deskripsi aksesibilitas ilustrasi', 'Ilustrasi kode'],
            ['Hero dan ilustrasi kode', 'headline_accent_fallback', 'Teks headline saat bagian kedua kosong', 'yang sedang belajar'],
            ['Hero dan ilustrasi kode', 'code_title', 'Nama file pada panel kode', 'developer.ts'],
            ['Hero dan ilustrasi kode', 'code_content', 'Isi panel kode (gunakan {nama} dan {peran})', "const developer = {\n  name: '{nama}',\n  role: '{peran}',\n  mindset: 'Terus belajar',\n  build: (idea) => solution(idea)\n};"],
            ['Hero dan ilustrasi kode', 'code_status', 'Status panel kode', 'Terus belajar'],
            ['Hero dan ilustrasi kode', 'code_encoding', 'Info bahasa panel kode', 'UTF-8 / JavaScript'],
            ['Hero dan ilustrasi kode', 'hero_note_learning_title', 'Catatan mengambang: judul belajar', 'Belajar'],
            ['Hero dan ilustrasi kode', 'hero_note_learning_subtitle', 'Catatan mengambang: deskripsi belajar', 'sambil praktik'],
            ['Hero dan ilustrasi kode', 'hero_note_project_title', 'Catatan mengambang: judul proyek', 'Membuat'],
            ['Hero dan ilustrasi kode', 'hero_note_project_subtitle', 'Catatan mengambang: deskripsi proyek', 'proyek pribadi'],
            ['Hero dan ilustrasi kode', 'hero_scroll_hint', 'Petunjuk gulir', 'GULIR UNTUK MELIHAT'],
            ['Hero dan ilustrasi kode', 'hero_current_index', 'Angka urutan hero', '01'],
            ['Hero dan ilustrasi kode', 'hero_index_separator', 'Pemisah angka urutan', '-'],
            ['Hero dan ilustrasi kode', 'hero_total_index', 'Jumlah bagian pada indikator', '05'],
            ['Hero dan ilustrasi kode', 'hero_section_index', 'Keterangan bagian hero', '01 / PERKENALAN'],
            ['Tentang saya', 'about_eyebrow', 'Label bagian', '01 / TENTANG SAYA'],
            ['Tentang saya', 'about_profile_label', 'Label kartu profil', 'PROFIL SINGKAT'],
            ['Tentang saya', 'profile_student_label', 'Label status profil', 'MAHASISWA'],
            ['Tentang saya', 'profile_photo_alt', 'Teks alternatif foto profil', 'Foto profil {nama}'],
            ['Tentang saya', 'about_location_fallback', 'Lokasi saat kosong', 'INDONESIA'],
            ['Tentang saya', 'about_projects_label', 'Hitungan: nama proyek', 'PROYEK'],
            ['Tentang saya', 'about_projects_tail', 'Hitungan proyek: baris kedua', 'SAYA'],
            ['Tentang saya', 'about_skills_label', 'Hitungan: nama keahlian', 'KEAHLIAN'],
            ['Tentang saya', 'about_skills_tail', 'Hitungan keahlian: baris kedua', 'SAYA'],
            ['Tentang saya', 'about_history_label', 'Hitungan: nama riwayat', 'RIWAYAT'],
            ['Tentang saya', 'about_history_tail', 'Hitungan riwayat: baris kedua', 'SAYA'],
            ['Proyek', 'projects_eyebrow', 'Label bagian', '02 / PROYEK'],
            ['Proyek', 'projects_title_first', 'Judul proyek: baris pertama', 'Beberapa proyek'],
            ['Proyek', 'projects_title_second', 'Judul proyek: baris kedua', 'yang saya kerjakan.'],
            ['Proyek', 'projects_aside', 'Keterangan bagian proyek', 'Proyek kuliah dan eksperimen pribadi.'],
            ['Proyek', 'category_web', 'Kategori: aplikasi web', 'Aplikasi web'],
            ['Proyek', 'category_game', 'Kategori: game', 'Pengembangan game'],
            ['Proyek', 'category_infra', 'Kategori: infrastruktur', 'Infrastruktur'],
            ['Proyek', 'filter_all', 'Filter semua proyek', 'Semua'],
            ['Proyek', 'project_list_label', 'Label daftar proyek', 'DAFTAR PROYEK'],
            ['Proyek', 'project_number_suffix', 'Kata setelah nomor proyek', 'PROYEK'],
            ['Proyek', 'project_detail_label', 'Label aksesibilitas tombol detail ({proyek})', 'Lihat detail {proyek}'],
            ['Proyek', 'project_image_alt', 'Teks alternatif gambar pratinjau ({proyek})', 'Pratinjau proyek {proyek}'],
            ['Proyek', 'projects_footnote', 'Keterangan bawah daftar', 'PROYEK YANG SAYA KERJAKAN'],
            ['Proyek', 'projects_total_suffix', 'Label jumlah total proyek', 'TOTAL PROYEK'],
            ['Keahlian', 'skills_eyebrow', 'Label bagian', '03 / KEAHLIAN'],
            ['Keahlian', 'skills_title_first', 'Judul keahlian: baris pertama', 'Keahlian dan teknologi'],
            ['Keahlian', 'skills_title_second', 'Judul keahlian: baris kedua', 'yang saya pelajari.'],
            ['Keahlian', 'skills_aside', 'Keterangan bagian keahlian', 'Daftar teknologi yang sedang dipelajari.'],
            ['Keahlian', 'skills_note_first', 'Judul catatan: baris pertama', 'Belajar'],
            ['Keahlian', 'skills_note_second', 'Judul catatan: baris kedua', 'sambil praktik.'],
            ['Keahlian', 'skills_description', 'Deskripsi keahlian', 'Teknologi yang pernah saya gunakan atau sedang saya pelajari.'],
            ['Keahlian', 'skills_caption', 'Label daftar keahlian', 'DAFTAR KEAHLIAN'],
            ['Pendidikan dan pengalaman', 'experience_eyebrow', 'Label bagian', '04 / PENDIDIKAN & PENGALAMAN'],
            ['Pendidikan dan pengalaman', 'experience_title_first', 'Judul riwayat: baris pertama', 'Pendidikan dan'],
            ['Pendidikan dan pengalaman', 'experience_title_second', 'Judul riwayat: baris kedua', 'pengalaman.'],
            ['Pendidikan dan pengalaman', 'experience_aside', 'Keterangan bagian riwayat', 'Riwayat pendidikan, kegiatan, dan proyek yang pernah saya ikuti.'],
            ['Pendidikan dan pengalaman', 'experience_type_education', 'Jenis riwayat: pendidikan', 'Pendidikan'],
            ['Pendidikan dan pengalaman', 'experience_type_organization', 'Jenis riwayat: organisasi', 'Organisasi'],
            ['Pendidikan dan pengalaman', 'experience_type_work', 'Jenis riwayat: pengalaman kerja', 'Pengalaman kerja'],
            ['Pendidikan dan pengalaman', 'experience_type_achievement', 'Jenis riwayat: pencapaian', 'Pencapaian'],
            ['Pendidikan dan pengalaman', 'experience_type_project', 'Jenis riwayat: proyek', 'Proyek / aktivitas'],
            ['Kontak dan footer', 'contact_eyebrow', 'Label bagian kontak', '05 / KONTAK'],
            ['Kontak dan footer', 'contact_heading', 'Judul kontak', 'Hubungi saya.'],
            ['Kontak dan footer', 'contact_email_message', 'Keterangan saat email tersedia', 'Silakan hubungi saya melalui email atau tautan berikut.'],
            ['Kontak dan footer', 'contact_no_email_message', 'Keterangan saat email belum tersedia', 'Tautan kontak yang tersedia ada di bawah ini.'],
            ['Kontak dan footer', 'contact_email_button', 'Tombol email', 'Kirim email'],
            ['Kontak dan footer', 'contact_side_note', 'Catatan samping kontak', 'PORTOFOLIO PRIBADI'],
            ['Kontak dan footer', 'footer_label', 'Label footer', 'PORTOFOLIO PRIBADI'],
            ['Aksesibilitas dan dialog', 'back_to_top_label', 'Tombol kembali ke atas', 'Kembali ke atas'],
            ['Aksesibilitas dan dialog', 'mobile_menu_open_label', 'Tombol buka navigasi', 'Buka navigasi'],
            ['Aksesibilitas dan dialog', 'mobile_menu_close_label', 'Tombol tutup navigasi', 'Tutup navigasi'],
            ['Aksesibilitas dan dialog', 'modal_close_label', 'Tombol tutup detail proyek', 'Tutup'],
            ['Aksesibilitas dan dialog', 'modal_live_label', 'Tautan demo proyek', 'Live preview'],
            ['Aksesibilitas dan dialog', 'modal_source_label', 'Tautan source proyek', 'Source code'],
            ['Aksesibilitas dan dialog', 'modal_empty_links', 'Pesan saat proyek belum punya tautan', 'Tautan proyek belum ditambahkan'],
            ['Aksesibilitas dan dialog', 'project_modal_separator', 'Pemisah detail proyek', '·'],
            ['Aksesibilitas dan dialog', 'experience_separator', 'Pemisah nama dan jenis riwayat', '·'],
            ['Aksesibilitas dan dialog', 'contact_social_arrow', 'Penanda tautan sosial', '↗'],
            ['Aksesibilitas dan dialog', 'close_icon_label', 'Karakter tombol tutup', '×'],
        ];

        $rows = [];
        foreach ($defaults as $index => [$section, $key, $label, $value]) {
            $rows[] = [
                'section' => $section,
                'copy_key' => $key,
                'label' => $label,
                'value' => $value,
                'sort_order' => $index + 1,
            ];
        }
        $this->db->table('site_copy')->insertBatch($rows);
    }

    public function down(): void
    {
        $this->forge->dropTable('site_copy', true);
    }
}

# PROJECT_CONTEXT.md

> Ringkasan faktual project untuk handoff agent. Source code dan konfigurasi tetap menjadi sumber kebenaran. Tidak mencatat credential atau isi `.env`.

## Identitas dan tujuan

- Portofolio pribadi Rifan Maulana untuk kebutuhan UTS Pemrograman Web Framework.
- Rencana domain utama: `rifrain.my.id`; rencana salinan penilaian: subdomain kelas. Game dan POS akan menjadi proyek eksternal masing-masing di `rifrain.tech` dan `skincare.rifrain.my.id` setelah tersedia.
- Repository GitHub: `https://github.com/rifan44/portofolio-rifan`, branch `main`.
- Status hosting/DNS dan domain kelas: belum diketahui.

## Stack aktual

- PHP dan CodeIgniter 4.7 (`composer.json`, `composer.lock`; versi terkunci `v4.7.6`).
- MySQL/MariaDB menggunakan driver MySQLi.
- PHP lokal yang tersedia saat implementasi: 8.4.27. CI4 4.7.x membutuhkan PHP 8.2+ serta ekstensi `intl` dan `mbstring`; MySQL memerlukan `mysqli`/`mysqlnd`.
- UI tetap memakai CSS dan JavaScript vanilla. Framework menjalankan halaman server-rendered; tidak ada build frontend.
- `public/` adalah web root CI4; server harus mengarahkan document root ke sana.

## Struktur aplikasi

```text
app/
├── Config/                  # Routes, database, filters, dan konfigurasi admin
├── Controllers/             # Home dan CMS admin
├── Database/Migrations/     # Schema portfolio
├── Database/Seeds/          # Konten awal
├── Filters/                 # Proteksi session admin
├── Models/                  # Profile, projects, skills, experiences, socials
└── Views/                   # Halaman portfolio dan CMS
public/
├── assets/app.js            # Filter, modal detail, navigasi, reveal
├── assets/styles.css        # Tema visual portofolio dan admin
└── index.php                # Front controller CodeIgniter
legacy-static/               # Salinan HTML/CSS/JS statis sebelum migrasi
Game/                        # Paket aset game, tidak diperlukan aplikasi portfolio
```

## Data dan fitur

- Halaman publik mengambil profil, proyek, teknologi proyek, skills, pengalaman/pendidikan/pencapaian, dan social links dari MySQL melalui model CI4.
- Tabel: `profiles`, `projects`, `project_technologies`, `skills`, `experiences`, `social_links`. Relasi teknologi proyek memakai foreign key cascade.
- Migrasi: `app/Database/Migrations/2026-11-06-000001_CreatePortfolioTables.php`.
- Seeder: `app/Database/Seeds/PortfolioSeeder.php`; hanya mengisi konten demo jika tabel `profiles` kosong.
- CMS di `/admin/login` menyediakan edit profil, proyek, daftar skill, riwayat (pendidikan/organisasi/kerja/pencapaian/proyek), dan tautan sosial.
- Admin memakai `ADMIN_EMAIL` dan hash `ADMIN_PASSWORD_HASH` dari `.env`, session auth, pembatas login per-session, validasi input, CSRF, serta escape output.
- `.env.example` berisi kerangka konfigurasi tanpa credential. `.env` belum dibuat.
- Data awal masih mengandung email dan social URL contoh. Perbarui melalui CMS sebelum publikasi.
- URL proyek eksternal dapat diatur dari CMS setelah game/POS live; sinkronisasi antar salinan domain tidak tersedia.

## Setup dan status database

- Panduan lokal/deploy ada di `README.md`.
- Jalankan `composer install`, buat database baru, lalu `php spark migrate` dan `php spark db:seed PortfolioSeeder`.
- Database belum dibuat atau diubah oleh agent; migrasi dan seeder belum dijalankan.
- Login admin belum dapat dipakai sampai `ADMIN_EMAIL` dan hash password diisi ke `.env`.

## Pengujian dan verifikasi

- Manifest appstarter menyediakan dependency/framework dan konfigurasi PHPUnit; belum ada verifikasi aplikasi atau browser yang dijalankan pada sesi migrasi ini.
- Composer berhasil membuat appstarter dan mengunci `codeigniter4/framework v4.7.6`. Composer 2.4.1 mengeluarkan sejumlah deprecation notice saat berjalan di PHP 8.4.
- Tidak ada uji koneksi database karena database lokal belum dikonfigurasi.
- Source asli di folder `rifrain.tech` berada di bawah root Git user Windows, jadi publikasi dilakukan dari clone bersih repository ini.

## Risiko dan next steps

1. Buat database MySQL lokal, salin `.env.example` menjadi `.env`, lalu jalankan migrasi dan seeder.
2. Siapkan password admin lokal, simpan hanya hash di `.env`, dan cek CMS serta halaman publik di browser.
3. Verifikasi hosting mendukung PHP 8.2+, ekstensi yang diperlukan, MySQLi, dan document root ke `public/`.
4. Ganti konten contoh dan URL placeholder sebelum publikasi.
5. Periksa lisensi paket di `Game/` sebelum aset didistribusikan.

## Catatan workspace

- `legacy-static/` sengaja mempertahankan source halaman statis lama sebagai arsip.
- `.ci4-scaffold/` di folder source asli adalah scaffold sementara hasil Composer yang tidak dipakai runtime dan sudah masuk `.gitignore`. Percobaan pembersihannya ditolak execution policy; folder tidak disalin ke repository ini.
- Tidak ada file `.env`, secret, deployment, atau migrasi database yang dibuat/dijalankan.

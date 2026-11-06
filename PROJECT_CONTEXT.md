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
- Database lokal baru `rifrain_portfolio` telah dibuat di MySQL lokal; tidak ada database hosting/production yang diubah.
- Migration dan seeder berhasil. Data lokal sekarang: 1 profil, 3 proyek, 12 relasi teknologi, 8 skill, 2 riwayat, 2 tautan sosial.
- `.env` lokal berada hanya di clone kerja dan diabaikan Git; admin tetap belum aktif sampai `ADMIN_EMAIL` dan hash password dikonfigurasi.

## Pengujian dan verifikasi

- `composer test -- --no-coverage`: lulus, 5 test / 7 assertion (test bawaan appstarter).
- `php spark migrate` dan `php spark db:seed PortfolioSeeder`: berhasil di database lokal baru.
- Server lokal: homepage dan `/admin/login` merespons HTTP 200; isi profil, proyek, skill, dan konfigurasi login tampil.
- Composer mengunci `codeigniter4/framework v4.7.6`. Composer 2.4.1 mengeluarkan deprecation notice di PHP 8.4.
- Visual browser QA tidak selesai: Computer Use helper gagal tiga kali dengan `native pipe unavailable`; HTTP smoke check tetap berhasil.
- Belum ada uji CRUD setelah login atau uji deploy/hosting.
- Source asli di folder `rifrain.tech` berada di bawah root Git user Windows, jadi publikasi dilakukan dari clone bersih repository ini.

## Risiko dan next steps

1. Isi kredensial admin lokal menggunakan hash password, lalu uji login dan CRUD CMS.
2. Lakukan visual browser QA di beberapa ukuran layar.
3. Verifikasi hosting mendukung PHP 8.2+, ekstensi yang diperlukan, MySQLi, dan document root ke `public/`.
4. Ganti konten contoh dan URL placeholder sebelum publikasi.
5. Periksa lisensi paket di `Game/` sebelum aset didistribusikan.

## Catatan workspace

- `legacy-static/` sengaja mempertahankan source halaman statis lama sebagai arsip.
- `.ci4-scaffold/` di folder source asli adalah scaffold sementara hasil Composer yang tidak dipakai runtime dan sudah masuk `.gitignore`. Percobaan pembersihannya ditolak execution policy; folder tidak disalin ke repository ini.
- Tidak ada file `.env`, secret, deployment, atau migrasi database yang dibuat/dijalankan.

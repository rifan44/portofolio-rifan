# Rifan Portfolio

Portofolio pribadi Rifan dengan CodeIgniter 4, PHP, dan MySQL. Halaman publik membaca profil, proyek, daftar teknologi, riwayat pendidikan/pengalaman/pencapaian, serta tautan kontak dari database.

## Stack dan kebutuhan server

- PHP 8.2+ dengan ekstensi `intl`, `mbstring`, `mysqli`/`mysqlnd`.
- MySQL/MariaDB.
- CodeIgniter 4.7 (`codeigniter4/framework` dikelola lewat Composer).
- Document root web server harus menunjuk ke folder `public/`.

[Persyaratan resmi CodeIgniter](https://codeigniter.com/user_guide/intro/requirements.html) menyebut CI4 4.7.x memerlukan PHP 8.2 atau lebih baru. [Panduan appstarter](https://github.com/codeigniter4/appstarter) menyarankan web root diarahkan ke `public/`. Pastikan versi PHP ini tersedia di hosting sebelum memilih paket dan subdomain.

## Menjalankan lokal

1. Buat database MySQL bernama `rifrain_portfolio`.
2. Salin `.env.example` menjadi `.env`, lalu isi konfigurasi database.
3. Pastikan dependensi tersedia dengan `composer install`.
4. Buat tabel dan isi data awal:

   ```bash
   php spark migrate
   php spark db:seed PortfolioSeeder
   ```

5. Jalankan server lokal:

   ```bash
   php spark serve
   ```

   Buka `http://localhost:8080`.

Seeder hanya mengisi konten awal jika tabel profil masih kosong. Ubah email, link sosial, status proyek, dan deskripsi demo melalui CMS atau database sebelum situs dipublikasikan.

## Login dan CMS admin

CMS tersedia di `/admin/login`. Login memakai email dan hash password dari environment, bukan akun default yang tertanam di source.

Buat hash password secara lokal, lalu masukkan outputnya ke `ADMIN_PASSWORD_HASH` dalam `.env`:

```bash
php -r "echo password_hash('GANTI_DENGAN_PASSWORD_KUAT', PASSWORD_DEFAULT), PHP_EOL;"
```

Isi `ADMIN_EMAIL` dengan email admin. Jangan commit `.env` atau bagikan nilai kredensial. CMS memiliki session login, pembatas percobaan login per session, validasi input, CSRF untuk form, dan escape output.

## Struktur database

- `profiles`: satu baris identitas dan informasi kontak utama.
- `social_links`: URL media sosial/website yang terhubung ke area kontak.
- `projects`: metadata setiap proyek.
- `project_technologies`: daftar teknologi relasional untuk setiap proyek; terhapus otomatis saat proyek dihapus.
- `skills`: keahlian dan teknologi.
- `experiences`: pendidikan, organisasi, pekerjaan, pencapaian, dan aktivitas proyek dengan kolom jenis.

Migrasi skema ada di `app/Database/Migrations`; data awal ada di `app/Database/Seeds/PortfolioSeeder.php`.

## Deploy ke hosting bersama

1. Pastikan cPanel mendukung PHP 8.2+, ekstensi yang diperlukan, MySQL/MariaDB, dan pengaturan document root subdomain.
2. Buat database dan user MySQL di cPanel, lalu berikan izin ke database.
3. Upload source CI4 dan dependensi. Jika Composer tersedia di hosting, jalankan `composer install --no-dev --optimize-autoloader`; kalau tidak, jalankan di mesin lokal dan upload juga folder `vendor/`.
4. Atur document root domain ke folder `public/` project agar `app/`, `writable/`, dan konfigurasi tidak menjadi direktori web publik.
5. Buat `.env` di root aplikasi dengan `CI_ENVIRONMENT = production`, `database.default.DBDebug = false`, base URL HTTPS domain yang benar, koneksi database hosting, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD_HASH`.
6. Pastikan `writable/` bisa ditulis oleh PHP. Jalankan migrasi dan seeder satu kali setelah memeriksa nama database yang dituju:

   ```bash
   php spark migrate
   php spark db:seed PortfolioSeeder
   ```

Jangan unggah file `.env` ke repository. Untuk domain kelas, buat salinan project dengan konfigurasi `.env` dan database miliknya sendiri; perubahan konten di satu salinan tidak otomatis tersinkron dengan domain lain.

## Domain proyek lain

URL proyek dapat ditambahkan atau diperbarui dari CMS. Setelah proyek game di `rifrain.tech` atau POS di `skincare.rifrain.my.id` tersedia publik, masukkan URL live dan repository pada formulir proyek agar tampil di modal detail portofolio.

## Catatan repository

- `legacy-static/` menyimpan salinan HTML/CSS/JavaScript statis versi awal sebagai arsip migrasi. Aplikasi aktif menggunakan `app/` dan `public/`.
- `Game/` berisi koleksi aset pixel-art/game dan tidak diperlukan untuk menjalankan halaman portofolio ini. Periksa lisensi masing-masing paket sebelum distribusi.
- Sebelum menjalankan migration/seeder di server lain, pastikan `.env` menunjuk ke database project yang benar. Jangan arahkan perintah ini ke database production yang sudah berisi data tanpa meninjau dampaknya.

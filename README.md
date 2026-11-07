# Rifan Maulana — Portfolio

Portofolio pribadi Rifan Maulana dengan CodeIgniter 4, PHP, dan MySQL. Halaman publik membaca profil, proyek, daftar teknologi, riwayat pendidikan/pengalaman/pencapaian, keahlian, dan tautan kontak dari database.

## Stack dan kebutuhan server

- PHP 8.2+ dengan ekstensi `intl`, `mbstring`, `mysqli`/`mysqlnd`; PHP 8.4 direkomendasikan jika tersedia.
- MySQL/MariaDB.
- CodeIgniter 4.7 (`codeigniter4/framework` dikelola lewat Composer).
- Document root web server harus menunjuk ke folder `public/`.

[Persyaratan resmi CodeIgniter](https://codeigniter.com/user_guide/intro/requirements.html) menyebut CI4 4.7.x memerlukan PHP 8.2 atau lebih baru. [Panduan deployment CI4](https://codeigniter.com/user_guide/installation/deployment.html) menjelaskan layout shared hosting dan menyarankan web root diarahkan ke `public/`. Pastikan versi PHP dan ekstensi tersedia di hosting sebelum mengunggah aplikasi.

## Menjalankan lokal

Jalankan perintah dari **root project**, yaitu folder yang berisi `spark`, `composer.json`, `app/`, dan `public/`. File `.env` harus berada di folder yang sama dengan `spark`, bukan di dalam `public/`. Jika ada beberapa salinan project, pastikan server dijalankan dari salinan yang `.env`-nya sedang dikonfigurasi.

1. Buat database MySQL bernama `rifrain_portfolio`.
2. Salin `.env.example` menjadi `.env`, lalu isi konfigurasi database. Di PowerShell, gunakan `Copy-Item .env.example .env`; jangan menimpa `.env` yang sudah berisi konfigurasi lokal.
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

Di Windows, buka terminal pada root project tersebut sebelum menjalankan `php spark serve`. Mengedit `.env` pada salinan folder lain tidak mengubah konfigurasi server yang sedang berjalan. Setelah mengganti konfigurasi, hentikan lalu jalankan ulang server lokal.

## Login dan CMS admin

CMS tersedia di `/admin/login`. Login memakai email dan hash password dari environment, bukan akun default yang tertanam di source.

Di CMS, bagian profil dapat mengubah nama lengkap, peran, headline, ringkasan, tentang, lokasi, fokus, status ketersediaan, dan email. Proyek, teknologi, keahlian, pendidikan/pengalaman/pencapaian, hobi/minat, serta tautan sosial juga dikelola dari sana. Branding nama mengikuti kolom nama lengkap pada profil. Hobi/minat akan tampil di area Tentang Saya setelah ditambahkan. Teks antarmuka yang bersifat tetap tidak menjadi field CMS; ajukan revisi bila ingin mengubahnya.

CMS juga mendukung foto profil, logo pada header/footer, dan gambar pratinjau pada kartu proyek. Editor crop berjalan di browser: foto profil dan logo dipotong persegi, gambar proyek dipotong 16:9, lalu hasilnya dikonversi ke WebP maksimal 340 KB sebelum dikirim. Batas ini menjaga total unggahan tetap di bawah batas request Nginx 1 MB pada server saat ini. Server tetap memeriksa tipe file, ukuran, dan dimensi; file disimpan di `writable/uploads/portfolio`, di luar folder publik. Saat memindahkan atau mencadangkan situs, sertakan folder upload tersebut. Urutan proyek dan riwayat pendidikan/pengalaman/pencapaian dapat diatur dengan tombol naik/turun di panel admin.

Untuk memastikan login terkonfigurasi, cek bahwa `ADMIN_EMAIL` dan `ADMIN_PASSWORD_HASH` terisi di `.env` pada root project yang sedang dijalankan. Jika halaman login menyatakan admin belum dikonfigurasi, biasanya file `.env` berada di salinan folder yang berbeda atau server belum dijalankan ulang.

Buat hash password secara lokal, lalu masukkan outputnya ke `ADMIN_PASSWORD_HASH` dalam `.env`:

```bash
php -r "echo password_hash('GANTI_DENGAN_PASSWORD_KUAT', PASSWORD_DEFAULT), PHP_EOL;"
```

Isi `ADMIN_EMAIL` dengan email admin. Jangan commit `.env` atau salin `.env` lokal ke hosting. Jika deploy nanti, buat konfigurasi produksi yang baru di root aplikasi server dan gunakan kredensial admin tersendiri. CMS memiliki session login, pembatas percobaan login per session, validasi input, CSRF untuk form, dan escape output.

## Struktur database

- `profiles`: satu baris identitas dan informasi kontak utama.
- `social_links`: URL media sosial/website yang terhubung ke area kontak.
- `projects`: metadata setiap proyek.
- `project_technologies`: daftar teknologi relasional untuk setiap proyek; terhapus otomatis saat proyek dihapus.
- `skills`: keahlian dan teknologi.
- `experiences`: pendidikan, organisasi, pekerjaan, pencapaian, dan aktivitas proyek dengan kolom jenis.
- `site_copy`: teks antarmuka yang dipakai oleh halaman publik; tidak tersedia sebagai field CMS.
- `hobbies`: daftar hobi dan minat yang ditambahkan melalui CMS.

Migrasi skema ada di `app/Database/Migrations`; data awal ada di `app/Database/Seeds/PortfolioSeeder.php`.

## Deploy ke hosting bersama

### Persiapan

1. Pastikan paket hosting menyediakan PHP 8.2 atau lebih baru (PHP 8.4 direkomendasikan), ekstensi `intl`, `mbstring`, dan `mysqli`, MySQL/MariaDB, SSL untuk domain, serta akses untuk mengatur document root atau menjalankan panduan layout alternatif CI4.
2. Buat database dan user MySQL melalui panel hosting, lalu berikan hak akses ke database tersebut. Gunakan nama database, username, dan password persis seperti yang diberikan panel.
3. Siapkan kredensial admin khusus untuk hosting. Buat hash password secara lokal memakai langkah di bagian Login dan CMS admin.

### Upload dan document root

Layout yang disarankan adalah menyimpan seluruh project di folder non-publik, lalu mengarahkan document root domain ke `public/` di dalam project. Dengan begitu, `app/`, `vendor/`, `writable/`, dan `.env` berada di luar web root.

Jika Composer tersedia di hosting, jalankan dari root project:

```bash
composer install --no-dev --optimize-autoloader
```

Jika Composer tidak tersedia, siapkan salinan release terpisah dari project, jalankan perintah yang sama di mesin lokal, lalu upload `vendor/` hasil instalasi itu. Jangan menimpa `vendor/` pada folder kerja yang digunakan untuk pengembangan dan test.

> Jangan jadikan root project sebagai document root. Jika panel tidak mengizinkan document root menunjuk ke `public/`, ikuti layout dua direktori di [panduan deployment CI4](https://codeigniter.com/user_guide/installation/deployment.html) dan sesuaikan path di front controller. Simpan `.env` di luar semua folder yang dapat diakses publik.

### Konfigurasi produksi

Buat file `.env` baru di root project pada server; jangan upload `.env` dari komputer lokal. Atur minimal nilai berikut memakai data domain dan database dari panel hosting:

```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://rifrain.my.id/'
app.forceGlobalSecureRequests = true

database.default.hostname = localhost
database.default.database = NAMA_DATABASE_DARI_PANEL
database.default.username = USER_DATABASE_DARI_PANEL
database.default.password = PASSWORD_DATABASE_DARI_PANEL
database.default.DBDriver = MySQLi
database.default.port = 3306
database.default.DBDebug = false

ADMIN_EMAIL = EMAIL_ADMIN
ADMIN_PASSWORD_HASH = HASH_PASSWORD_ADMIN
```

Aktifkan SSL sebelum memakai base URL HTTPS dan `app.forceGlobalSecureRequests`. Cookie session dikirim dengan flag `Secure` ketika environment `production`; aplikasi admin memerlukan HTTPS agar browser mengirim cookie tersebut. Jangan menggunakan password polos pada `ADMIN_PASSWORD_HASH`.

Pastikan PHP dapat menulis ke `writable/cache/`, `writable/logs/`, dan `writable/session/`. Gunakan permission minimum yang didukung hosting, biasanya 755 atau 775; jangan memberi permission 777.

### Database dan pemeriksaan setelah upload

Jalankan migration setiap kali aplikasi diperbarui agar perubahan struktur database diterapkan. Jalankan seeder hanya pada pemasangan baru di database kosong; jangan menjalankan seeder pada database berisi data tanpa meninjau isinya.

```bash
php spark migrate
php spark db:seed PortfolioSeeder
```

Setelah itu, buka homepage dan `/admin/login` melalui HTTPS, periksa koneksi database, lalu login dan tinjau konten. Data seed masih berisi email dan tautan sosial contoh; ganti melalui CMS sebelum situs diumumkan. Untuk domain kelas, gunakan file `.env` dan database tersendiri. Perubahan konten di satu salinan tidak otomatis tersinkron ke domain lain.

### Home server dengan Ubuntu, Nginx, PHP-FPM, dan MariaDB

Untuk server pribadi, letakkan root aplikasi di luar document root Nginx. Contoh layout yang dipakai untuk `rifrain.my.id` adalah `/var/www/rifrain.my.id` sebagai root aplikasi dan `/var/www/rifrain.my.id/public` sebagai document root. Jangan jadikan `/var/www/rifrain.my.id` sendiri sebagai document root karena `.env`, `app/`, dan `vendor/` berada di sana.

Contoh konfigurasi Nginx:

```nginx
server {
    listen 80;
    server_name rifrain.my.id;
    root /var/www/rifrain.my.id/public;
    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param HTTP_X_FORWARDED_PROTO $http_x_forwarded_proto;
        fastcgi_param HTTP_CF_CONNECTING_IP $http_cf_connecting_ip;
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Sesuaikan socket PHP-FPM dengan versi PHP pada server. Setelah mengubah konfigurasi, jalankan `nginx -t` sebelum reload Nginx. Atur pemilik file agar Nginx/PHP-FPM dapat membaca aplikasi dan menulis hanya ke `writable/`; jangan menggunakan permission `777`.

Buat database MariaDB dan user khusus aplikasi dengan hak hanya pada database portofolio. Biarkan MariaDB hanya mendengarkan di localhost; jangan membuka port database ke internet. Buat `.env` baru di root aplikasi server, jalankan migration dan seeder pada database kosong, kemudian tinjau data dari `/admin`.

Jika memakai Cloudflare Tunnel, traffic publik menggunakan HTTPS dan tunnel meneruskan `X-Forwarded-Proto` serta `CF-Connecting-IP`. Contoh Nginx di atas meneruskan header tersebut ke PHP-FPM; CI4 hanya mempercayai proxy loopback. Cloudflare Tunnel mengenkripsi sambungan antara server dan jaringan Cloudflare. [Dokumentasi header Cloudflare](https://developers.cloudflare.com/fundamentals/reference/http-headers/) dan [Cloudflare Tunnel](https://developers.cloudflare.com/tunnel/) menjelaskan header dan jalur koneksi ini. Cookie admin production memakai flag `Secure`, jadi login membutuhkan HTTPS di browser.

## Domain proyek lain

URL proyek dapat ditambahkan atau diperbarui dari CMS. Setelah proyek game di `rifrain.tech` atau POS di `skincare.rifrain.my.id` tersedia publik, masukkan URL live dan repository pada formulir proyek agar tampil di modal detail portofolio.

## Keamanan dan data

- Form admin dilindungi CSRF berbasis session, validasi input, dan escape output. CI4 menyarankan CSRF berbasis session ketika aplikasi memakai session; lihat [panduan keamanan CI4](https://codeigniter.com/user_guide/libraries/security.html).
- Login admin memakai pembatas percobaan gagal per IP dengan token bucket 5 percobaan yang pulih bertahap selama 10 menit. Jika hosting berada di belakang reverse proxy, atur alamat proxy tepercaya di `app/Config/App.php` agar alamat client yang dibaca aplikasi akurat.
- `AGENTS.md`, `PROJECT_CONTEXT.md`, dan `.env` adalah file lokal yang tidak boleh masuk repository atau diunggah ke hosting.
- Sebelum menjalankan migration atau seeder di server, pastikan `.env` menunjuk ke database project yang benar. Jangan jalankan seeder ke database production yang sudah berisi data tanpa meninjau dampaknya.

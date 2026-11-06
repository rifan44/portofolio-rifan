# AGENTS.md

Instruksi bersama untuk seluruh AI coding agent yang bekerja pada repository ini.

## 1. Aturan kerja wajib

- Baca file ini dan `PROJECT_CONTEXT.md` sebelum memulai pekerjaan.
- Periksa struktur repository, `package.json`/manifest, konfigurasi, dan implementasi terkait sebelum mengusulkan atau mengubah kode.
- Jangan mengarang nama file, API, database schema, dependency, atau versi.
- Pertahankan framework, arsitektur, coding convention, dan dependency yang sudah digunakan.
- Jangan melakukan refactor besar atau mengubah bagian yang tidak terkait tanpa alasan dan persetujuan.
- Jangan menghapus file, menjalankan perintah destruktif, mengubah database secara berisiko, atau melakukan deployment tanpa persetujuan eksplisit.
- Jangan meminta, menampilkan, atau menyalin secret seperti API key, password, token, private key, maupun isi `.env`.
- Jangan menaruh secret di dokumentasi, prompt, log, atau commit.
- Jika requirement belum jelas dan memengaruhi desain/hasil, tanyakan satu klarifikasi yang paling penting sebelum implementasi.

## 2. Workflow sebelum mengubah kode

1. Ringkas requirement dan acceptance criteria.
2. Periksa file, fungsi, komponen, endpoint, schema, dan test yang relevan.
3. Identifikasi root cause jika tugasnya debugging.
4. Sampaikan rencana singkat dan daftar file yang kemungkinan terdampak.
5. Implementasikan perubahan sekecil mungkin.
6. Jalankan test, lint, type-check, atau build yang memang tersedia di repository.
7. Tinjau diff untuk memastikan tidak ada perubahan tak terkait.

Untuk tugas sederhana yang jelas dan berisiko rendah, rencana dapat dibuat singkat; tetap periksa konteks sebelum mengedit.

## 3. Standar implementasi

- Utamakan solusi yang aman, maintainable, mudah diuji, dan sesuai versi dependency.
- Validasi input di boundary yang tepat.
- Tangani error secara eksplisit; jangan menyembunyikan error dengan fallback yang menyesatkan.
- Hindari duplikasi logic dan abstraksi berlebihan.
- Jaga backward compatibility kecuali requirement menyatakan sebaliknya.
- Jangan menonaktifkan security checks hanya agar implementasi terlihat berhasil.
- Ikuti pola penamaan dan struktur yang sudah ada.

## 4. Dependency dan dokumentasi

- Periksa dependency dan versinya sebelum menggunakan API atau fitur tertentu.
- Untuk perilaku yang bergantung pada versi, utamakan dokumentasi resmi versi terkait.
- Jangan menambahkan dependency baru jika kemampuan yang ada sudah memadai.
- Jelaskan alasan dan dampak jika dependency baru memang diperlukan.

## 5. Database dan operasi berisiko

- Jangan menjalankan migration yang berpotensi menghapus atau mengubah data tanpa menjelaskan dampaknya dan meminta persetujuan.
- Jangan melakukan reset database, force push, penghapusan massal, deployment, atau perubahan production tanpa persetujuan eksplisit.
- Buat backup/rollback plan bila perubahan data memang disetujui dan diperlukan.

## 6. Kolaborasi multi-agent

- Anggap `PROJECT_CONTEXT.md` sebagai ringkasan status bersama, bukan pengganti pemeriksaan source code.
- Sebelum mengedit, periksa `git status` dan diff agar perubahan agent lain tidak tertimpa.
- Hindari mengedit file/area yang sama secara paralel.
- Jika pekerjaan dibagi, tetapkan file atau area tanggung jawab setiap agent.
- Jangan mengklaim pekerjaan agent lain sudah selesai tanpa memverifikasi hasilnya.
- Setelah tugas selesai, perbarui bagian status di `PROJECT_CONTEXT.md` jika status, keputusan, atau next task berubah.
- Jangan menimpa catatan yang ditulis agent lain tanpa memeriksa perubahan terbaru.

## 7. Format laporan setelah bekerja

Laporkan secara ringkas:
- **Hasil:** apa yang berubah.
- **File:** daftar file yang dibuat/diubah.
- **Alasan:** root cause atau alasan desain.
- **Verifikasi:** perintah test/lint/build yang dijalankan dan hasilnya.
- **Belum selesai:** risiko, keterbatasan, atau pekerjaan lanjutan.

Jika tidak menjalankan test, nyatakan dengan jelas dan jangan mengklaim implementasi sudah teruji.

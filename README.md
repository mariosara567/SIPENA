# My Asssesmen

My Asssesmen (Sistem Penilaian dan Ujian Nasional Akademik) adalah aplikasi ujian sekolah berbasis web dengan pendekatan online-first. Seluruh pengguna mengakses satu server aplikasi melalui internet, sementara data ujian dan jawaban disimpan langsung pada database utama.

## Stack

- Laravel 12
- PostgreSQL
- Tailwind CSS
- Livewire

## Role Pengguna

- Administrator: mengelola guru, siswa, kelas, mata pelajaran, sesi ujian, monitoring, dan laporan.
- Guru: mengelola bank soal, paket ujian, peserta, sesi, hasil ujian, dan rekap nilai.
- Siswa: login, memasukkan token, mengikuti ujian, dan melihat hasil jika diaktifkan.

## Fitur Inti

- Manajemen pengguna, kelas, mata pelajaran, guru, dan siswa.
- Bank soal pilihan ganda dengan opsi A sampai E, kunci jawaban, bobot, dan import Excel.
- Manajemen ujian dengan jadwal, durasi, token, peserta, dan jumlah soal.
- Pelaksanaan ujian dengan timer, validasi token, auto save, navigasi soal, dan auto submit.
- Penilaian otomatis, monitoring real time, laporan PDF/Excel, dan autosave jawaban ke server.

## Menjalankan Lokal

1. Salin konfigurasi dari `.env.example` ke `.env`.
2. Sesuaikan koneksi PostgreSQL pada `.env`.
3. Jalankan `composer install`.
4. Jalankan `php artisan key:generate`.
5. Jalankan `php artisan migrate --seed`.
6. Jalankan `npm install` lalu `npm run dev`.
7. Jalankan `php artisan serve`.
8. Jika menggunakan queue untuk proses asinkron, jalankan worker: `php artisan queue:work`.

Admin awal:

- Username: `admin`
- Password: `password`

Akun demo tambahan:

- Guru: `guru1` / `password`
- Siswa: `siswa1` / `password`

## MVP yang sudah bisa dipakai

- Role middleware untuk pemisahan akses administrator, guru, dan siswa.
- Guru/Admin: buat ujian, tambah soal, dan tetapkan peserta.
- Siswa: lihat daftar ujian, input token, kerjakan ujian dengan timer, autosave jawaban, submit.
- Penilaian otomatis saat submit atau saat waktu ujian habis.
- Admin: kelola kelas/mapel, kelola akun guru/siswa, reset password, generate akun siswa massal.
- Monitoring ujian real-time (online, sedang berjalan, selesai, progress per ujian).
- Laporan nilai dengan filter kelas/mapel dan export CSV (Excel-compatible).
- Audit log aktivitas penting (auth, master data, ujian, penilaian, dan aktivitas siswa).

## Endpoint modul utama

- `/admin/academic` - data kelas dan mata pelajaran
- `/admin/teachers` - manajemen akun guru
- `/admin/students` - manajemen akun siswa dan generate massal
- `/exams` - manajemen ujian dan bank soal
- `/my-exams` - halaman ujian siswa
- `/monitoring` - monitoring ujian
- `/reports` - laporan nilai


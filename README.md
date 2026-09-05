# SIPENA

SIPENA (Sistem Penilaian dan Ujian Nasional Akademik) adalah aplikasi ujian sekolah berbasis web dengan pendekatan offline first. Aplikasi berjalan di server lokal sekolah melalui LAN/WiFi sehingga siswa tetap dapat mengikuti ujian tanpa internet. Saat koneksi tersedia, hasil ujian dapat disinkronkan ke server pusat.

## Stack

- Laravel 12
- PostgreSQL
- Tailwind CSS
- Livewire

## Role Pengguna

- Administrator: mengelola guru, siswa, kelas, mata pelajaran, sesi ujian, monitoring, sinkronisasi, dan laporan.
- Guru: mengelola bank soal, paket ujian, peserta, sesi, hasil ujian, dan rekap nilai.
- Siswa: login, memasukkan token, mengikuti ujian, dan melihat hasil jika diaktifkan.

## Fitur Inti

- Manajemen pengguna, kelas, mata pelajaran, guru, dan siswa.
- Bank soal pilihan ganda dengan opsi A sampai E, kunci jawaban, bobot, dan import Excel.
- Manajemen ujian dengan jadwal, durasi, token, peserta, dan jumlah soal.
- Pelaksanaan ujian dengan timer, validasi token, auto save, navigasi soal, dan auto submit.
- Penilaian otomatis, monitoring real time, laporan PDF/Excel, dan sinkronisasi hasil.

## Menjalankan Lokal

1. Salin konfigurasi dari `.env.example` ke `.env`.
2. Sesuaikan koneksi PostgreSQL pada `.env`.
3. Jalankan `composer install`.
4. Jalankan `php artisan key:generate`.
5. Jalankan `php artisan migrate --seed`.
6. Jalankan `npm install` lalu `npm run dev`.
7. Jalankan `php artisan serve`.
8. Jalankan worker queue untuk sinkronisasi: `php artisan queue:work`.

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
- Sinkronisasi hasil ujian ke server pusat via queue job + retry + log sinkronisasi.
- Audit log aktivitas penting (auth, master data, ujian, sinkronisasi).

## Endpoint modul utama

- `/admin/academic` - data kelas dan mata pelajaran
- `/admin/teachers` - manajemen akun guru
- `/admin/students` - manajemen akun siswa dan generate massal
- `/exams` - manajemen ujian dan bank soal
- `/my-exams` - halaman ujian siswa
- `/monitoring` - monitoring ujian
- `/reports` - laporan nilai
- `/sync` - sinkronisasi data ke server pusat

## Konfigurasi sinkronisasi pusat

Tambahkan variabel berikut di `.env` server sekolah:

- `SIPENA_CENTRAL_BASE_URL=https://domain-server-pusat`
- `SIPENA_CENTRAL_TOKEN=token-rahasia`
- `SIPENA_SCHOOL_CODE=kode-sekolah`

Sinkronisasi manual:

- `php artisan sipena:sync-exam-results`

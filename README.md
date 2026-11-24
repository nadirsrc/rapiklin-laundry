# RapiKlin Laundry — Sistem Informasi Manajemen Laundry Berbasis Web

Aplikasi kasir dan manajemen operasional laundry berbasis **Laravel** yang dibangun sesuai spesifikasi ASAS KK RPL XII (Login Multi-Role Admin & Kasir, CRUD Layanan, Alur Transaksi Berelasi, dan Rekap Laporan Keuangan).

## Fitur Utama

1. **Autentikasi & Hak Akses Role (`Admin` & `Kasir`)**
   - **Admin**: Akses penuh ke Dashboard, CRUD Data Layanan, Transaksi Laundry, Data Pelanggan, Rekap Transaksi & Laporan Keuangan (Cetak & Export CSV), serta Manajemen Akun Pengguna.
   - **Kasir**: Fokus operasional harian — Dashboard, Lihat Daftar Layanan (tanpa akses ubah/hapus), Input Transaksi Baru (hitung tarif otomatis), Update Status (`Masuk` → `Proses` → `Selesai`), Pelunasan (`Belum Lunas` → `Lunas`), Cetak Nota, Data Pelanggan, dan Rekap Transaksi.
2. **CRUD Data Layanan** (`layanans`)
   - Tersedia paket awal: **Reguler** (Rp 7.000/kg, 3-4 hari), **Ekspress** (Rp 12.000/kg, 1-2 hari), dan **Superkilat** (Rp 25.000/kg, 5 jam selesai).
   - Layanan yang sudah terhubung dengan transaksi dilindungi agar integritas laporan historis tetap aman.
3. **Alur Transaksi & Perhitungan Otomatis** (`transaksis` ↔ `pelanggans` ↔ `layanans` ↔ `users`)
   - Input data pelanggan (terhubung otomatis via nomor HP), pilih layanan, masukkan berat cucian (`kg`), hitung total harga otomatis di UI & diverifikasi ulang di server.
   - Update progres pengerjaan (`Masuk` → `Proses` → `Selesai`), konfirmasi pembayaran (`Belum Lunas` / `Lunas`), dan halaman **Cetak Nota**.
4. **Rekap Transaksi & Laporan Keuangan**
   - Filter periode tanggal, jenis layanan, dan status pengerjaan.
   - Rangkuman nilai transaksi per relasi jenis layanan, mode cetak laporan rapi, serta unduh **CSV**.
5. **Halaman Publik Cek Status Cucian** (`/cek-cucian`)
   - Memungkinkan pelanggan mengecek progres cucian menggunakan kode nota atau nomor HP tanpa harus login.

## Akun Pengujian (Seeder Bawaan)

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@rapiklin.test` | `password` |
| **Kasir** | `kasir@rapiklin.test` | `password` |

## Cara Menjalankan Aplikasi

1. Masuk ke folder proyek:
   ```powershell
   cd C:\Users\ACER\Downloads\sistem-laundry
   ```
2. Jalankan server Laravel (database SQLite sudah termigrasi & terisi seeder):
   ```powershell
   php artisan serve
   ```
3. Buka browser di `http://127.0.0.1:8000`.

### Catatan Opsional: Jika Ingin Menggunakan MySQL (Laragon / XAMPP)

1. Nyalakan service **MySQL** di Laragon atau XAMPP, lalu buat database kosong bernama `sistem_laundry`.
2. Ubah konfigurasi di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sistem_laundry
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan ulang migrasi dan seeder:
   ```powershell
   php artisan migrate:fresh --seed
   ```

## Pengujian Otomatis

Jalankan seluruh pengujian fitur (autentikasi, proteksi role Admin vs Kasir, CRUD layanan, alur transaksi, laporan relasional, dan cek cucian) dengan perintah:

```powershell
php artisan test
```

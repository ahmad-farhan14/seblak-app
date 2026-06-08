Markdown
# 🍜 Seblak App - Sistem Pemesanan Mandiri & Kasir

Sistem informasi pemesanan menu makanan (Seblak) dan minuman berbasis web menggunakan framework **Laravel 10** dan **Alpine.js**, serta telah terintegrasi dengan payment gateway **Midtrans QRIS** untuk proses pembayaran otomatis.

---

## 👨‍🏫 Cara Cepat untuk Dosen Penguji (Quick Run)

Jika Bapak/Ibu Dosen Penguji ingin memeriksa dan menjalankan aplikasi ini secara instan di komputer yang sudah terinstal Laragon/XAMPP & Composer, silakan ikuti 3 langkah mudah berikut:

1. **Download / Clone** repository ini dan letakkan folder `seblak-app` di dalam direktori server local Anda (misal: `C:\laragon\www\` atau `C:\xampp\htdocs\`).
2. Buat database baru yang masih kosong bernama **`seblak_db`** melalui phpMyAdmin atau HeidiSQL.
3. Jalankan file otomatisasi instalasi dengan melakukan *double-click* pada file **`install-demo.bat`** yang telah disediakan di dalam folder proyek ini.

*Script tersebut akan otomatis menginstal vendor library yang dibutuhkan, membuat file konfigurasi `.env` default, melakukan generate key keamanan, serta mengisi data menu awal (seeding) ke database secara otomatis. Aplikasi siap diakses di browser melalui URL `http://localhost/seblak-app/public` atau `http://seblak-app.test`.*

---

## 🚀 Panduan Instalasi Manual (Langkah demi Langkah)

Jika Anda ingin melakukan proses instalasi dan konfigurasi sistem secara manual, silakan ikuti langkah-langkah di bawah ini:

### 1. Masuk ke Folder Proyek
Buka terminal (Cmder / Git Bash) di dalam direktori proyek, lalu jalankan:
```bash
cd seblak-app
2. Jalankan Composer Install
Karena folder vendor di-ignore oleh Git, Anda wajib mengunduh kembali seluruh dependencies library PHP (termasuk SDK Midtrans) dengan perintah:

Bash
composer install
3. Konfigurasi Environment (.env)
Salin file .env.example yang sudah ada menjadi file .env:

Bash
cp .env.example .env
Buka file .env tersebut menggunakan teks editor, kemudian sesuaikan nama database serta masukkan Midtrans Server Key Sandbox Anda:

Cuplikan kode
DB_DATABASE=seblak_db

MIDTRANS_SERVER_KEY=Mid-server-YOUR_SANDBOX_SERVER_KEY
MIDTRANS_CLIENT_KEY=Mid-client-YOUR_SANDBOX_CLIENT_KEY
MIDTRANS_IS_PRODUCTION=false
4. Generate Application Key & Jalankan Migrasi Database
Buat key baru untuk enkripsi Laravel dan jalankan migration beserta seeder bawaan untuk mengisi data menu awal ke database:

Bash
php artisan key:generate
php artisan migrate --seed
5. Bersihkan Cache Aplikasi
Pastikan seluruh konfigurasi lama dibersihkan agar Laravel membaca konfigurasi yang baru diperbarui:

Bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
🛠️ Fitur Utama Aplikasi
Pemesanan Mandiri Pelanggan (Customer Front-End):

Pemilihan tipe order: Dine-In (Makan di sini dengan nomor meja) atau Take Away (Bawa pulang).

Kustomisasi menu seblak: Pilihan jenis kuah (pedas gurih/pedas manis), tingkat kepedasan (level 0-5), dan validasi wajib memilih minimal 3 unit topping tambahan.

Kustomisasi menu minuman: Deteksi varian rasa secara dinamis berdasarkan jenis produk (Pop Ice, Nutrisari, Good Day) serta pilihan suhu (Ice/Hot).

Integrasi Gerbang Pembayaran (Payment Gateway):

Pembayaran otomatis menggunakan Midtrans Snap API (QRIS, Gopay, ShopeePay) pada mode pembayaran non-tunai.

Penanganan status transaksi aman yang langsung terhubung ke database.

Struk Nota Digital:

Halaman sukses yang didesain menyerupai tampilan nota kasir fisik, menampilkan informasi personal berupa Nama Pelanggan, Nomor Meja, Rincian Menu beserta kustomisasinya, dan Total Tagihan akhir.

Manajemen Dapur & Kasir (Back-End Admin):

Pembaruan status antrean pengerjaan makanan secara berkala dari panel admin.
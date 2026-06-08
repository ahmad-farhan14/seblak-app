# Panduan Setup & Menjalankan Aplikasi Seblak

Dokumentasi lengkap untuk menjalankan aplikasi Seblak UAS.

## ✅ Status Setup

- [x] **Fix PHP zip extension** - Sudah diaktifkan di php.ini
- [x] **Composer install** - Sudah mendownload semua dependencies
- [ ] **Copy .env file** - Jalankan: `Copy-Item .env.example .env`
- [ ] **Generate app key** - Jalankan: `php artisan key:generate`
- [ ] **Database setup** - Jalankan: `php artisan migrate:fresh --seed`
- [ ] **NPM install** - Jalankan: `npm install`

## 🚀 Cara Menjalankan Web

Buka **2 Terminal** secara bersamaan:

### Terminal 1: Laravel Backend Server
```powershell
cd C:\laragon\www\seblak-app
php artisan serve
```
✅ Server akan jalan di: **http://localhost:8000**

### Terminal 2: Vite Frontend Dev Server
```powershell
cd C:\laragon\www\seblak-app
npm run dev
```
✅ Vite akan jalan di: **http://localhost:5173** (biasanya)

## 📋 Langkah Setup Lengkap

Jika belum pernah setup sama sekali:

```powershell
# 1. Masuk ke folder project
cd C:\laragon\www\seblak-app

# 2. Copy environment file
Copy-Item .env.example .env

# 3. Generate app key (hanya 1x di awal)
php artisan key:generate

# 4. Setup database (fresh & seed)
php artisan migrate:fresh --seed

# 5. Install npm dependencies
npm install

# 6. Jalankan di 2 terminal (seperti di atas)
```

## 🗄️ Konfigurasi Database

Edit file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seblak_app
DB_USERNAME=root
DB_PASSWORD=
```

> Di Laragon, pastikan MySQL sudah running di Laragon Dashboard

## 🔧 Command Berguna

```powershell
# List semua routes
php artisan route:list

# Fresh migrations (reset database)
php artisan migrate:fresh

# Seed database dengan sample data
php artisan db:seed

# Generate dummy data
php artisan tinker
# Lalu di dalam tinker:
# >>> App\Models\Menu::factory(10)->create();

# Clear cache
php artisan cache:clear

# View logs
php artisan logs
```

## 🎯 Akses Aplikasi

- **Frontend/Customer**: http://localhost:8000
- **Admin Panel** (jika ada): http://localhost:8000/admin
- **API** (jika ada): http://localhost:8000/api

## ❌ Troubleshooting

**Error: "The zip extension is missing"**
- ✅ Sudah diperbaiki! Zip extension sudah diaktifkan di php.ini

**Error: "php command not found"**
- Gunakan PowerShell baru atau jalankan: `php artisan serve` dari folder project

**Database connection error**
- Pastikan MySQL running di Laragon Dashboard
- Check file `.env` konfigurasi DB

**Port 8000 sudah terpakai**
- Gunakan port lain: `php artisan serve --port=8001`

**Npm dependencies error**
- Clear cache: `npm cache clean --force` 
- Hapus node_modules & package-lock.json, install ulang

---

**Good Luck untuk UAS! 🚀**

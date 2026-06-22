# 🏫 Bait Tahfiz Al-Qur'an Ridhallah (BTQR)

Petunjuk pemasangan dan penyiapan proyek BTQR (Website Profil & LMS Bahasa Arab) di laptop atau komputer lain setelah melakukan `git clone`.

---

## 🛠️ Prasyarat (Prerequisites)
Pastikan laptop tujuan sudah terpasang:
- **PHP** >= 8.2 (misalnya menggunakan XAMPP atau Laragon)
- **Composer** (untuk dependensi PHP/Laravel)
- **Node.js** >= 18 & **NPM** (untuk dependensi JS/Vue.js)
- **MySQL / MariaDB** (melalui XAMPP/Laragon)

---

## 🚀 Langkah-Langkah Penyiapan (Setup)

### 1. Clone Repositori
Clone proyek ini ke laptop tujuan:
```bash
git clone <url-repositori-anda>
cd BTQR
```

### 2. Konfigurasi Backend (Laravel REST API)
Pindah ke direktori `backend`, instal dependensi, dan atur environment:
```bash
cd backend

# Instal dependensi composer
composer install

# Salin file konfigurasi environment
cp .env.example .env

# Generate Application Key
php artisan key:generate
```

*   **Penting**: Buka file `.env` di editor Anda dan sesuaikan konfigurasi database berikut dengan server database lokal Anda:
    ```env
    DB_CONNECTION=sqlite
    # DB_HOST=127.0.0.1
    # DB_PORT=3306
    # DB_DATABASE=laravel
    # DB_USERNAME=root
    # DB_PASSWORD=
    ```
*   Buat database baru bernama `btqr_lms` di phpMyAdmin atau DBMS lokal Anda.
*   Jalankan migrasi database beserta data awal (seeder):
    ```bash
    php artisan migrate --seed
    ```
*   Hubungkan folder storage Laravel agar dapat diakses publik:
    ```bash
    php artisan storage:link
    ```
*   Jalankan server API lokal backend:
    ```bash
    php artisan serve
    ```
    *Backend akan berjalan di: `http://localhost:8000/`*

---

### 3. Konfigurasi Frontend (Vue.js SPA)
Buka terminal baru, pindah ke direktori `frontend`, instal dependensi, dan atur environment:
```bash
cd/.. (jika masih di backend)
cd frontend

# Instal dependensi npm
npm install

# Salin file konfigurasi environment
cp .env.example .env
```
*   Buka file `.env` di frontend dan pastikan URL backend API sudah sesuai:
    ```env
    VITE_API_URL=http://localhost:8000
    ```
*   Jalankan server lokal frontend:
    ```bash
    npm run dev
    ```
    *Aplikasi frontend Vue akan berjalan di: `http://localhost:5173/`*

---



# 🏷️ Online Auction System

## Ujian Akhir Semester (UAS)

### Pemrograman Berbasis Web

Sistem Lelang Online berbasis **Laravel 12**, **Vue 3**, **MySQL**, **Laravel Sanctum**, dan **Laravel Reverb** yang mendukung proses lelang secara realtime, pengelolaan barang lelang, serta penentuan pemenang otomatis.

---

# 👨‍💻 Kelompok 5

| Nama                      | NIM        |
| ------------------------- | ---------- |
| Ilham Maulana Kusuma      | 2401010004 |
| I Putu Yoga Dhandi Wijaya | 2401010014 |
| I Gede Sumeryasa          | 2401010033 |

---

# 📖 Deskripsi Sistem

Online Auction System merupakan aplikasi berbasis web yang memungkinkan pengguna untuk membuat lelang, mengikuti lelang, melakukan penawaran harga (bidding), serta menerima pembaruan data secara realtime menggunakan Laravel Reverb.

Sistem dirancang untuk memberikan pengalaman lelang yang interaktif dengan fitur notifikasi realtime, validasi penawaran, dan penentuan pemenang secara otomatis saat waktu lelang berakhir.

---

# ✨ Fitur Utama

## Authentication

* Register
* Login
* Logout
* Laravel Sanctum Authentication

## Auction Management

* Create Auction
* Read Auction
* Update Auction
* Delete Auction
* My Auctions
* Upload Image Barang

## Bidding System

* Realtime Bid
* Bid Validation
* Minimum Increment Bid
* Bid History
* Prevent Self Bidding
* Automatic Winner Selection

## Realtime Features

* Laravel Reverb
* Laravel Echo
* Realtime Bid Update
* Outbid Notification
* Realtime Auction Result

## Auction Lifecycle

* Scheduled Auction
* Active Auction
* Ended Auction
* Countdown Timer
* Automatic Auction Closing

---

# 🛠️ Teknologi yang Digunakan

## Backend

* Laravel 12
* Laravel Sanctum
* Laravel Reverb
* MySQL
* Eloquent ORM

## Frontend

* Vue 3
* Vue Router
* Pinia
* Axios
* Laravel Echo
* Pusher JS

---

# 📋 Prasyarat

Pastikan perangkat telah terinstall:

* PHP 8.2 atau lebih baru
* Composer
* Node.js 18 atau lebih baru
* NPM
* MySQL
* Git

---

# 🚀 Instalasi Proyek

## 1. Clone Repository

```bash
git clone <repository-url>
cd Project_UAS_PBW
```

---

## 2. Instalasi Backend

Masuk ke folder backend:

```bash
cd lelang
```

Install dependency Laravel:

```bash
composer install
```

Copy file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

---

## 3. Instalasi Frontend

Masuk ke folder frontend:

```bash
cd ../frontend
```

Install dependency:

```bash
npm install
```

---

# ⚙️ Konfigurasi Database

Buat database MySQL:

```sql
CREATE DATABASE auction_db;
```

Edit file:

```text
lelang/.env
```

Sesuaikan konfigurasi berikut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=auction_db
DB_USERNAME=root
DB_PASSWORD=
```

---

# ⚡ Konfigurasi Laravel Reverb

Edit file:

```text
lelang/.env
```

Tambahkan konfigurasi berikut:

```env
BROADCAST_CONNECTION=reverb

QUEUE_CONNECTION=database

REVERB_APP_ID=local
REVERB_APP_KEY=auction-key
REVERB_APP_SECRET=auction-secret

REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

# 🗄️ Migrasi Database

Menjalankan migrasi:

```bash
php artisan migrate
```

Jika menggunakan seeder:

```bash
php artisan migrate:fresh --seed
```

---


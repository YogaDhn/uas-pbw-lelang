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
▶️ Menjalankan Aplikasi
Backend Server
cd lelang
php artisan serve

Aplikasi backend berjalan pada:

http://127.0.0.1:8000
Frontend Server
cd frontend
npm run dev

Aplikasi frontend berjalan pada:

http://localhost:5173
Queue Worker

Digunakan untuk memproses job dan event queue.

php artisan queue:work
Scheduler

Digunakan untuk memperbarui status lelang dan menutup lelang secara otomatis.

php artisan schedule:work
Laravel Reverb

Digunakan untuk komunikasi realtime.

php artisan reverb:start

🔑 Akun Demo
Penjual
Email	Password
yoga@gmail.com	password
Penawar
Email	Password
stella@gmail.com	password

🗃️ Entity Relationship Diagram (ERD)
Tabel Users
Field	Tipe
id	bigint
name	varchar
email	varchar
password	varchar
created_at	timestamp
updated_at	timestamp
Tabel Auctions
Field	Tipe
id	bigint
user_id	bigint
title	varchar
description	text
image	varchar
starting_price	decimal
bid_increment	decimal
start_time	datetime
end_time	datetime
status	varchar
created_at	timestamp
updated_at	timestamp
Tabel Bids
Field	Tipe
id	bigint
auction_id	bigint
user_id	bigint
amount	decimal
is_winner	boolean
created_at	timestamp
updated_at	timestamp
Relasi Antar Tabel
USERS
│
├──< AUCTIONS
│
└──< BIDS

AUCTIONS
│
└──< BIDS
ERD Diagram
+-------------------+
|       USERS       |
+-------------------+
| id (PK)           |
| name              |
| email             |
| password          |
+-------------------+
          |
          | 1
          |
          | N
+-------------------+
|     AUCTIONS      |
+-------------------+
| id (PK)           |
| user_id (FK)      |
| title             |
| description       |
| image             |
| starting_price    |
| bid_increment     |
| start_time        |
| end_time          |
| status            |
+-------------------+
          |
          | 1
          |
          | N
+-------------------+
|       BIDS        |
+-------------------+
| id (PK)           |
| auction_id (FK)   |
| user_id (FK)      |
| amount            |
| is_winner         |
+-------------------+
          ^
          |
          |
        USERS
📂 Struktur Folder
Project_UAS_PBW
│
├── lelang
│   ├── app
│   ├── config
│   ├── database
│   ├── routes
│   ├── storage
│   └── public
│
└── frontend
    ├── src
    │   ├── views
    │   ├── router
    │   ├── stores
    │   ├── services
    │   └── assets
    │
    └── public
    
🧪 Skenario Pengujian
Login sebagai Penjual.
Membuat barang lelang baru.
Login sebagai Penawar pada browser berbeda.
Membuka halaman detail lelang yang sama.
Melakukan bid.
Bid tertinggi diperbarui secara realtime.
Sistem mengirim notifikasi ketika bid dikalahkan.
Setelah waktu lelang berakhir, sistem menentukan pemenang secara otomatis.

📌 Catatan
Untuk menjalankan seluruh fitur realtime secara optimal, pastikan backend, frontend, queue worker, scheduler, dan Laravel Reverb berjalan secara bersamaan.

📄 Lisensi
Proyek ini dibuat untuk memenuhi tugas Ujian Akhir Semester (UAS) Mata Kuliah Pemrograman Berbasis Web.

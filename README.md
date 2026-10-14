# Projek Elektonik

## Modul yang Ada
- POS
- Inventory
- Finance

## Link Figma
https://www.figma.com/design/R1ZzYhjdQ3HmXtLIWQqQex/ERP-Elektronik?node-id=0-1&t=nhS7zQY5cQZjpvvn-1

# Elektronik

Aplikasi web pengelolaan elektronik yang dibangun menggunakan **Laravel 12**.

## Requirements

Pastikan perangkat sudah memiliki beberapa kebutuhan berikut:

- **PHP:** 8.3 atau lebih baru
- **Composer:** Versi terbaru yang kompatibel
- **Node.js:** Versi LTS terbaru yang kompatibel
- **NPM:** Terinstal bersama Node.js
- **Database:** MySQL atau MariaDB
- **Git:** Untuk meng-clone repository

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/LoeisDavid/elektronik.git
cd elektronik
```

### 2. Checkout Branch Dev

```bash
git fetch origin
git checkout dev
```

### 3. Install Dependencies PHP

```bash
composer install
```

### 4. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Untuk pengguna Windows CMD:

```cmd
copy .env.example .env
```

Buka file `.env`, lalu sesuaikan konfigurasi database dengan database lokal masing-masing.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elektronik
DB_USERNAME=root
DB_PASSWORD=
```

Pastikan database `elektronik` sudah dibuat sebelum melanjutkan.

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Install Dependencies Frontend

```bash
npm install
```

### 7. Build Frontend Assets

```bash
npm run build
```

### 8. Jalankan Migration dan Seeder

Jalankan perintah berikut untuk membuat tabel sekaligus mengisi database dengan dummy data:

```bash
php artisan migrate --seed
```

### 9. Jalankan Aplikasi

```bash
php artisan serve
```

Buka aplikasi melalui browser:

http://127.0.0.1:8000

## Login Account

Gunakan akun berikut untuk login:

| Informasi | Detail |
|---|---|
| Username | `admin` |
| Password | `admin123` |

**Catatan:** Akun login harus sudah dibuat oleh seeder agar dapat digunakan.

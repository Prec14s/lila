# ☕ Sistem Warkop Samalila (Laravel 12 + MySQL)

Sistem pemesanan & pembayaran digital berbasis QR Code untuk Warkop Samalila, dengan 4 role: **Super Admin**, **Owner**, **Dapur**, dan **User/Pelanggan** (tanpa login).

Fitur pembayaran mendukung **Tunai (bayar di kasir)** dan **Non-Tunai (QRIS / Transfer Bank)**, lengkap dengan pembeda visual & alur verifikasi yang berbeda untuk masing-masing metode.

---

## 🧩 Cara Instalasi

Karena proyek ini dikembangkan secara offline (tanpa akses ke Packagist saat dibuat), ikuti langkah berikut agar dependency Laravel terpasang dengan benar:

### 1. Buat skeleton Laravel 12 baru
```bash
composer create-project laravel/laravel warkop-samalila-app "12.*"
cd warkop-samalila-app
```

### 2. Timpa (overwrite) folder berikut dengan isi paket ini
Salin & timpa folder-folder berikut dari paket `warkop-samalila/` ke dalam project hasil `create-project` di atas:
```
app/            → timpa seluruhnya
database/       → timpa seluruhnya
resources/views → timpa seluruhnya
routes/web.php  → timpa
routes/console.php → timpa
bootstrap/app.php → timpa
bootstrap/providers.php → timpa
```
Jangan menimpa folder `vendor/`, `public/index.php`, `artisan`, atau `config/` bawaan `create-project` — biarkan tetap yang asli dari Laravel.

### 3. Konfigurasi environment
```bash
cp .env.example .env      # gunakan .env.example dari paket ini sebagai acuan, sesuaikan ke .env project
php artisan key:generate
```
Edit `.env`, sesuaikan koneksi database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warkop_samalila
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat database & migrasi
```bash
mysql -u root -p -e "CREATE DATABASE warkop_samalila"
php artisan migrate --seed
```

### 5. Buat symbolic link storage (untuk foto menu, QRIS, bukti bayar)
```bash
php artisan storage:link
```

### 6. Jalankan server
```bash
php artisan serve
```
Buka `http://localhost:8000`

> **Catatan tampilan:** Sistem ini memakai **Tailwind CSS via CDN + Alpine.js via CDN**, jadi **tidak perlu** menjalankan `npm install` / `npm run build` — animasi dan styling langsung aktif begitu server dijalankan.

---

## 🔑 Akun Default (hasil seeder)

| Role | Email | Password | URL Login |
|---|---|---|---|
| Super Admin | superadmin@warkopsamalila.test | password | `/login/superadmin` |
| Owner | owner@warkopsamalila.test | password | `/login/owner` |
| Dapur | dapur@warkopsamalila.test | password | `/login/dapur` |

**Segera ganti password default ini setelah instalasi**, terutama sebelum digunakan di lingkungan produksi.

Halaman pelanggan (tanpa login, hasil scan barcode): `/pesan`

---

## 💵 vs 📲 Pembeda Pembayaran Tunai & Non-Tunai

| Aspek | 💵 Tunai | 📲 Non-Tunai (QRIS/Transfer) |
|---|---|---|
| Dikonfigurasi di | Owner → Metode Pembayaran → tipe "Tunai" | Owner → Metode Pembayaran → tipe "QRIS"/"Transfer" |
| Upload bukti bayar | **Tidak wajib** — pelanggan cukup konfirmasi | **Wajib** — pelanggan unggah foto bukti transfer/QRIS |
| Badge di semua tampilan | 🟠 Oranye "Tunai" | 🟣 Indigo "Non-Tunai" |
| Tombol verifikasi Owner | "💵 Tunai Diterima & ACC" | "✅ ACC Pembayaran" (menampilkan foto bukti) |
| Teks WA ke Owner | "Metode: Bayar TUNAI di kasir..." | "Bukti pembayaran sudah saya unggah..." |
| Struk cetak | Menampilkan label "💵 Tunai — Tunai di Kasir" | Menampilkan label "📲 Non-Tunai — QRIS/Transfer Bank" |

Owner bisa mengaktifkan/menonaktifkan opsi Tunai kapan saja lewat menu **Metode Pembayaran**, tanpa mengubah kode.

---

## 🗂️ Struktur Alur Sistem

1. Pelanggan scan barcode meja → `/pesan`
2. Pilih menu (Makanan/Minuman/Snack) → isi nama & WA → checkout
3. Pilih cara bayar: **Tunai** atau **Non-Tunai** (QRIS/Transfer)
   - Non-Tunai → upload bukti bayar
   - Tunai → langsung konfirmasi, bayar saat ambil pesanan
4. Sistem generate Nomor Order (format `WS-YYYYMMDD-XXXX`) & buka link WhatsApp ke Owner
5. Owner verifikasi (ACC/Tolak) di menu **Verifikasi Pembayaran**
6. Setelah ACC → pesanan tampil di dashboard Dapur & bisa diteruskan via WA Dapur
7. Dapur proses pesanan → tandai selesai
8. Owner cetak struk & bisa cek status order kapan saja lewat Nomor Order

---

## 📁 Struktur Folder Penting

```
app/Http/Controllers/Customer/   → alur pelanggan (menu, checkout, bayar, status)
app/Http/Controllers/Auth/       → login terpisah superadmin/owner/dapur
app/Http/Controllers/Owner/      → CRUD menu, kategori, metode bayar, verifikasi, struk
app/Http/Controllers/Dapur/      → daftar & update status pesanan dapur
app/Http/Controllers/SuperAdmin/ → kelola akun owner/dapur
app/Models/                      → User, Category, Menu, Order, OrderItem, PaymentSetting, BusinessSetting
database/migrations/             → skema database lengkap
database/seeders/                → akun default + data contoh
resources/views/                 → semua tampilan Blade (Tailwind CDN + Alpine.js + animasi ringan)
routes/web.php                   → seluruh routing sistem
```

---

## ⚙️ Kebutuhan Server
- PHP >= 8.2
- MySQL >= 5.7 / MariaDB >= 10.3
- Ekstensi PHP: `pdo_mysql`, `mbstring`, `gd` atau `imagick` (untuk resize foto jika diperlukan ke depannya)
- Composer 2.x

---

## 🚀 Pengembangan Lanjutan (opsional)
- Integrasi WhatsApp Business API resmi (saat ini pakai tautan `wa.me` klik-kirim)
- Integrasi payment gateway otomatis untuk verifikasi QRIS real-time
- Laporan omzet harian/bulanan (tunai vs non-tunai) dalam bentuk grafik

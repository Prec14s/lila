# ☕ Sistem Pemesanan & Pembayaran Digital — Warkop Samalila

Sistem pemesanan dan pembayaran digital berbasis QR Code untuk **Warkop Samalila**, dibangun menggunakan kerangka kerja **Laravel 12** dan **MySQL**. Sistem ini dirancang untuk mempercepat alur transaksi dari meja pelanggan, mempermudah verifikasi pembayaran oleh Owner, hingga pengelolaan antrean masak di Dapur secara realtime.

---

## 🎯 Gambaran Umum Sistem

Sistem ini menghilangkan antrean manual di kasir dengan memungkinkan pelanggan memesan langsung dari meja masing-masing melalui scan QR Code. Menggunakan arsitektur multi-role dengan hak akses yang terisolasi secara aman:

1. **📱 Pelanggan (Customer / User / Scan QR)**:
   - Tanpa perlu register/login.
   - Mengakses menu digital melalui scan QR Code meja.
   - Memilih menu, mengisi nomor meja & nama, serta memilih metode pembayaran (**Tunai** atau **Non-Tunai**).
   - Melacak status pesanan secara *realtime*, menyalin nomor order, serta mengunduh Struk Digital (PDF) setelah pembayaran disetujui.

2. **👑 Owner**:
   - Manajerial penuh terhadap operasional warkop.
   - Manajemen katalog menu (tambah, edit, hapus, upload foto, status stok/ketersediaan).
   - Pengaturan kategori menu dan metode pembayaran (QRIS, Transfer Bank, Tunai di Kasir).
   - Verifikasi Pembayaran (ACC/Tolak pesanan disertai alasan penolakan).
   - Monitoring riwayat pesanan, laporan omzet harian/bulanan, cetak struk fisik/PDF, dan ekspor laporan.

3. **🍳 Dapur (Kitchen Staff)**:
   - Antarmuka khusus tim dapur dengan fitur *Auto-Update / Realtime Refresh*.
   - Menampilkan urutan antrean pesanan yang sudah diverifikasi (ACC) oleh Owner.
   - Fitur pencatatan durasi pengerjaan (*timer realtime* dari tombol "Mulai Proses" hingga "Tandai Selesai").
   - Laporan riwayat durasi pengerjaan dan statistik memasak rata-rata/tercepat/terlama.

4. **⚡ Super Admin**:
   - Pengelola tingkat tinggi untuk manajemen akun pengguna (Owner & Dapur).
   - Monitoring seluruh transaksi sistem secara umum.
   - Fitur Audit Log / Log Aktivitas untuk mencatat tindakan krusial pengguna.

---

## 💡 Fitur Utama

- **Pemesanan QR Code Meja**: Pelanggan memilih menu dan meja secara langsung dari perangkat smartphone mereka.
- **Dukungan Pembayaran Fleksibel**:
  - **📲 Non-Tunai (QRIS & Transfer Bank)**: Pelanggan mengunggah foto bukti pembayaran untuk diverifikasi Owner.
  - **💵 Tunai (Bayar di Kasir)**: Pelanggan mengonfirmasi pesanan tanpa unggah bukti, lalu bayar langsung saat mengambil pesanan.
- **Tombol Salin No. Order**: Memudahkan pelanggan menyalin nomor transaksi untuk pelacakan status.
- **Verifikasi Instant & Notifikasi WA**: Terhubung dengan tautan WhatsApp Owner untuk konfirmasi cepat pesanan.
- **Struk Digital & Cetak PDF**: Generasi otomatis struk belanja berformat PDF dengan tampilan struk fisik thermal.
- **Manajemen Antrean Dapur & Timer Realtime**: Dapur dapat memantau estimasi waktu masak tiap pesanan.
- **Keamanan Multi-Guard**: Sistem otentikasi terpisah untuk masing-masing peran pengguna.
- **Antarmuka Responsive**: Desain modern berbasis Tailwind CSS dan Alpine.js yang responsif di perangkat desktop maupun HP.

---

## 💵 vs 📲 Skema Pembayaran

| Fitur / Aspek | 💵 Pembayaran Tunai | 📲 Pembayaran Non-Tunai (QRIS / Transfer) |
|---|---|---|
| **Verifikasi Bukti** | Tidak perlu unggah foto bukti transfer | Wajib unggah foto screenshot/bukti bayar |
| **Pembeda Visual** | Badge Oranye "Tunai" | Badge Indigo "Non-Tunai" |
| **Aksi Verifikasi Owner** | Tombol "💵 Tunai Diterima & ACC" | Tombol "✅ ACC Pembayaran" (disertai pratinjau foto) |
| **Keterangan Struk** | `💵 Tunai — Bayar di Kasir` | `📲 Non-Tunai — QRIS / Transfer Bank` |

---

## 🗂️ Alur Kerja Sistem (Workflow)

```
[Pelanggan Scan QR] ➔ [Pilih Menu & Isi Meja] ➔ [Pilih Cara Bayar (Tunai/Non-Tunai)]
                                                         │
                                                         ▼
[Sistem Generate No. Order (WS-YYYYMMDD-XXXX)] ➔ [Kirim Konfirmasi via WhatsApp]
                                                         │
                                                         ▼
                                          [Owner Verifikasi (ACC / Tolak)]
                                                         │
                                                         ▼
                                        [Dapur Terima Order & Mulai Masak]
                                                         │
                                                         ▼
                                       [Pesanan Selesai ➔ Struk PDF Aktif]
```

---

## 🔑 Hak Akses Pengujian (Demo)

| Role / Akses | Email | Akses URL |
|---|---|---|
| **Super Admin** | `superadmin@warkopsamalila.test` | `/login/superadmin` |
| **Owner** | `owner@warkopsamalila.test` | `/login/owner` |
| **Dapur** | `dapur@warkopsamalila.test` | `/login/dapur` |
| **Pelanggan** | *(Tanpa Login)* | `/pesan` |

---

## 📁 Arsitektur Kode & Modul

- `app/Http/Controllers/Customer/`: Logika pemesanan, pembacaan menu, upload bukti, dan pelacakan status order pelanggan.
- `app/Http/Controllers/Owner/`: Modul pengelolaan menu, kategori, verifikasi pembayaran, laporan omzet, dan cetak struk.
- `app/Http/Controllers/Dapur/`: Manajemen antrean dapur realtime dan penghitungan durasi pengerjaan.
- `app/Http/Controllers/SuperAdmin/`: Manajemen akun pengguna dan pemantauan aktivitas.
- `resources/views/`: Komponen antarmuka Blade berbasis Tailwind CSS dan Alpine.js.

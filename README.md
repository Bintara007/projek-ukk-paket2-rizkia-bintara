# 🅿️ Aplikasi Pengelolaan Parkir (UKK Paket 2)

Repositori ini berisi source code untuk **Aplikasi Pengelolaan Parkir** yang dikembangkan sebagai Ujian Kompetensi Keahlian (UKK) Paket 2 oleh **Rizkia Bintara**. Aplikasi berbasis web ini dirancang untuk memudahkan manajemen area parkir, pengelolaan tarif, pencatatan transaksi kendaraan masuk/keluar, serta manajemen data member dan pengguna.

---

## 🌐 Live Demo & Video Demo
* 🔗 **Tautan Live Demo:** [Klik di sini untuk mencoba aplikasi](projeck-ukk-rizkia.site.je)

---

## 🔑 Akun Login (Credentials)
Untuk menguji fitur aplikasi berdasarkan hak akses (*multi-role*), Anda dapat menggunakan akun di bawah ini:

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `admin123` |
| **Petugas** | `p1` | `p1` |
| **Owner** | `owner` | `owner123` |

---

## 👥 Hak Akses & Fitur Role
1. **Admin**: Mengelola data pengguna, data area parkir, tarif, serta memantau keseluruhan sistem.
2. **Petugas**: Mengelola transaksi kendaraan masuk dan keluar, serta pencatatan data member.
3. **Owner**: Melihat laporan dan dashboard khusus pemilik terkait aktivitas parkir.

---

## 📂 Struktur Direktori Project
Berikut adalah struktur folder dan file utama dari project ini:

```text
projek-ukk-paket2-rizkia-bintara-main/
│
├── area/                      # Manajemen Area Parkir
│   ├── edit_area.php
│   ├── hapus_area.php
│   ├── simpan_area.php
│   ├── tambah_area.php
│   └── tampil_area_parkir.php
│
├── assets/                    # Aset Pendukung (CSS & Gambar)
│   ├── css/
│   │   ├── dashboard.css
│   │   ├── edit.css
│   │   ├── form.css
│   │   ├── login.css
│   │   ├── logout.css
│   │   └── tabel.css
│   └── img/
│       └── logo-persib.png
│
├── log_aktivitas/             # Pencatatan Log Aktivitas Sistem
│   ├── edit_log.php
│   ├── hapus_log.php
│   ├── simpan_log.php
│   ├── tambah_log.php
│   └── tampil_log.php
│
├── member/                    # Manajemen Member Parkir
│   ├── edit_member.php
│   ├── hapus_member.php
│   ├── simpan_member.php
│   ├── tambah_member.php
│   └── tampil_member.php
│
├── parkir/                    # Manajemen Transaksi Parkir Kendaraan
│   ├── edit_parkir.php
│   ├── hapus_parkir.php
│   ├── proses_keluar.php
│   ├── simpan_parkir.php
│   ├── tambah_parkir.php
│   └── tampil_parkir.php
│
├── tarif/                     # Manajemen Tarif Parkir
│   ├── edit_tarif.php
│   ├── hapus_tarif.php
│   ├── simpan_tarif.php
│   ├── tambah_tarif.php
│   └── tampil_tarif.php
│
├── transaksi/                 # Laporan & Histori Transaksi
│   ├── cetak_histori.php
│   ├── edit_transaksi.php
│   ├── hapus_transaksi.php
│   ├── simpan_transaksi.php
│   ├── tambah_transaksi.php
│   └── tampil_transaksi.php
│
├── user/                      # Manajemen Pengguna / Petugas
│   ├── edit_user.php
│   ├── hapus_user.php
│   ├── simpan_user.php
│   ├── tambah_user.php
│   └── tampil_user.php
│
├── dashboard_admin.php        # Dashboard khusus Admin
├── dashboard_owner.php        # Dashboard khusus Owner
├── dashboard_petugas.php      # Dashboard khusus Petugas
├── index.php                  # Halaman Utama / Login
├── koneksi.php                # Konfigurasi Koneksi Database
├── logout.php                 # Proses Logout
├── parkir.sql                 # File Database Utama
└── if0_43208612_parkir.sql    # Backup Database (InfinityFree / Hosting)

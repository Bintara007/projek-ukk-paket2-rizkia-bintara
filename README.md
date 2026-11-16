# 🅿️ Sistem Manajemen Parkir

### UKK Rekayasa Perangkat Lunak — Paket 2

<p align="center">
    <img src="https://img.shields.io/badge/PROJECT-UKK%20PAKET%202-blue?style=for-the-badge" alt="UKK Paket 2">
    <img src="https://img.shields.io/badge/BACKEND-PHP%20NATIVE-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Native">
    <img src="https://img.shields.io/badge/DATABASE-MYSQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/STATUS-COMPLETED-success?style=for-the-badge" alt="Completed">
</p>

<p align="center">
    <strong>Aplikasi Manajemen Parkir Berbasis Web</strong>
    <br>
    Solusi digital untuk pengelolaan operasional parkir yang terintegrasi, efisien, dan terstruktur.
</p>


## 📌 Tentang Proyek

**Sistem Manajemen Parkir** merupakan aplikasi berbasis web yang dikembangkan untuk membantu proses pengelolaan operasional parkir secara digital dan terintegrasi.

Aplikasi ini dirancang untuk mengelola seluruh aktivitas parkir, mulai dari pencatatan kendaraan masuk dan keluar, manajemen member, pengaturan tarif, pengelolaan area parkir, hingga pemantauan transaksi dan laporan pendapatan.

Sistem menerapkan konsep **Role-Based Access Control (RBAC)** sehingga setiap pengguna memiliki hak akses dan tanggung jawab sesuai dengan perannya masing-masing.

Proyek ini dikembangkan sebagai bagian dari **Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak Paket 2**.

## 🌐 Informasi Proyek

| Informasi          | Detail                        |
| ------------------ | ----------------------------- |
| Nama Proyek        | Sistem Manajemen Parkir       |
| Kategori           | Uji Kompetensi Keahlian (UKK) |
| Paket              | Paket 2                       |
| Platform           | Web Application               |
| Bahasa Pemrograman | PHP                           |
| Database           | MySQL / MariaDB               |
| Status             | Completed                     |

### 🔗 Tautan Proyek

- 🌍 **Live Demo:** [projek-ukk-rizkia.site.je](https://projek-ukk-rizkia.site.je/)
- 💻 **GitHub Repository:** [Bintara007/projek-ukk-paket2-rizkia-bintara](https://github.com/Bintara007/projek-ukk-paket2-rizkia-bintara)

---

## 🔑 Akun Demo

Gunakan akun berikut untuk mencoba aplikasi berdasarkan masing-masing level hak akses.

|       Role       | Username |  Password  |
| :--------------: | :------: | :--------: |
| 👑 Administrator |  `admin` | `admin123` |
|     💼 Owner     |  `owner` | `owner123` |
|    🛡️ Petugas   |   `p1`   |    `p1`    |

> **Catatan:** Akun di atas merupakan akun pengujian. Gunakan hanya untuk keperluan demonstrasi aplikasi.

---

## ✨ Fitur Utama

### 1. 👑 Administrator

Administrator memiliki hak akses penuh untuk mengelola data dan konfigurasi sistem.

- **Manajemen Pengguna:** Mengelola akun pengguna sistem.
- **Manajemen Member:** Menambah, melihat, mengubah, dan menghapus data member.
- **Manajemen Area Parkir:** Mengatur area dan kapasitas parkir.
- **Manajemen Tarif:** Mengelola tarif berdasarkan jenis kendaraan.
- **Monitoring Parkir:** Memantau kendaraan yang sedang parkir.
- **Riwayat Transaksi:** Melihat data transaksi parkir.
- **Log Aktivitas:** Memantau aktivitas pengguna dalam sistem.

### 2. 💼 Owner

Owner memiliki akses untuk memantau aktivitas operasional dan performa bisnis parkir.

- Dashboard ringkasan operasional.
- Monitoring pendapatan keseluruhan.
- Informasi pendapatan harian.
- Rekapitulasi transaksi parkir.
- Statistik aktivitas parkir.
- Pemantauan performa operasional.

### 3. 🛡️ Petugas

Petugas bertanggung jawab terhadap proses operasional kendaraan di area parkir.

- **Kendaraan Masuk:** Mencatat kendaraan yang memasuki area parkir.
- **Pembuatan Transaksi:** Membuat data parkir aktif secara otomatis.
- **Kendaraan Keluar:** Memproses kendaraan yang meninggalkan area parkir.
- **Perhitungan Biaya:** Menghitung biaya parkir berdasarkan tarif yang berlaku.
- **Monitoring Parkir:** Melihat daftar kendaraan yang masih berada di area parkir.

---

## 🛠️ Teknologi yang Digunakan

| Teknologi       | Kegunaan                        |
| --------------- | ------------------------------- |
| PHP Native      | Backend dan pemrosesan aplikasi |
| MySQL / MariaDB | Pengelolaan database            |
| HTML5           | Struktur halaman web            |
| CSS3            | Desain dan tata letak antarmuka |
| JavaScript      | Interaktivitas aplikasi         |
| Bootstrap Icons | Ikon antarmuka                  |
| XAMPP           | Lingkungan pengembangan lokal   |

---

## 🚀 Instalasi dan Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di lingkungan lokal.

### 1. Persiapan

Pastikan perangkat sudah memiliki:

- [XAMPP](https://www.apachefriends.org/)
- PHP
- MySQL / MariaDB
- Web Browser
- Git (opsional)

### 2. Clone Repository

Buka terminal atau Command Prompt, kemudian jalankan:

```bash
git clone https://github.com/Bintara007/projek-ukk-paket2-rizkia-bintara.git
```

### 3. Pindahkan Folder Proyek

Pindahkan folder hasil clone ke direktori:

```text
C:\xampp\htdocs\parkir
```

### 4. Jalankan XAMPP

Buka XAMPP Control Panel, kemudian aktifkan:

- Apache
- MySQL

### 5. Buat Database

1. Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
2. Buat database baru dengan nama `db_parkir`.
3. Pilih database yang telah dibuat.
4. Import file SQL yang tersedia di repository proyek.
5. Tunggu hingga proses import selesai.

> Sesuaikan nama database dengan konfigurasi koneksi aplikasi apabila berbeda.

### 6. Konfigurasi Database

Buka file `koneksi.php`, kemudian sesuaikan konfigurasi koneksi database:

```php
<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_parkir";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
```

> Pastikan struktur variabel dan koneksi disesuaikan dengan implementasi `koneksi.php` pada repository.

### 7. Jalankan Aplikasi

Buka browser dan akses alamat berikut:

```text
http://localhost/parkir
```

Aplikasi siap digunakan setelah konfigurasi database berhasil.

---

## 🔐 Sistem Hak Akses

Aplikasi menggunakan pembagian hak akses berdasarkan tiga jenis pengguna.

| Modul              | Administrator | Owner |      Petugas     |
| ------------------ | :-----------: | :---: | :--------------: |
| Dashboard          |       ✅       |   ✅   |         ✅        |
| Manajemen User     |       ✅       |   ❌   |         ❌        |
| Manajemen Member   |       ✅       |   ❌   |         ❌        |
| Manajemen Area     |       ✅       |   ❌   |         ❌        |
| Manajemen Tarif    |       ✅       |   ❌   |         ❌        |
| Kendaraan Masuk    |       ❌       |   ❌   |         ✅        |
| Kendaraan Keluar   |       ❌       |   ❌   |         ✅        |
| Monitoring Parkir  |       ✅       |   ✅   |         ✅        |
| Riwayat Transaksi  |       ✅       |   ✅   | Sesuai hak akses |
| Laporan Pendapatan |       ❌       |   ✅   |         ❌        |
| Log Aktivitas      |       ✅       |   ❌   |         ❌        |

---

## 📂 Struktur Dokumentasi

Dokumentasi proyek mencakup beberapa bagian utama:

- Deskripsi dan tujuan pengembangan aplikasi.
- Kebutuhan sistem dan teknologi.
- Perancangan database.
- Implementasi antarmuka dan fungsionalitas.
- Pengujian fitur berdasarkan hak akses.
- Dokumentasi penggunaan aplikasi.

---

## 🧪 Pengujian Sistem

Pengujian dilakukan untuk memastikan setiap fitur berjalan sesuai dengan kebutuhan sistem.

Beberapa skenario pengujian meliputi:

- Pengujian login berhasil dan gagal.
- Pengujian pembatasan hak akses setiap role.
- Pengujian pengelolaan data pengguna dan member.
- Pengujian pengaturan tarif dan area parkir.
- Pengujian proses kendaraan masuk.
- Pengujian proses kendaraan keluar.
- Pengujian perhitungan biaya parkir.
- Pengujian pencatatan transaksi.
- Pengujian dashboard dan laporan pendapatan.

---

## 🎯 Tujuan Pengembangan

Pengembangan aplikasi ini bertujuan untuk:

1. Mendigitalisasi proses administrasi dan operasional parkir.
2. Mempermudah pencatatan kendaraan masuk dan keluar.
3. Meminimalkan kesalahan pencatatan transaksi.
4. Mempermudah pengelolaan data member, tarif, dan area parkir.
5. Menyediakan informasi transaksi dan pendapatan secara terstruktur.
6. Menerapkan sistem pengelolaan pengguna berdasarkan hak akses.

---

## 👨‍💻 Pengembang

**Proyek Uji Kompetensi Keahlian (UKK)**

Bidang Keahlian: Rekayasa Perangkat Lunak (RPL)
Paket Kompetensi: Paket 2

Repository: [GitHub Project](https://github.com/Bintara007/projek-ukk-paket2-rizkia-bintara)

---

## 📄 Lisensi dan Penggunaan

Proyek ini dikembangkan untuk memenuhi kebutuhan Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak Paket 2.

Kode sumber dapat digunakan sebagai referensi pembelajaran dan pengembangan aplikasi serupa dengan tetap memperhatikan hak cipta serta ketentuan penggunaan masing-masing komponen yang digunakan.

---

<p align="center">
  <img src="https://img.shields.io/badge/Status-Active-success?style=for-the-badge" alt="Status Active">
  <img src="https://img.shields.io/badge/UKK-RPL%20Paket%202-blue?style=for-the-badge" alt="UKK RPL">
</p>

<h1 align="center">🅿️ SISTEM MANAJEMEN PARKIR</h1>
<p align="center"><em>Smart Parking Management System</em></p>

<p align="center">
  Solusi modern, efisien, dan terintegrasi untuk pengelolaan sistem parkir kendaraan.
</p>

<p align="center">
  ⭐ Jika proyek ini bermanfaat bagi Anda, jangan lupa berikan <strong>Star</strong> pada repository ini!
</p>

<p align="center">
  <a href="https://github.com/Bintara007/projek-ukk-paket2-rizkia-bintara">
    <img src="https://img.shields.io/badge/GitHub-Repository-181717?style=for-the-badge&logo=github" alt="View Repository">
  </a>
</p>

<p align="center">
  <sub>Developed with ❤️ for UKK Rekayasa Perangkat Lunak — Paket 2</sub>
</p>

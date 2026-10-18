<?php

// ============================================================
// KONEKSI DATABASE SISTEM INFORMASI PARKIR
// File     : koneksi.php
// Lokasi   : parkir/koneksi.php
// Fungsi   : Menghubungkan aplikasi PHP dengan database MySQL
// ============================================================


// ============================================================
// BAGIAN 1 - PENGATURAN ZONA WAKTU PHP (REAL-TIME)
// ============================================================

// Mengatur zona waktu default PHP agar sesuai dengan waktu lokal (WIB).
// Anda bisa mengubahnya jika berada di WITA ("Asia/Makassar") atau WIT ("Asia/Jayapura").
date_default_timezone_set('Asia/Jakarta');


// ============================================================
// BAGIAN 2 - KONFIGURASI DATABASE
// ============================================================

$host = "localhost";
$user = "root";
$password = "";
$database = "parkir";


// ============================================================
// BAGIAN 3 - MEMBUAT KONEKSI
// ============================================================

$koneksi = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);


// ============================================================
// BAGIAN 4 - MEMERIKSA KONEKSI
// ============================================================

if (mysqli_connect_errno()) {
    die(
        "Koneksi database gagal: "
        . mysqli_connect_error()
    );
}


// ============================================================
// BAGIAN 5 - PENGATURAN CHARACTER SET & ZONA WAKTU MYSQL
// ============================================================

// Mengatur character set utf8mb4
mysqli_set_charset(
    $koneksi,
    "utf8mb4"
);

// Menyamakan zona waktu MySQL dengan zona waktu PHP (WIB / +07:00).
// Ini penting agar fungsi NOW() atau TIMESTAMP di database ikut akurat secara real-time.
mysqli_query($koneksi, "SET time_zone = '+07:00'");


// ============================================================
// BAGIAN 6 - KONEKSI BERHASIL
// ============================================================

// Jika program sampai sini, koneksi dan pengaturan waktu berhasil.
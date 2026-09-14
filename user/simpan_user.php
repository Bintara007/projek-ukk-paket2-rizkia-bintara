<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap data inputan yang dikirim dari form tambah_user.php
$nama_lengkap = $_POST['nama_lengkap'];
$username     = $_POST['username'];
$password     = $_POST['password'];
$role         = $_POST['role'];
$status_aktif = $_POST['status_aktif'];

// Perintah SQL untuk menambahkan data baru ke tabel tb_user
$query = "INSERT INTO tb_user (nama_lengkap, username, password, role, status_aktif) 
          VALUES ('$nama_lengkap', '$username', '$password', '$role', '$status_aktif')";

// Menjalankan query insert dan mengecek hasilnya
if (mysqli_query($koneksi, $query)) {
    // Jika berhasil, redirect ke halaman tampil_user.php
    header("Location: tampil_user.php");
} else {
    // Jika gagal, tampilkan pesan eror SQL
    echo "Gagal menyimpan data user: " . mysqli_error($koneksi);
}

?>
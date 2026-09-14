<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID user yang dikirim melalui URL
$id = $_GET['id'];

// Perintah SQL untuk menghapus baris data berdasarkan id_user
$query = "DELETE FROM tb_user WHERE id_user = '$id'";

// Menjalankan query hapus
if (mysqli_query($koneksi, $query)) {
    // Jika berhasil, kembali ke halaman utama data user
    header("Location: tampil_user.php");
} else {
    // Jika gagal, tampilkan pesan eror
    echo "Gagal menghapus user: " . mysqli_error($koneksi);
}

?>
<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID log yang dikirim melalui URL
$id = $_GET['id'];

// Perintah SQL untuk menghapus baris data berdasarkan id_log
$query = "DELETE FROM tb_log_aktivitas WHERE id_log = '$id'";

// Menjalankan query hapus
if (mysqli_query($koneksi, $query)) {
    // Jika berhasil, kembali ke halaman utama data log
    header("Location: tampil_log.php");
} else {
    // Jika gagal, tampilkan pesan eror
    echo "Gagal menghapus log: " . mysqli_error($koneksi);
}

?>
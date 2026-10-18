<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID transaksi yang dikirim melalui URL
$id = $_GET['id'];

// Perintah SQL untuk menghapus baris data berdasarkan id_transaksi
$query = "DELETE FROM tb_transaksi WHERE id_transaksi = '$id'";

// Menjalankan query hapus
if (mysqli_query($koneksi, $query)) {
    header("Location: tampil_transaksi.php");
} else {
    echo "Gagal menghapus transaksi: " . mysqli_error($koneksi);
}

?>
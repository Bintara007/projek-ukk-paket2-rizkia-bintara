<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID parkir yang dikirim melalui URL
$id = $_GET['id'];

// Perintah SQL untuk menghapus baris data berdasarkan id_parkir
$query = "DELETE FROM tb_parkir WHERE id_parkir = '$id'";

// Menjalankan query hapus
if (mysqli_query($koneksi, $query)) {
    header("Location: tampil_parkir.php");
} else {
    echo "Gagal menghapus data parkir: " . mysqli_error($koneksi);
}

?>
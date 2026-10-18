<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID member yang dikirim melalui URL
$id = $_GET['id'];

// Perintah SQL untuk menghapus baris data berdasarkan id_member
$query = "DELETE FROM tb_member WHERE id_member = '$id'";

// Menjalankan query hapus
if (mysqli_query($koneksi, $query)) {
    header("Location: tampil_member.php");
} else {
    echo "Gagal menghapus member: " . mysqli_error($koneksi);
}

?>
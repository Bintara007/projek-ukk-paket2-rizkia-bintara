<?php

// Memanggil file koneksi.php untuk menghubungkan ke database
include '../koneksi.php';

// Memeriksa apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari inputan form
    $id_user   = $_POST['id_user'];
    $aktivitas = $_POST['aktivitas'];

    // Query SQL untuk menyimpan data ke tabel tb_log_aktivitas (waktu_aktivitas diisi otomatis dengan waktu saat ini)
    $query = "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES ('$id_user', '$aktivitas', NOW())";

    // Menjalankan query dan memeriksa keberhasilannya
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, alihkan ke halaman tampil_log.php
        header("Location: tampil_log.php");
        exit;
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}

?>
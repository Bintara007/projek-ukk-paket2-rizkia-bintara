<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Memeriksa apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $id_parkir    = $_POST['id_parkir'];
    $waktu_keluar = $_POST['waktu_keluar'];
    $durasi_jam   = $_POST['durasi_jam'];
    $biaya_total  = $_POST['biaya_total'];
    $id_user      = $_POST['id_user'];

    // Validasi jika id_parkir belum dipilih
    if (empty($id_parkir)) {
        echo "<script>
                alert('Pilih kendaraan terlebih dahulu!');
                window.history.back();
              </script>";
        exit;
    }

    // Query SQL untuk menyimpan data ke tb_transaksi
    $query = "INSERT INTO tb_transaksi (id_parkir, waktu_keluar, durasi_jam, biaya_total, id_user) 
              VALUES ('$id_parkir', '$waktu_keluar', '$durasi_jam', '$biaya_total', '$id_user')";

    // Menjalankan query
    if (mysqli_query($koneksi, $query)) {
        header("Location: tampil_transaksi.php");
        exit;
    } else {
        echo "Gagal menyimpan data transaksi: " . mysqli_error($koneksi);
    }
}

?>
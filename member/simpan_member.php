<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Memeriksa apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $nama_member    = $_POST['nama_member'];
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $no_hp          = $_POST['no_hp'];
    $tanggal_daftar = $_POST['tanggal_daftar'];
    $status_aktif   = $_POST['status_aktif'];
    $id_user        = $_POST['id_user'];
    $id_area        = $_POST['id_area'];

    // Query SQL untuk menyimpan data ke tb_member
    $query = "INSERT INTO tb_member (nama_member, jenis_kelamin, no_hp, tanggal_daftar, status_aktif, id_user, id_area) 
              VALUES ('$nama_member', '$jenis_kelamin', '$no_hp', '$tanggal_daftar', '$status_aktif', '$id_user', '$id_area')";

    // Menjalankan query
    if (mysqli_query($koneksi, $query)) {
        header("Location: tampil_member.php");
        exit;
    } else {
        echo "Gagal menyimpan data member: " . mysqli_error($koneksi);
    }
}

?>
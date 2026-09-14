<?php
// Memanggil file koneksi ke database
include '../koneksi.php';

// Memeriksa apakah ID dikirim melalui URL
if (isset($_GET['id'])) {
    // Mengambil nilai ID area dari URL
    $id = $_GET['id'];
    
    // Query SQL untuk menghapus baris data berdasarkan ID
    $query = "DELETE FROM tb_area_parkir WHERE id_area = '$id'";
    
    // Eksekusi query hapus
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil dihapus, kembalikan ke tampil_area_parkir.php
        header("Location: tampil_area_parkir.php");
        exit;
    } else {
        // Jika gagal, tampilkan pesan kesalahan
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
}
?>
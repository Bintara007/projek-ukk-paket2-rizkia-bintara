<?php
// Memanggil file koneksi ke database
include '../koneksi.php';

// Memeriksa apakah ID dikirim melalui URL
if (isset($_GET['id'])) {
    // Mengambil nilai id_tarif dari URL
    $id = $_GET['id'];
    
    // Query SQL untuk menghapus baris data berdasarkan id_tarif
    $query = "DELETE FROM tb_tarif WHERE id_tarif = '$id'";
    
    // Eksekusi query hapus
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil dihapus, kembalikan ke index_tarif.php
        header("Location: tampil_tarif.php");
        exit;
    } else {
        // Jika gagal, tampilkan pesan kesalahan
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
}
?>
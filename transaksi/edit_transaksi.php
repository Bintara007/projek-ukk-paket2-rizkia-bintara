<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID transaksi dari URL
$id = $_GET['id'];

// Mengambil data transaksi berdasarkan id_transaksi
$data = mysqli_query($koneksi, "SELECT * FROM tb_transaksi WHERE id_transaksi = '$id'");
$row  = mysqli_fetch_assoc($data);

// Memproses pembaruan data jika form dikirim (submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_transaksi = $_POST['id_transaksi'];
    $id_parkir    = $_POST['id_parkir'];
    $waktu_keluar = $_POST['waktu_keluar'];
    $durasi_jam   = $_POST['durasi_jam'];
    $biaya_total  = $_POST['biaya_total'];
    $id_user      = $_POST['id_user'];

    // Perintah SQL untuk memperbarui data
    $update = "UPDATE tb_transaksi SET 
                id_parkir    = '$id_parkir',
                waktu_keluar = '$waktu_keluar',
                durasi_jam   = '$durasi_jam',
                biaya_total  = '$biaya_total',
                id_user      = '$id_user'
               WHERE id_transaksi = '$id_transaksi'";

    if (mysqli_query($koneksi, $update)) {
        header("Location: tampil_transaksi.php");
        exit;
    } else {
        echo "Gagal memperbarui data transaksi: " . mysqli_error($koneksi);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
</head>

<body>

    <h2>Edit Data Transaksi</h2>

    <form action="" method="POST">
        <!-- Input hidden untuk id_transaksi -->
        <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi']; ?>">

        <table>
            <tr>
                <td>ID Parkir</td>
                <td><input type="number" name="id_parkir" value="<?= $row['id_parkir']; ?>" required></td>
            </tr>
            <tr>
                <td>Waktu Keluar</td>
                <td><input type="datetime-local" name="waktu_keluar" value="<?= date('Y-m-d\TH:i', strtotime($row['waktu_keluar'])); ?>" required></td>
            </tr>
            <tr>
                <td>Durasi (Jam)</td>
                <td><input type="number" name="durasi_jam" value="<?= $row['durasi_jam']; ?>" required></td>
            </tr>
            <tr>
                <td>Biaya Total (Rp)</td>
                <td><input type="number" name="biaya_total" value="<?= $row['biaya_total']; ?>" required></td>
            </tr>
            <tr>
                <td>ID User</td>
                <td><input type="number" name="id_user" value="<?= $row['id_user']; ?>" required></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Update</button>
                    <a href="tampil_transaksi.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

</body>

</html>
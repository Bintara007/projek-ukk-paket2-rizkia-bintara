<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID parkir dari URL
$id = $_GET['id'];

// Mengambil data parkir berdasarkan id_parkir
$data = mysqli_query($koneksi, "SELECT * FROM tb_parkir WHERE id_parkir = '$id'");
$row  = mysqli_fetch_assoc($data);

// Memproses pembaruan data jika form dikirim (submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_parkir       = $_POST['id_parkir'];
    $id_member       = $_POST['id_member'];
    $plat_nomor      = $_POST['plat_nomor'];
    $warna_kendaraan = $_POST['warna_kendaraan'];
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $id_tarif        = $_POST['id_tarif'];
    $waktu_masuk     = $_POST['waktu_masuk'];
    $id_user_masuk   = $_POST['id_user_masuk'];

    // Perintah SQL untuk memperbarui data
    $update = "UPDATE tb_parkir SET 
                id_member       = '$id_member',
                plat_nomor      = '$plat_nomor',
                warna_kendaraan = '$warna_kendaraan',
                jenis_kendaraan = '$jenis_kendaraan',
                id_tarif        = '$id_tarif',
                waktu_masuk     = '$waktu_masuk',
                id_user_masuk   = '$id_user_masuk'
               WHERE id_parkir  = '$id_parkir'";

    if (mysqli_query($koneksi, $update)) {
        header("Location: tampil_parkir.php");
        exit;
    } else {
        echo "Gagal memperbarui data parkir: " . mysqli_error($koneksi);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Parkir</title>
</head>

<body>

    <h2>Edit Data Parkir</h2>

    <form action="" method="POST">
        <!-- Input hidden untuk id_parkir -->
        <input type="hidden" name="id_parkir" value="<?= $row['id_parkir']; ?>">

        <table>
            <tr>
                <td>ID Member</td>
                <td><input type="number" name="id_member" value="<?= $row['id_member']; ?>" required></td>
            </tr>
            <tr>
                <td>Plat Nomor</td>
                <td><input type="text" name="plat_nomor" value="<?= $row['plat_nomor']; ?>" required></td>
            </tr>
            <tr>
                <td>Warna Kendaraan</td>
                <td><input type="text" name="warna_kendaraan" value="<?= $row['warna_kendaraan']; ?>" required></td>
            </tr>
            <tr>
                <td>Jenis Kendaraan</td>
                <td>
                    <select name="jenis_kendaraan" required>
                        <option value="motor" <?= $row['jenis_kendaraan'] == 'motor' ? 'selected' : ''; ?>>Motor</option>
                        <option value="mobil" <?= $row['jenis_kendaraan'] == 'mobil' ? 'selected' : ''; ?>>Mobil</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>ID Tarif</td>
                <td><input type="number" name="id_tarif" value="<?= $row['id_tarif']; ?>" required></td>
            </tr>
            <tr>
                <td>Waktu Masuk</td>
                <td><input type="datetime-local" name="waktu_masuk" value="<?= date('Y-m-d\TH:i', strtotime($row['waktu_masuk'])); ?>" required></td>
            </tr>
            <tr>
                <td>ID User Masuk</td>
                <td><input type="number" name="id_user_masuk" value="<?= $row['id_user_masuk']; ?>" required></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Update</button>
                    <a href="tampil_parkir.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

</body>

</html>
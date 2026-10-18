<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID log dari URL ($_GET)
$id = $_GET['id'];

// Mengambil data log berdasarkan id_log
$data = mysqli_query($koneksi, "SELECT * FROM tb_log_aktivitas WHERE id_log = '$id'");
// Mengubah data menjadi array tunggal
$row  = mysqli_fetch_assoc($data);

// Memproses pembaruan data jika form dikirim (submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_log    = $_POST['id_log'];
    $id_user   = $_POST['id_user'];
    $aktivitas = $_POST['aktivitas'];

    // Perintah SQL untuk memperbarui data log berdasarkan id_log
    $update = "UPDATE tb_log_aktivitas SET 
                id_user   = '$id_user',
                aktivitas = '$aktivitas'
               WHERE id_log = '$id_log'";

    // Menjalankan query update
    if (mysqli_query($koneksi, $update)) {
        header("Location: tampil_log.php");
        exit;
    } else {
        echo "Gagal memperbarui data log: " . mysqli_error($koneksi);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Log Aktivitas</title>
</head>

<body>

    <h2>Edit Data Log Aktivitas</h2>

    <form action="" method="POST">
        <!-- Input hidden untuk menampung id_log sebagai acuan update -->
        <input type="hidden" name="id_log" value="<?= $row['id_log']; ?>">

        <table>
            <tr>
                <td>ID User</td>
                <td><input type="number" name="id_user" value="<?= $row['id_user']; ?>" required></td>
            </tr>
            <tr>
                <td>Aktivitas</td>
                <td><textarea name="aktivitas" rows="4" cols="30" required><?= $row['aktivitas']; ?></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Update</button>
                    <a href="tampil_log.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

</body>

</html>
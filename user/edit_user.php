<?php

// Memanggil file koneksi.php
include '../koneksi.php';

// Menangkap ID user dari URL ($_GET)
$id = $_GET['id'];

// Mengambil data user berdasarkan id_user
$data = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$id'");
// Mengubah data menjadi array tunggal
$row  = mysqli_fetch_assoc($data);

// Memproses pembaruan data jika form dikirim (submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user      = $_POST['id_user'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $username     = $_POST['username'];
    $password     = $_POST['password'];
    $role         = $_POST['role'];
    $status_aktif = $_POST['status_aktif'];

    // Perintah SQL untuk memperbarui data user berdasarkan id_user
    $update = "UPDATE tb_user SET 
                nama_lengkap = '$nama_lengkap',
                username     = '$username',
                password     = '$password',
                role         = '$role',
                status_aktif = '$status_aktif'
               WHERE id_user = '$id_user'";

    // Menjalankan query update
    if (mysqli_query($koneksi, $update)) {
        header("Location: tampil_user.php");
        exit;
    } else {
        echo "Gagal memperbarui data user: " . mysqli_error($koneksi);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>

<body>

    <h2>Edit Data User</h2>

    <form action="" method="POST">
        <!-- Input hidden untuk menampung id_user sebagai acuan update -->
        <input type="hidden" name="id_user" value="<?= $row['id_user']; ?>">

        <table>
            <tr>
                <td>Nama Lengkap</td>
                <td><input type="text" name="nama_lengkap" value="<?= $row['nama_lengkap']; ?>" required></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><input type="text" name="username" value="<?= $row['username']; ?>" required></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input type="text" name="password" value="<?= $row['password']; ?>" required></td>
            </tr>
            <tr>
                <td>Role</td>
                <td>
                    <!-- Pilihan otomatis terpilih (selected) sesuai data lama di database -->
                    <select name="role" required>
                        <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="petugas" <?= $row['role'] == 'petugas' ? 'selected' : ''; ?>>Petugas</option>
                        <option value="owner" <?= $row['role'] == 'owner' ? 'selected' : ''; ?>>Owner</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Status Aktif</td>
                <td>
                    <select name="status_aktif" required>
                        <option value="1" <?= $row['status_aktif'] == '1' ? 'selected' : ''; ?>>1 - Aktif</option>
                        <option value="0" <?= $row['status_aktif'] == '0' ? 'selected' : ''; ?>>0 - Nonaktif</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Update</button>
                    <a href="tampil_user.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

</body>

</html>
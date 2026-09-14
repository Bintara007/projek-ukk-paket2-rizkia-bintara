<?php

// Memanggil file koneksi.php untuk menghubungkan ke database
include '../koneksi.php';

// Menjalankan query SQL untuk mengambil seluruh data dari tabel tb_user
$data = mysqli_query($koneksi, "SELECT * FROM tb_user");

// Mengubah seluruh hasil query menjadi array asosiatif (MYSQLI_ASSOC)
$rows = mysqli_fetch_all($data, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Menentukan karakter encoding halaman -->
    <meta charset="UTF-8">

    <!-- Judul yang muncul pada tab browser -->
    <title>Data User</title>
</head>

<body>

    <!-- Judul utama halaman -->
    <h2>Data User</h2>

    <!-- Link untuk berpindah ke halaman form tambah user -->
    <p><a href="tambah_user.php">+ Tambah Data User</a></p>

    <!-- Membuat tabel untuk menampilkan data -->
    <table border="1" cellpadding="5" cellspacing="0">
        <!-- <tr> untuk baris, <th> untuk judul kolom -->
        <tr>
            <th>ID User</th>
            <th>Nama Lengkap</th>
            <th>Username</th>
            <th>Password</th>
            <th>Role</th>
            <th>Status Aktif</th>
            <th>Aksi</th>
        </tr>

        <?php
        /*
            FOREACH
            digunakan untuk mengulangi setiap baris data
            yang ada di dalam array $rows.
        */
        foreach ($rows as $row):
        ?>

        <!-- Menampilkan baris data user -->
        <tr>
            <!-- Menampilkan data sesuai nama kolom tabel tb_user -->
            <td><?= $row['id_user']; ?></td>
            <td><?= $row['nama_lengkap']; ?></td>
            <td><?= $row['username']; ?></td>
            <!-- Mengubah isi password menjadi tanda bintang (*) sebanyak jumlah karakternya -->
            <td><?= str_repeat('*', strlen($row['password'])); ?></td>
            <td><?= $row['role']; ?></td>
            <td><?= $row['status_aktif']; ?></td>

            <!-- Link Aksi untuk Edit dan Hapus dengan mengirimkan parameter ID -->
            <td>
                <a href="edit_user.php?id=<?= $row['id_user']; ?>">Edit</a> | 
                <a href="hapus_user.php?id=<?= $row['id_user']; ?>" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
            </td>
        </tr>

        <?php
        // Mengakhiri perulangan foreach
        endforeach;
        ?>
    </table>

</body>

</html>
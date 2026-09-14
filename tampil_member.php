<?php

// Memanggil file koneksi.php
// include digunakan untuk memasukkan file PHP lain
// ke dalam program
include 'koneksi.php';


// Menjalankan query SQL
// mysqli_query() digunakan untuk menjalankan perintah SQL
// SELECT * = mengambil semua kolom
// FROM tb_member = mengambil data dari tabel tb_member
$data = mysqli_query($koneksi, "SELECT * FROM tb_member");


// Mengambil semua hasil query menjadi array
// MYSQLI_ASSOC = data dapat dipanggil berdasarkan nama kolom
$rows = mysqli_fetch_all($data, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <!-- Menentukan karakter halaman -->
    <meta charset="UTF-8">

    <!-- Judul pada tab browser -->
    <title>Data Member</title>

</head>

<body>

    <!-- Judul halaman -->
    <h2>Data Member</h2>


    <!-- Membuat tabel -->
    <table border="1" cellpadding="5" cellspacing="0">

        <!--
            <tr> = membuat baris tabel
            <th> = membuat judul kolom
        -->
        <tr>
            <th>ID Member</th>
            <th>Nama Member</th>
            <th>Jenis Kelamin</th>
            <th>No. Telepon</th>
            <th>Tanggal Daftar</th>
            <th>Status</th>
            <th>Id User</th>
            <th>Id Area</th>
        </tr>


        <?php

        /*
            FOREACH

            foreach digunakan untuk melakukan perulangan
            pada setiap data yang terdapat di dalam array.

            $rows = berisi semua data dari tb_member

            $row = berisi satu data member yang sedang
                   diproses oleh foreach.

            Jika terdapat 5 data, maka foreach akan
            melakukan perulangan sebanyak 5 kali.
        */

        foreach ($rows as $row):

        ?>

        <!--
            Membuat satu baris untuk setiap data member
        -->
        <>

            <!-- Menampilkan ID member -->
            <td><?= $row['id_member']; ?></td>

            <!-- Menampilkan nama member -->
            <td><?= $row['nama_member']; ?></td>

            <!-- Menampilkan nomor telepon -->
            <td><?= $row['jenis_kelamin']; ?></td>

            <!-- Menampilkan alamat -->
            <td><?= $row['no_hp']; ?></td>

            <!-- Menampilkan jenis kelamin -->
            <td><?= $row['tanggal_daftar']; ?></td>

            <!-- Menampilkan status member -->
            <td><?= $row['status_aktif']; ?></td>
            
            <!-- Menampilkan id user -->
            <td><?= $row['id_user']; ?></td>

        </tr>

        <?php

        /*
            endforeach digunakan untuk mengakhiri
            perulangan foreach.

            Setelah sampai endforeach, foreach akan
            mengambil data berikutnya sampai semua
            data selesai ditampilkan.
        */

        endforeach;

        ?>

    </table>

</body>

</html>
<?php
// Memanggil file koneksi untuk menyambungkan ke database
include '../koneksi.php';

// Menjalankan perintah SQL untuk mengambil seluruh data dari tabel tb_tarif
$data = mysqli_query($koneksi, "SELECT * FROM tb_tarif");

// Mengubah hasil query menjadi array asosiatif agar mudah diulangi (looping)
$rows = mysqli_fetch_all($data, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Tarif Parkir</title>
</head>
<body>

    <h2>Data Tarif Parkir</h2>
    
    <!-- Tombol/Link untuk menuju ke halaman tambah data tarif -->
    <a href="tambah_tarif.php">+ Tambah Tarif Parkir</a><br><br>

    <!-- Tabel untuk menampilkan data tarif -->
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID Tarif</th>
            <th>Jenis Kendaraan</th>
            <th>Tarif Per Jam</th>
            <th>Aksi</th> <!-- Kolom aksi untuk tombol Edit dan Hapus -->
        </tr>

        <!-- Pengulangan (looping) untuk menampilkan tiap baris data -->
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= $row['id_tarif']; ?></td>
            <td><?= $row['jenis_kendaraan']; ?></td>
            
            <!-- Format tampilan harga ke dalam rupiah -->
            <td>Rp <?= number_format($row['tarif_per_jam'], 0, ',', '.'); ?></td>
            
            <td>
                <!-- Link Edit mengirimkan ID tarif melalui URL (GET) -->
                <a href="edit_tarif.php?id=<?= $row['id_tarif']; ?>">Edit</a> | 
                
                <!-- Link Hapus mengirimkan ID tarif melalui URL disertai konfirmasi JavaScript -->
                <a href="hapus_tarif.php?id=<?= $row['id_tarif']; ?>" onclick="return confirm('Yakin ingin menghapus tarif ini?')">Hapus</a>
            </td>
            
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
<?php
// Memanggil file koneksi untuk menyambungkan ke database
include '../koneksi.php';

// Menjalankan perintah SQL untuk mengambil seluruh data dari tabel tb_area_parkir
$data = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir");

// Mengubah hasil query menjadi array asosiatif agar mudah diulangi (looping)
$rows = mysqli_fetch_all($data, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Area Parkir</title>
</head>
<body>

    <h2>Data Area Parkir</h2>
    
    <!-- Tombol/Link untuk menuju ke halaman tambah data -->
    <a href="tambah_area.php">+ Tambah Area Parkir</a><br><br>

    <!-- Tabel untuk menampilkan data -->
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID Area</th>
            <th>Nama Area</th>
            <th>Kapasitas</th>
            <th>Terisi</th>
            <th>Sisa Slot</th>
            <th>Aksi</th> <!-- Kolom aksi untuk tombol Edit dan Hapus -->
        </tr>

        <!-- Pengulangan (looping) untuk menampilkan tiap baris data -->
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= $row['id_area']; ?></td>
            <td><?= $row['nama_area']; ?></td>
            <td><?= $row['kapasitas']; ?></td>
            <td><?= $row['terisi']; ?></td>
            
            <!-- Perhitungan Sisa Slot secara otomatis (Kapasitas dikurangi Terisi) -->
            <td><?= $row['kapasitas'] - $row['terisi']; ?></td>
            
            <td>
                <!-- Link Edit mengirimkan ID area melalui URL (GET) -->
                <a href="edit_area.php?id=<?= $row['id_area']; ?>">Edit</a> | 
                
                <!-- Link Hapus mengirimkan ID area melalui URL disertai konfirmasi JavaScript -->
                <a href="hapus_area.php?id=<?= $row['id_area']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
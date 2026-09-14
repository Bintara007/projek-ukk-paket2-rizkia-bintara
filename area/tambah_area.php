<?php
// Memanggil file koneksi ke database
include '../koneksi.php';

// Memeriksa apakah tombol 'submit' pada form sudah ditekan
if (isset($_POST['submit'])) {
    // Mengambil nilai input dari form HTML
    $nama_area = $_POST['nama_area'];
    $kapasitas = $_POST['kapasitas'];
    $terisi    = $_POST['terisi'];

    // Query SQL untuk memasukkan data baru ke tabel tb_area_parkir
    $query = "INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi) VALUES ('$nama_area', '$kapasitas', '$terisi')";
    
    // Eksekusi query ke database
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, alihkan halaman kembali ke tampil_area_parkir.php
        header("Location: tampil_area_parkir.php");
        exit;
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Area Parkir</title>
</head>
<body>

    <h2>Tambah Area Parkir</h2>

    <!-- Form menggunakan metode POST untuk mengalihkan inputan data secara aman -->
    <form action="" method="POST">
        <label>Nama Area:</label><br>
        <input type="text" name="nama_area" required><br><br>

        <label>Kapasitas:</label><br>
        <input type="number" name="kapasitas" required><br><br>

        <label>Terisi:</label><br>
        <input type="number" name="terisi" value="0" required><br><br>

        <!-- Tombol untuk mengirimkan data form -->
        <button type="submit" name="submit">Simpan</button>
        <a href="tampil_area.php">Batal</a>
    </form>

</body>
</html>
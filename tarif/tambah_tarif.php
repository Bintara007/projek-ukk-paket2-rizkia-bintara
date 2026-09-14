<?php
// Memanggil file koneksi ke database
include '../koneksi.php';

// Memeriksa apakah tombol 'submit' pada form sudah ditekan
if (isset($_POST['submit'])) {
    // Mengambil nilai input dari form HTML
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $tarif_per_jam   = $_POST['tarif_per_jam'];

    // Query SQL untuk memasukkan data tarif baru ke tabel tb_tarif
    $query = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES ('$jenis_kendaraan', '$tarif_per_jam')";
    
    // Eksekusi query ke database
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, alihkan halaman kembali ke index_tarif.php
        header("Location: tampil_tarif.php");
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
    <title>Tambah Tarif Parkir</title>
</head>
<body>

    <h2>Tambah Tarif Parkir</h2>

    <!-- Form menggunakan metode POST untuk mengirimkan data secara aman -->
    <form action="" method="POST">
        <label>Jenis Kendaraan:</label><br>
        <input type="text" name="jenis_kendaraan" placeholder="Contoh: Motor / Mobil" required><br><br>

        <label>Tarif Per Jam (Rp):</label><br>
        <input type="number" name="tarif_per_jam" placeholder="Contoh: 3000" required><br><br>

        <!-- Tombol untuk mengirimkan data form -->
        <button type="submit" name="submit">Simpan</button>
        <a href="tampil_tarif.php">Batal</a>
    </form>

</body>
</html>
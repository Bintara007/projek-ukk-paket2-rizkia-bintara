<?php
// Memanggil file koneksi ke database
include '../koneksi.php';

// Mengambil ID tarif dari parameter URL (method GET)
$id = $_GET['id'];

// Query untuk mengambil data tarif lama berdasarkan ID yang dipilih
$result = mysqli_query($koneksi, "SELECT * FROM tb_tarif WHERE id_tarif = '$id'");
$data = mysqli_fetch_assoc($result);

// Memeriksa apakah tombol 'update' pada form sudah ditekan
if (isset($_POST['update'])) {
    // Mengambil nilai baru dari form
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $tarif_per_jam   = $_POST['tarif_per_jam'];

    // Query SQL untuk memperbarui data berdasarkan id_tarif
    $query = "UPDATE tb_tarif SET 
                jenis_kendaraan = '$jenis_kendaraan', 
                tarif_per_jam = '$tarif_per_jam' 
              WHERE id_tarif = '$id'";

    // Eksekusi query update ke database
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, alihkan kembali ke halaman utama tarif (index_tarif.php)
        header("Location: tampil_tarif.php");
        exit;
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tarif Parkir</title>
</head>
<body>

    <h2>Edit Tarif Parkir</h2>

    <!-- Form edit, atribut value diisi dengan data lama dari database -->
    <form action="" method="POST">
        <label>Jenis Kendaraan:</label><br>
        <input type="text" name="jenis_kendaraan" value="<?= $data['jenis_kendaraan']; ?>" required><br><br>

        <label>Tarif Per Jam (Rp):</label><br>
        <input type="number" name="tarif_per_jam" value="<?= $data['tarif_per_jam']; ?>" required><br><br>

        <!-- Tombol submit untuk memperbarui data -->
        <button type="submit" name="update">Update</button>
        <a href="tampil_tarif.php">Batal</a>
    </form>

</body>
</html>
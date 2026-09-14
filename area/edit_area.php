<?php
// Memanggil file koneksi ke database
include '../koneksi.php';

// Mengambil ID area dari parameter URL (method GET)
$id = $_GET['id'];

// Query untuk mengambil data lama berdasarkan ID yang dipilih
$result = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir WHERE id_area = '$id'");
$data = mysqli_fetch_assoc($result);

// Memeriksa apakah tombol 'update' pada form sudah ditekan
if (isset($_POST['update'])) {
    // Mengambil nilai baru dari form
    $nama_area = $_POST['nama_area'];
    $kapasitas = $_POST['kapasitas'];
    $terisi    = $_POST['terisi'];

    // Query SQL untuk memperbarui data berdasarkan ID area
    $query = "UPDATE tb_area_parkir SET 
                nama_area = '$nama_area', 
                kapasitas = '$kapasitas', 
                terisi = '$terisi' 
              WHERE id_area = '$id'";

    // Eksekusi query update ke database
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, alihkan kembali ke halaman utama (index.php)
        header("Location: tampil_area_parkir.php");
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
    <title>Edit Area Parkir</title>
</head>
<body>

    <h2>Edit Area Parkir</h2>

    <!-- Form edit, atribut value diisi dengan data lama dari database -->
    <form action="" method="POST">
        <label>Nama Area:</label><br>
        <input type="text" name="nama_area" value="<?= $data['nama_area']; ?>" required><br><br>

        <label>Kapasitas:</label><br>
        <input type="number" name="kapasitas" value="<?= $data['kapasitas']; ?>" required><br><br>

        <label>Terisi:</label><br>
        <input type="number" name="terisi" value="<?= $data['terisi']; ?>" required><br><br>

        <!-- Tombol submit untuk memperbarui data -->
        <button type="submit" name="update">Update</button>
        <a href="index.php">Batal</a>
    </form>

</body>
</html>
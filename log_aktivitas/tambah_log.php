<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Menentukan karakter encoding halaman -->
    <meta charset="UTF-8">

    <!-- Judul yang muncul pada tab browser -->
    <title>Tambah Log Aktivitas</title>
</head>

<body>

    <!-- Judul utama halaman -->
    <h2>Tambah Data Log Aktivitas</h2>

    <!-- Form mengirim data ke simpan_log.php menggunakan metode POST -->
    <form action="simpan_log.php" method="POST">
        <table>
            <tr>
                <td>ID User</td>
                <td><input type="number" name="id_user" required></td>
            </tr>
            <tr>
                <td>Aktivitas</td>
                <td><textarea name="aktivitas" rows="4" cols="30" required></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <!-- Tombol simpan dan batal -->
                    <button type="submit">Simpan</button>
                    <a href="tampil_log.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

</body>

</html>
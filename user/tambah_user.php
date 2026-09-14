<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
</head>

<body>

    <h2>Tambah Data User</h2>

    <!-- Form mengirim data ke simpan_user.php menggunakan metode POST -->
    <form action="simpan_user.php" method="POST">
        <table>
            <tr>
                <td>Nama Lengkap</td>
                <td><input type="text" name="nama_lengkap" required></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><input type="text" name="username" required></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td>Role</td>
                <td>
                    <!-- Dropdown pilihan role -->
                    <select name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="owner">Owner</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Status Aktif</td>
                <td>
                    <!-- Dropdown status keaktifan user -->
                    <select name="status_aktif" required>
                        <option value="1">1 - Aktif</option>
                        <option value="0">0 - Nonaktif</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Simpan</button>
                    <a href="tampil_user.php">Batal</a>
                    <a href=""></a>
                </td>
            </tr>
        </table>
    </form>

</body>

</html>
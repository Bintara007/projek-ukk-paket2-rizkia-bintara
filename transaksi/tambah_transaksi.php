<?php
// Memanggil file koneksi.php
include '../koneksi.php';

// Ambil data kendaraan dari tb_parkir yang BELUM pernah keluar/transaksi
$query_parkir = "SELECT * FROM tb_parkir 
                 WHERE id_parkir NOT IN (SELECT id_parkir FROM tb_transaksi)";
$data_parkir = mysqli_query($koneksi, $query_parkir);

// Ambil data user/petugas yang aktif
$data_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE status_aktif = '1'");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Transaksi</title>
</head>

<body>

    <h2>Tambah Data Transaksi (Kendaraan Keluar)</h2>

    <form action="simpan_transaksi.php" method="POST">
        <table>
            <tr>
                <td>Pilih Kendaraan / Plat Nomor</td>
                <td>
                    <select name="id_parkir" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        <?php 
                        if (mysqli_num_rows($data_parkir) > 0) {
                            while ($p = mysqli_fetch_assoc($data_parkir)) {
                                echo "<option value='" . $p['id_parkir'] . "'>";
                                echo "ID: " . $p['id_parkir'] . " | Plat: " . $p['plat_nomor'] . " (" . $p['jenis_kendaraan'] . ")";
                                echo "</option>";
                            }
                        } else {
                            echo "<option value=''>-- Tidak ada kendaraan aktif --</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Waktu Keluar</td>
                <td><input type="datetime-local" name="waktu_keluar" value="<?= date('Y-m-d\TH:i'); ?>" required></td>
            </tr>
            <tr>
                <td>Durasi (Jam)</td>
                <td><input type="number" name="durasi_jam" min="1" required></td>
            </tr>
            <tr>
                <td>Biaya Total (Rp)</td>
                <td><input type="number" name="biaya_total" required></td>
            </tr>
            <tr>
                <td>Petugas / User</td>
                <td>
                    <select name="id_user" required>
                        <option value="">-- Pilih Petugas --</option>
                        <?php while ($u = mysqli_fetch_assoc($data_user)): ?>
                            <option value="<?= $u['id_user']; ?>">
                                <?= $u['nama_lengkap']; ?> (ID: <?= $u['id_user']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Simpan Transaksi</button>
                    <a href="tampil_transaksi.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

</body>

</html>
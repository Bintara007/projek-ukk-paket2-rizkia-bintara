<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id_user_login = (int) $_SESSION['id_user'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
    $tarif_per_jam = (int) ($_POST['tarif_per_jam'] ?? 0);

    $jenis_valid = [
        'motor',
        'mobil',
        'mini bus',
        'bus'
    ];

    if (!in_array($jenis_kendaraan, $jenis_valid, true)) {
        $error = 'Jenis kendaraan tidak valid.';
    }

    if ($error === '' && $tarif_per_jam <= 0) {
        $error = 'Tarif per jam harus lebih dari 0.';
    }

    /* Cek tarif kendaraan */

    if ($error === '') {

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id_tarif
             FROM tb_tarif
             WHERE jenis_kendaraan = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $jenis_kendaraan
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = 'Tarif untuk jenis kendaraan tersebut sudah ada.';
        }

        mysqli_stmt_close($stmt);
    }

    if ($error === '') {

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO tb_tarif
            (
                jenis_kendaraan,
                tarif_per_jam
            )
            VALUES (?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $jenis_kendaraan,
            $tarif_per_jam
        );

        if (mysqli_stmt_execute($stmt)) {

            $aktivitas =
                "Menambahkan tarif kendaraan: " .
                $jenis_kendaraan .
                " - Rp " .
                number_format($tarif_per_jam, 0, ',', '.');

            $stmt_log = mysqli_prepare(
                $koneksi,
                "INSERT INTO tb_log_aktivitas
                (id_user, aktivitas, waktu_aktivitas)
                VALUES (?, ?, NOW())"
            );

            mysqli_stmt_bind_param(
                $stmt_log,
                "is",
                $id_user_login,
                $aktivitas
            );

            mysqli_stmt_execute($stmt_log);
            mysqli_stmt_close($stmt_log);

            header("Location: tampil_tarif.php?status=tambah");
            exit;

        } else {

            $error = 'Gagal menambahkan tarif: ' .
                     mysqli_error($koneksi);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah Tarif</title>

    <link
        rel="stylesheet"
        href="../assets/css/form.css"
    >

</head>

<body>

<div class="container">

    <div class="form-card">

        <div class="form-header">

            <h1>Tambah Tarif</h1>

            <p>
                Menambahkan tarif parkir kendaraan.
            </p>

        </div>


        <div class="form-body">

            <?php if ($error !== ''): ?>

                <div class="form-alert form-alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <form method="POST">

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Jenis Kendaraan
                        </label>

                        <select
                            name="jenis_kendaraan"
                            required
                        >

                            <option value="">
                                -- Pilih Kendaraan --
                            </option>

                            <option value="motor">
                                Motor
                            </option>

                            <option value="mobil">
                                Mobil
                            </option>

                            <option value="mini bus">
                                Mini Bus
                            </option>

                            <option value="bus">
                                Bus
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Tarif Per Jam
                        </label>

                        <input
                            type="number"
                            name="tarif_per_jam"
                            min="1"
                            value="<?= htmlspecialchars($_POST['tarif_per_jam'] ?? '') ?>"
                            required
                        >

                    </div>

                </div>


                <div class="form-actions">

                    <a
                        href="tampil_tarif.php"
                        class="btn btn-secondary"
                    >
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan Tarif
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>
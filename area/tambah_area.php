<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id_user_login = (int) $_SESSION['id_user'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_area = trim($_POST['nama_area'] ?? '');
    $kapasitas = (int) ($_POST['kapasitas'] ?? 0);

    if ($nama_area === '') {
        $error = 'Nama area wajib diisi.';
    }

    if ($error === '' && $kapasitas <= 0) {
        $error = 'Kapasitas harus lebih dari 0.';
    }

    /* Cek nama area */

    if ($error === '') {

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id_area
             FROM tb_area_parkir
             WHERE nama_area = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $nama_area
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = 'Nama area sudah digunakan.';
        }

        mysqli_stmt_close($stmt);
    }

    if ($error === '') {

        $terisi = 0;

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO tb_area_parkir
            (
                nama_area,
                kapasitas,
                terisi
            )
            VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sii",
            $nama_area,
            $kapasitas,
            $terisi
        );

        if (mysqli_stmt_execute($stmt)) {

            $aktivitas =
                "Menambahkan area parkir: " .
                $nama_area;

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

            header("Location: tampil_area_parkir.php?status=tambah");
            exit;

        } else {

            $error = 'Gagal menambahkan area: ' .
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

    <title>Tambah Area Parkir</title>

    <link
        rel="stylesheet"
        href="../assets/css/form.css"
    >

</head>

<body>

<div class="container">

    <div class="form-card">

        <div class="form-header">

            <h1>Tambah Area Parkir</h1>

            <p>
                Menambahkan area parkir baru.
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
                            Nama Area
                        </label>

                        <input
                            type="text"
                            name="nama_area"
                            value="<?= htmlspecialchars($_POST['nama_area'] ?? '') ?>"
                            placeholder="Contoh: Area A"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Kapasitas
                        </label>

                        <input
                            type="number"
                            name="kapasitas"
                            min="1"
                            value="<?= htmlspecialchars($_POST['kapasitas'] ?? '') ?>"
                            placeholder="Contoh: 50"
                            required
                        >

                    </div>

                </div>


                <div class="form-actions">

                    <a
                        href="tampil_area_parkir.php"
                        class="btn btn-secondary"
                    >
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan Area
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>
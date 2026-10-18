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

$id_tarif = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id_tarif <= 0) {
    header("Location: tampil_tarif.php");
    exit;
}

/* Ambil data */

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT
        id_tarif,
        jenis_kendaraan,
        tarif_per_jam
     FROM tb_tarif
     WHERE id_tarif = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_tarif
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: tampil_tarif.php");
    exit;
}

$error = '';

/* Update */

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
        $error = 'Tarif harus lebih dari 0.';
    }

    if ($error === '') {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE tb_tarif
             SET
                jenis_kendaraan = ?,
                tarif_per_jam = ?
             WHERE id_tarif = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sii",
            $jenis_kendaraan,
            $tarif_per_jam,
            $id_tarif
        );

        if (mysqli_stmt_execute($stmt)) {

            $aktivitas =
                "Mengubah tarif kendaraan: " .
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

            mysqli_stmt_close($stmt);

            header("Location: tampil_tarif.php?status=edit");
            exit;
        }

        $error = 'Gagal mengubah tarif: ' .
                 mysqli_error($koneksi);

        mysqli_stmt_close($stmt);
    }

    $data['jenis_kendaraan'] = $jenis_kendaraan;
    $data['tarif_per_jam'] = $tarif_per_jam;
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

    <title>Edit Tarif</title>

    <link
        rel="stylesheet"
        href="../assets/css/form.css"
    >

</head>

<body>

<div class="container">

    <div class="form-card">

        <div class="form-header">

            <h1>Edit Tarif</h1>

            <p>
                Mengubah tarif parkir kendaraan.
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

                            <option
                                value="motor"
                                <?= $data['jenis_kendaraan'] === 'motor' ? 'selected' : '' ?>
                            >
                                Motor
                            </option>

                            <option
                                value="mobil"
                                <?= $data['jenis_kendaraan'] === 'mobil' ? 'selected' : '' ?>
                            >
                                Mobil
                            </option>

                            <option
                                value="mini bus"
                                <?= $data['jenis_kendaraan'] === 'mini bus' ? 'selected' : '' ?>
                            >
                                Mini Bus
                            </option>

                            <option
                                value="bus"
                                <?= $data['jenis_kendaraan'] === 'bus' ? 'selected' : '' ?>
                            >
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
                            value="<?= htmlspecialchars($data['tarif_per_jam']) ?>"
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
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>     
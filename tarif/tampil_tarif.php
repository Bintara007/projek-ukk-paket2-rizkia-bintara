<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../koneksi.php';

/* =========================================================
   CEK LOGIN
   ========================================================= */

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

/* =========================================================
   DASHBOARD
   ========================================================= */

$role = $_SESSION['role'] ?? '';

if ($role === 'admin') {
    $dashboard = '../dashboard_admin.php';
} elseif ($role === 'petugas') {
    $dashboard = '../dashboard_petugas.php';
} elseif ($role === 'owner') {
    $dashboard = '../dashboard_owner.php';
} else {
    header("Location: ../index.php");
    exit;
}

/* =========================================================
   SEARCH
   ========================================================= */

$keyword = trim($_GET['keyword'] ?? '');

if ($keyword !== '') {

    $keyword_sql = mysqli_real_escape_string($koneksi, $keyword);

    $query = mysqli_query(
        $koneksi,
        "SELECT
            id_tarif,
            jenis_kendaraan,
            tarif_per_jam
         FROM tb_tarif
         WHERE
            jenis_kendaraan LIKE '%$keyword_sql%'
            OR tarif_per_jam LIKE '%$keyword_sql%'
         ORDER BY id_tarif DESC"
    );

} else {

    $query = mysqli_query(
        $koneksi,
        "SELECT
            id_tarif,
            jenis_kendaraan,
            tarif_per_jam
         FROM tb_tarif
         ORDER BY id_tarif DESC"
    );
}

$tarif = mysqli_fetch_all($query, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Data Tarif</title>

    <link
        rel="stylesheet"
        href="../assets/css/tabel.css"
    >

</head>

<body>

<div class="container">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Data Tarif
            </h1>

            <p class="page-subtitle">
                Kelola tarif parkir berdasarkan jenis kendaraan.
            </p>

        </div>

        <a
            href="<?= htmlspecialchars($dashboard) ?>"
            class="btn-kembali"
        >
            ← Kembali ke Dashboard
        </a>

    </div>


    <div class="table-card">

        <div class="table-toolbar">

            <div class="toolbar-left">

                <form
                    method="GET"
                    class="search-box"
                >

                    <input
                        type="text"
                        name="keyword"
                        value="<?= htmlspecialchars($keyword) ?>"
                        placeholder="Cari tarif..."
                    >

                </form>

            </div>


            <div class="toolbar-right">

                <?php if ($role === 'admin'): ?>

                    <a
                        href="tambah_tarif.php"
                        class="btn btn-warning"
                    >
                        + Tambah Tarif
                    </a>

                <?php endif; ?>

            </div>

        </div>


        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>ID Tarif</th>
                        <th>Jenis Kendaraan</th>
                        <th>Tarif Per Jam</th>
                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($tarif) > 0): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($tarif as $t): ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td>
                                <?= (int)$t['id_tarif'] ?>
                            </td>

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        ucfirst($t['jenis_kendaraan'])
                                    ) ?>
                                </strong>

                            </td>

                            <td>

                                Rp
                                <?= number_format(
                                    (int)$t['tarif_per_jam'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                                / jam

                            </td>

                            <td>

                                <div class="aksi">

                                    <?php if ($role === 'admin'): ?>

                                        <a
                                            href="edit_tarif.php?id=<?= (int)$t['id_tarif'] ?>"
                                            class="btn btn-primary"
                                        >
                                            Edit
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="5"
                            class="empty-data"
                        >
                            Data tarif belum tersedia.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../koneksi.php';

/* =========================================================
   CEK LOGIN
   ========================================================= */

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
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
    header("Location: ../login.php");
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
            tr.id_transaksi,
            tr.id_parkir,
            tr.waktu_keluar,
            tr.durasi_jam,
            tr.biaya_total,
            tr.id_user,
            p.plat_nomor,
            p.jenis_kendaraan,
            u.nama_lengkap
         FROM tb_transaksi AS tr

         LEFT JOIN tb_parkir AS p
            ON tr.id_parkir = p.id_parkir

         LEFT JOIN tb_user AS u
            ON tr.id_user = u.id_user

         WHERE
            p.plat_nomor LIKE '%$keyword_sql%'
            OR p.jenis_kendaraan LIKE '%$keyword_sql%'
            OR u.nama_lengkap LIKE '%$keyword_sql%'

         ORDER BY tr.id_transaksi DESC"
    );

} else {

    $query = mysqli_query(
        $koneksi,
        "SELECT
            tr.id_transaksi,
            tr.id_parkir,
            tr.waktu_keluar,
            tr.durasi_jam,
            tr.biaya_total,
            tr.id_user,
            p.plat_nomor,
            p.jenis_kendaraan,
            u.nama_lengkap
         FROM tb_transaksi AS tr

         LEFT JOIN tb_parkir AS p
            ON tr.id_parkir = p.id_parkir

         LEFT JOIN tb_user AS u
            ON tr.id_user = u.id_user

         ORDER BY tr.id_transaksi DESC"
    );
}

$transaksi = mysqli_fetch_all($query, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Data Transaksi</title>

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
                Data Transaksi
            </h1>

            <p class="page-subtitle">
                Riwayat transaksi kendaraan yang telah keluar dari parkir.
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
                        placeholder="Cari transaksi..."
                    >

                </form>

            </div>

        </div>


        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>ID Transaksi</th>
                        <th>Plat Nomor</th>
                        <th>Jenis Kendaraan</th>
                        <th>Waktu Keluar</th>
                        <th>Durasi</th>
                        <th>Biaya</th>
                        <th>Petugas</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($transaksi) > 0): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($transaksi as $t): ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td>
                                <?= (int)$t['id_transaksi'] ?>
                            </td>

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $t['plat_nomor'] ?? '-'
                                    ) ?>
                                </strong>

                            </td>

                            <td>

                                <span class="badge badge-primary">

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $t['jenis_kendaraan'] ?? '-'
                                        )
                                    ) ?>

                                </span>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $t['waktu_keluar']
                                ) ?>
                            </td>

                            <td>
                                <?= (int)$t['durasi_jam'] ?> jam
                            </td>

                            <td>

                                <strong>

                                    Rp
                                    <?= number_format(
                                        (int)$t['biaya_total'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </strong>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $t['nama_lengkap'] ?? '-'
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="8"
                            class="empty-data"
                        >
                            Belum ada transaksi.
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
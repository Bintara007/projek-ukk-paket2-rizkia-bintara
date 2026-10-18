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
            p.id_parkir,
            p.id_member,
            p.plat_nomor,
            p.warna_kendaraan,
            p.jenis_kendaraan,
            p.id_tarif,
            p.waktu_masuk,
            p.id_user_masuk,
            m.nama_member,
            u.nama_lengkap AS nama_petugas,
            t.tarif_per_jam
         FROM tb_parkir AS p

         LEFT JOIN tb_member AS m
            ON p.id_member = m.id_member

         LEFT JOIN tb_user AS u
            ON p.id_user_masuk = u.id_user

         LEFT JOIN tb_tarif AS t
            ON p.id_tarif = t.id_tarif

         LEFT JOIN tb_transaksi AS tr
            ON p.id_parkir = tr.id_parkir

         WHERE
            tr.id_parkir IS NULL
            AND (
                p.plat_nomor LIKE '%$keyword_sql%'
                OR p.warna_kendaraan LIKE '%$keyword_sql%'
                OR p.jenis_kendaraan LIKE '%$keyword_sql%'
                OR m.nama_member LIKE '%$keyword_sql%'
            )

         ORDER BY p.id_parkir DESC"
    );

} else {

    $query = mysqli_query(
        $koneksi,
        "SELECT
            p.id_parkir,
            p.id_member,
            p.plat_nomor,
            p.warna_kendaraan,
            p.jenis_kendaraan,
            p.id_tarif,
            p.waktu_masuk,
            p.id_user_masuk,
            m.nama_member,
            u.nama_lengkap AS nama_petugas,
            t.tarif_per_jam
         FROM tb_parkir AS p

         LEFT JOIN tb_member AS m
            ON p.id_member = m.id_member

         LEFT JOIN tb_user AS u
            ON p.id_user_masuk = u.id_user

         LEFT JOIN tb_tarif AS t
            ON p.id_tarif = t.id_tarif

         LEFT JOIN tb_transaksi AS tr
            ON p.id_parkir = tr.id_parkir

         WHERE tr.id_parkir IS NULL

         ORDER BY p.id_parkir DESC"
    );
}

$parkir = mysqli_fetch_all($query, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Data Parkir</title>

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
                Data Parkir
            </h1>

            <p class="page-subtitle">
                Daftar kendaraan yang sedang berada di area parkir.
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
                        placeholder="Cari kendaraan..."
                    >

                </form>

            </div>


            <div class="toolbar-right">

                <?php if ($role === 'admin' || $role === 'petugas'): ?>

                    <a
                        href="tambah_parkir.php"
                        class="btn btn-warning"
                    >
                        + Tambah Parkir
                    </a>

                <?php endif; ?>

            </div>

        </div>


        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Plat Nomor</th>
                        <th>Warna</th>
                        <th>Jenis Kendaraan</th>
                        <th>Tarif/Jam</th>
                        <th>Waktu Masuk</th>
                        <th>Petugas</th>
                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($parkir) > 0): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($parkir as $p): ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td>
                                <?= (int)$p['id_parkir'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $p['nama_member'] ?? '-'
                                ) ?>
                            </td>

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $p['plat_nomor']
                                    ) ?>
                                </strong>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $p['warna_kendaraan']
                                ) ?>
                            </td>

                            <td>

                                <span class="badge badge-primary">

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $p['jenis_kendaraan']
                                        )
                                    ) ?>

                                </span>

                            </td>

                            <td>

                                Rp
                                <?= number_format(
                                    (int)$p['tarif_per_jam'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $p['waktu_masuk']
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $p['nama_petugas'] ?? '-'
                                ) ?>
                            </td>

                            <td>

                                <div class="aksi">

                                    <?php if (
                                        $role === 'admin' ||
                                        $role === 'petugas'
                                    ): ?>

                                        <a
                                            href="proses_keluar.php?id=<?= (int)$p['id_parkir'] ?>"
                                            class="btn btn-success"
                                            onclick="return confirm('Proses kendaraan keluar?')"
                                        >
                                            Proses Keluar
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="10"
                            class="empty-data"
                        >
                            Tidak ada kendaraan yang sedang parkir.
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
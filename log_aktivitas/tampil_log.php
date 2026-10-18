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

    $keyword_sql = mysqli_real_escape_string(
        $koneksi,
        $keyword
    );

    $query = mysqli_query(
        $koneksi,
        "SELECT
            l.id_user,
            l.aktivitas,
            l.waktu_aktivitas,
            u.nama_lengkap,
            u.username,
            u.role
         FROM tb_log_aktivitas AS l

         LEFT JOIN tb_user AS u
            ON l.id_user = u.id_user

         WHERE
            l.aktivitas LIKE '%$keyword_sql%'
            OR u.nama_lengkap LIKE '%$keyword_sql%'
            OR u.username LIKE '%$keyword_sql%'

         ORDER BY l.waktu_aktivitas DESC"
    );

} else {

    $query = mysqli_query(
        $koneksi,
        "SELECT
            l.id_user,
            l.aktivitas,
            l.waktu_aktivitas,
            u.nama_lengkap,
            u.username,
            u.role
         FROM tb_log_aktivitas AS l

         LEFT JOIN tb_user AS u
            ON l.id_user = u.id_user

         ORDER BY l.waktu_aktivitas DESC"
    );
}

$log = mysqli_fetch_all($query, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Log Aktivitas</title>

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
                Log Aktivitas
            </h1>

            <p class="page-subtitle">
                Catatan aktivitas pengguna yang terjadi di dalam sistem.
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
                        placeholder="Cari aktivitas..."
                    >

                </form>

            </div>

        </div>


        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                        <th>Waktu</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($log) > 0): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($log as $l): ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $l['nama_lengkap'] ?? '-'
                                    ) ?>
                                </strong>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $l['username'] ?? '-'
                                ) ?>
                            </td>

                            <td>

                                <?php if ($l['role'] === 'admin'): ?>

                                    <span class="badge badge-primary">
                                        Admin
                                    </span>

                                <?php elseif ($l['role'] === 'petugas'): ?>

                                    <span class="badge badge-success">
                                        Petugas
                                    </span>

                                <?php elseif ($l['role'] === 'owner'): ?>

                                    <span class="badge badge-warning">
                                        Owner
                                    </span>

                                <?php else: ?>

                                    <span class="badge badge-primary">
                                        -
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $l['aktivitas']
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $l['waktu_aktivitas']
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="6"
                            class="empty-data"
                        >
                            Belum ada aktivitas yang tercatat.
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
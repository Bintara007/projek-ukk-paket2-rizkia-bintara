<?php

/*
|--------------------------------------------------------------------------
| FILE : user/tampil_user.php
|--------------------------------------------------------------------------
| Fungsi :
| - Menampilkan data user.
| - Mencari user.
| - Memfilter user berdasarkan status.
| - User tidak dapat dihapus.
| - User yang nonaktif tetap disimpan dalam database.
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../koneksi.php';


/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id_user'])) {

    header("Location: ../index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| DASHBOARD SESUAI ROLE
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$keyword = trim($_GET['keyword'] ?? '');


/*
|--------------------------------------------------------------------------
| FILTER STATUS
|--------------------------------------------------------------------------
| Pilihan :
| - semua
| - aktif
| - nonaktif
|--------------------------------------------------------------------------
*/

$filter_status = $_GET['status'] ?? 'semua';


/*
|--------------------------------------------------------------------------
| VALIDASI FILTER STATUS
|--------------------------------------------------------------------------
*/

$status_valid = [
    'semua',
    'aktif',
    'nonaktif'
];

if (!in_array($filter_status, $status_valid, true)) {

    $filter_status = 'semua';
}


/*
|--------------------------------------------------------------------------
| MEMBUAT KONDISI WHERE
|--------------------------------------------------------------------------
*/

$where = [];


/*
|--------------------------------------------------------------------------
| FILTER PENCARIAN
|--------------------------------------------------------------------------
*/

if ($keyword !== '') {

    $keyword_sql = mysqli_real_escape_string(
        $koneksi,
        $keyword
    );

    $where[] = "
        (
            nama_lengkap LIKE '%$keyword_sql%'
            OR username LIKE '%$keyword_sql%'
            OR role LIKE '%$keyword_sql%'
        )
    ";
}


/*
|--------------------------------------------------------------------------
| FILTER STATUS
|--------------------------------------------------------------------------
*/

if ($filter_status === 'aktif') {

    $where[] = "status_aktif = 1";

} elseif ($filter_status === 'nonaktif') {

    $where[] = "status_aktif = 0";
}


/*
|--------------------------------------------------------------------------
| GABUNG KONDISI
|--------------------------------------------------------------------------
*/

$where_sql = '';

if (count($where) > 0) {

    $where_sql = 'WHERE ' . implode(' AND ', $where);
}


/*
|--------------------------------------------------------------------------
| QUERY DATA USER
|--------------------------------------------------------------------------
| User aktif ditampilkan terlebih dahulu.
| User nonaktif berada setelah user aktif.
|--------------------------------------------------------------------------
*/

$query = mysqli_query(
    $koneksi,
    "
    SELECT
        id_user,
        nama_lengkap,
        username,
        role,
        status_aktif
    FROM tb_user

    $where_sql

    ORDER BY
        CASE
            WHEN status_aktif = 'aktif' THEN 1
            ELSE 2
        END,
        id_user DESC
    "
);


/*
|--------------------------------------------------------------------------
| CEK QUERY
|--------------------------------------------------------------------------
*/

if (!$query) {

    die('Gagal mengambil data user: ' .
        mysqli_error($koneksi));
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA
|--------------------------------------------------------------------------
*/

$user = mysqli_fetch_all(
    $query,
    MYSQLI_ASSOC
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Data User</title>

    <link
        rel="stylesheet"
        href="../assets/css/tabel.css">

</head>

<body>

    <div class="container">


        <!--
    ==========================================================================
    HEADER
    ==========================================================================
    -->

        <div class="page-header">

            <div>

                <h1 class="page-title">
                    Data User
                </h1>

                <p class="page-subtitle">
                    Kelola pengguna dan hak akses sistem parkir.
                </p>

            </div>


            <a
                href="<?= htmlspecialchars($dashboard) ?>"
                class="btn-kembali">
                ← Kembali ke Dashboard
            </a>

        </div>


        <!--
    ==========================================================================
    CARD
    ==========================================================================
    -->

        <div class="table-card">


            <!--
        ======================================================================
        TOOLBAR
        ======================================================================
        -->

            <div class="table-toolbar">


                <div class="toolbar-left">

                    <!-- SEARCH -->

                    <form
                        method="GET"
                        class="search-box">

                        <input
                            type="text"
                            name="keyword"
                            value="<?= htmlspecialchars($keyword) ?>"
                            placeholder="Cari user...">


                        <!--
                    Mempertahankan filter status
                    saat melakukan pencarian.
                    -->

                        <input
                            type="hidden"
                            name="status"
                            value="<?= htmlspecialchars($filter_status) ?>">

                    </form>


                    <!--
                ==============================================================
                FILTER STATUS
                ==============================================================
                -->

                    <form
                        method="GET"
                        class="search-box">

                        <input
                            type="hidden"
                            name="keyword"
                            value="<?= htmlspecialchars($keyword) ?>">

                        <select
                            name="status"
                            onchange="this.form.submit()"
                            style="
                            height: 42px;
                            min-width: 160px;
                            padding: 0 12px;
                            border: 1px solid #cbd5e1;
                            border-radius: 8px;
                            background: #ffffff;
                            color: #334155;
                            font-size: 14px;
                            outline: none;
                            cursor: pointer;
                        ">

                            <option
                                value="semua"
                                <?= $filter_status === 'semua'
                                    ? 'selected'
                                    : '' ?>>
                                Semua Status
                            </option>

                            <option
                                value="aktif"
                                <?= $filter_status === 'aktif'
                                    ? 'selected'
                                    : '' ?>>
                                Aktif
                            </option>

                            <option
                                value="nonaktif"
                                <?= $filter_status === 'nonaktif'
                                    ? 'selected'
                                    : '' ?>>
                                Nonaktif
                            </option>

                        </select>

                    </form>

                </div>


                <div class="toolbar-right">

                    <?php if ($role === 'admin'): ?>

                        <a
                            href="tambah_user.php"
                            class="btn btn-warning">
                            + Tambah User
                        </a>

                    <?php endif; ?>

                </div>

            </div>


            <!--
        ======================================================================
        INFORMASI FILTER
        ======================================================================
        -->

            <?php if ($filter_status !== 'semua'): ?>

                <div
                    style="
                    margin: 0 20px 15px;
                    padding: 11px 14px;
                    background: #eff6ff;
                    border: 1px solid #bfdbfe;
                    border-radius: 8px;
                    color: #1e40af;
                    font-size: 13px;
                ">

                    Menampilkan user dengan status:

                    <strong>
                        <?= htmlspecialchars(
                            ucfirst($filter_status)
                        ) ?>
                    </strong>

                </div>

            <?php endif; ?>


            <!--
        ======================================================================
        TABEL
        ======================================================================
        -->

            <div class="table-responsive">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>ID</th>

                            <th>Nama Lengkap</th>

                            <th>Username</th>

                            <th>Role</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (count($user) > 0): ?>

                            <?php $no = 1; ?>


                            <?php foreach ($user as $u): ?>

                                <tr>


                                    <td>
                                        <?= $no++ ?>
                                    </td>


                                    <td>
                                        <?= (int)$u['id_user'] ?>
                                    </td>


                                    <td>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $u['nama_lengkap']
                                            ) ?>
                                        </strong>

                                    </td>


                                    <td>
                                        <?= htmlspecialchars(
                                            $u['username']
                                        ) ?>
                                    </td>


                                    <td>

                                        <?php if ($u['role'] === 'admin'): ?>

                                            <span class="badge badge-primary">
                                                Admin
                                            </span>

                                        <?php elseif ($u['role'] === 'petugas'): ?>

                                            <span class="badge badge-success">
                                                Petugas
                                            </span>

                                        <?php else: ?>

                                            <span class="badge badge-warning">
                                                Owner
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <?php if ((int)$u['status_aktif'] === 1): ?>

                                            <span class="badge badge-success">
                                                Aktif
                                            </span>

                                        <?php else: ?>

                                            <span class="badge badge-danger">
                                                Nonaktif
                                            </span>

                                        <?php endif; ?>
                                    </td>


                                    <td>

                                        <div class="aksi">

                                            <?php if ($role === 'admin'): ?>

                                                <a
                                                    href="edit_user.php?id=<?= (int)$u['id_user'] ?>"
                                                    class="btn btn-primary">
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
                                    colspan="7"
                                    class="empty-data">
                                    Tidak ada data user dengan filter tersebut.
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
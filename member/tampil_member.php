<?php

/*
|--------------------------------------------------------------------------
| FILE : member/tampil_member.php
|--------------------------------------------------------------------------
| Fungsi :
| - Menampilkan data member.
| - Mencari member.
| - Memfilter member berdasarkan status.
| - Member tidak dapat dihapus.
| - Member suspended tetap disimpan.
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

    header("Location: ../login.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| DASHBOARD
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

    header("Location: ../login.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$keyword = trim(
    $_GET['keyword'] ?? ''
);


/*
|--------------------------------------------------------------------------
| FILTER STATUS
|--------------------------------------------------------------------------
*/

$filter_status = $_GET['status'] ?? 'semua';


/*
|--------------------------------------------------------------------------
| STATUS VALID
|--------------------------------------------------------------------------
*/

$status_valid = [
    'semua',
    'aktif',
    'suspended'
];

if (!in_array(
    $filter_status,
    $status_valid,
    true
)) {

    $filter_status = 'semua';
}


/*
|--------------------------------------------------------------------------
| KONDISI QUERY
|--------------------------------------------------------------------------
*/

$where = [];


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if ($keyword !== '') {

    $keyword_sql = mysqli_real_escape_string(
        $koneksi,
        $keyword
    );

    $where[] = "
        (
            m.nama_member LIKE '%$keyword_sql%'
            OR m.no_hp LIKE '%$keyword_sql%'
            OR m.jenis_kelamin LIKE '%$keyword_sql%'
            OR a.nama_area LIKE '%$keyword_sql%'
        )
    ";
}


/*
|--------------------------------------------------------------------------
| FILTER STATUS
|--------------------------------------------------------------------------
*/

if ($filter_status === 'aktif') {

    $where[] = "m.status_aktif = 'aktif'";

} elseif ($filter_status === 'suspended') {

    $where[] = "m.status_aktif = 'suspended'";
}


/*
|--------------------------------------------------------------------------
| WHERE SQL
|--------------------------------------------------------------------------
*/

$where_sql = '';

if (count($where) > 0) {

    $where_sql =
        'WHERE ' .
        implode(' AND ', $where);
}


/*
|--------------------------------------------------------------------------
| QUERY MEMBER
|--------------------------------------------------------------------------
| Member aktif ditempatkan terlebih dahulu.
| Suspended ditempatkan setelahnya.
|--------------------------------------------------------------------------
*/

$query = mysqli_query(
    $koneksi,
    "
    SELECT
        m.id_member,
        m.nama_member,
        m.jenis_kelamin,
        m.no_hp,
        m.tanggal_daftar,
        m.status_aktif,
        u.nama_lengkap AS nama_user,
        a.nama_area

    FROM tb_member AS m

    LEFT JOIN tb_user AS u
        ON m.id_user = u.id_user

    LEFT JOIN tb_area_parkir AS a
        ON m.id_area = a.id_area

    $where_sql

    ORDER BY
        CASE
            WHEN m.status_aktif = 'aktif' THEN 1
            ELSE 2
        END,
        m.id_member DESC
    "
);


/*
|--------------------------------------------------------------------------
| CEK QUERY
|--------------------------------------------------------------------------
*/

if (!$query) {

    die(
        'Gagal mengambil data member: ' .
        mysqli_error($koneksi)
    );
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA
|--------------------------------------------------------------------------
*/

$member = mysqli_fetch_all(
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
        content="width=device-width, initial-scale=1.0"
    >

    <title>Data Member</title>

    <link
        rel="stylesheet"
        href="../assets/css/tabel.css"
    >

</head>

<body>

<div class="container">


    <!-- HEADER -->

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Data Member
            </h1>

            <p class="page-subtitle">
                Kelola data member yang terdaftar pada sistem parkir.
            </p>

        </div>


        <a
            href="<?= htmlspecialchars($dashboard) ?>"
            class="btn-kembali"
        >
            ← Kembali ke Dashboard
        </a>

    </div>


    <!-- CARD -->

    <div class="table-card">


        <!-- TOOLBAR -->

        <div class="table-toolbar">


            <div class="toolbar-left">


                <!-- SEARCH -->

                <form
                    method="GET"
                    class="search-box"
                >

                    <input
                        type="text"
                        name="keyword"
                        value="<?= htmlspecialchars($keyword) ?>"
                        placeholder="Cari member..."
                    >

                    <input
                        type="hidden"
                        name="status"
                        value="<?= htmlspecialchars($filter_status) ?>"
                    >

                </form>


                <!-- FILTER STATUS -->

                <form
                    method="GET"
                    class="search-box"
                >

                    <input
                        type="hidden"
                        name="keyword"
                        value="<?= htmlspecialchars($keyword) ?>"
                    >

                    <select
                        name="status"
                        onchange="this.form.submit()"
                        style="
                            height: 42px;
                            min-width: 170px;
                            padding: 0 12px;
                            border: 1px solid #cbd5e1;
                            border-radius: 8px;
                            background: #ffffff;
                            color: #334155;
                            font-size: 14px;
                            outline: none;
                            cursor: pointer;
                        "
                    >

                        <option
                            value="semua"
                            <?= $filter_status === 'semua'
                                ? 'selected'
                                : '' ?>
                        >
                            Semua Status
                        </option>

                        <option
                            value="aktif"
                            <?= $filter_status === 'aktif'
                                ? 'selected'
                                : '' ?>
                        >
                            Aktif
                        </option>

                        <option
                            value="suspended"
                            <?= $filter_status === 'suspended'
                                ? 'selected'
                                : '' ?>
                        >
                            Suspended
                        </option>

                    </select>

                </form>

            </div>


            <div class="toolbar-right">

                <?php if ($role === 'admin'): ?>

                    <a
                        href="tambah_member.php"
                        class="btn btn-warning"
                    >
                        + Tambah Member
                    </a>

                <?php endif; ?>

            </div>

        </div>


        <!-- INFORMASI FILTER -->

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
                "
            >

                Menampilkan member dengan status:

                <strong>
                    <?= htmlspecialchars(
                        ucfirst($filter_status)
                    ) ?>
                </strong>

            </div>

        <?php endif; ?>


        <!-- TABEL -->

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>ID</th>
                        <th>Nama Member</th>
                        <th>Jenis Kelamin</th>
                        <th>No. HP</th>
                        <th>Tanggal Daftar</th>
                        <th>User</th>
                        <th>Area</th>
                        <th>Status</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (count($member) > 0): ?>

                    <?php $no = 1; ?>


                    <?php foreach ($member as $m): ?>

                        <tr>


                            <td>
                                <?= $no++ ?>
                            </td>


                            <td>
                                <?= (int)$m['id_member'] ?>
                            </td>


                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $m['nama_member']
                                    ) ?>
                                </strong>

                            </td>


                            <td>

                                <?= $m['jenis_kelamin'] === 'L'
                                    ? 'Laki-laki'
                                    : 'Perempuan'
                                ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $m['no_hp'] ?? '-'
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $m['tanggal_daftar']
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $m['nama_user'] ?? '-'
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $m['nama_area'] ?? '-'
                                ) ?>

                            </td>


                            <td>

                                <?php if ($m['status_aktif'] === 'aktif'): ?>

                                    <span class="badge badge-success">
                                        Aktif
                                    </span>

                                <?php else: ?>

                                    <span class="badge badge-warning">
                                        Suspended
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <div class="aksi">

                                    <?php if ($role === 'admin'): ?>

                                        <a
                                            href="edit_member.php?id=<?= (int)$m['id_member'] ?>"
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
                            colspan="10"
                            class="empty-data"
                        >
                            Tidak ada data member dengan filter tersebut.
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
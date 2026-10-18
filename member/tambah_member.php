<?php
/*
|--------------------------------------------------------------------------
| FILE : tambah_member.php
|--------------------------------------------------------------------------
| Fungsi :
| - Menampilkan form tambah member.
| - Menyimpan data member baru ke tb_member.
| - Hanya admin yang dapat menambahkan member.
| - id_user otomatis mengambil user yang sedang login.
| - Data aktivitas dicatat ke tb_log_aktivitas.
|--------------------------------------------------------------------------
*/

// Memulai session.
session_start();

// Menghubungkan ke database.
include '../koneksi.php';


/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
| User harus login terlebih dahulu.
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id_user'])) {

    header("Location: ../index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| HANYA ADMIN
|--------------------------------------------------------------------------
| Pengelolaan data member hanya dilakukan oleh admin.
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {

    header("Location: ../index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| ID USER YANG SEDANG LOGIN
|--------------------------------------------------------------------------
| ID ini digunakan untuk mengetahui admin yang menambahkan member.
|--------------------------------------------------------------------------
*/

$id_user_login = (int) $_SESSION['id_user'];


/*
|--------------------------------------------------------------------------
| VARIABEL ERROR
|--------------------------------------------------------------------------
*/

$error = '';


/*
|--------------------------------------------------------------------------
| DATA AREA PARKIR
|--------------------------------------------------------------------------
| Digunakan untuk menampilkan pilihan area pada form.
|--------------------------------------------------------------------------
*/

$query_area = mysqli_query(
    $koneksi,
    "
    SELECT
        id_area,
        nama_area
    FROM tb_area_parkir
    ORDER BY nama_area ASC
    "
);


/*
|--------------------------------------------------------------------------
| PROSES TAMBAH MEMBER
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA DARI FORM
    |--------------------------------------------------------------------------
    */

    $nama_member = trim($_POST['nama_member'] ?? '');

    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';

    $no_hp = trim($_POST['no_hp'] ?? '');

    $tanggal_daftar = $_POST['tanggal_daftar'] ?? '';

    $id_area = (int) ($_POST['id_area'] ?? 0);


    /*
    |--------------------------------------------------------------------------
    | STATUS OTOMATIS AKTIF
    |--------------------------------------------------------------------------
    | User tidak perlu memilih status saat menambahkan member.
    |--------------------------------------------------------------------------
    */

    $status_aktif = 'aktif';


    /*
    |--------------------------------------------------------------------------
    | VALIDASI DATA
    |--------------------------------------------------------------------------
    */

    if (
        $nama_member === '' ||
        $jenis_kelamin === '' ||
        $tanggal_daftar === '' ||
        $id_area <= 0
    ) {

        $error = 'Nama member, jenis kelamin, tanggal daftar, dan area wajib diisi.';
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI JENIS KELAMIN
    |--------------------------------------------------------------------------
    */

    if (
        $error === '' &&
        !in_array($jenis_kelamin, ['L', 'P'], true)
    ) {

        $error = 'Jenis kelamin tidak valid.';
    }


    /*
    |--------------------------------------------------------------------------
    | CEK AREA PARKIR
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        $stmt_area = mysqli_prepare(
            $koneksi,
            "
            SELECT id_area
            FROM tb_area_parkir
            WHERE id_area = ?
            LIMIT 1
            "
        );

        mysqli_stmt_bind_param(
            $stmt_area,
            "i",
            $id_area
        );

        mysqli_stmt_execute($stmt_area);

        $result_area = mysqli_stmt_get_result($stmt_area);


        /*
        |--------------------------------------------------------------------------
        | JIKA AREA TIDAK DITEMUKAN
        |--------------------------------------------------------------------------
        */

        if (mysqli_num_rows($result_area) === 0) {

            $error = 'Area parkir yang dipilih tidak ditemukan.';
        }

        mysqli_stmt_close($stmt_area);
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN MEMBER
    |--------------------------------------------------------------------------
    */

    if ($error === '') {


        /*
        |--------------------------------------------------------------------------
        | PREPARE INSERT
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare(
            $koneksi,
            "
            INSERT INTO tb_member
            (
                nama_member,
                jenis_kelamin,
                no_hp,
                tanggal_daftar,
                status_aktif,
                id_user,
                id_area
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
            "
        );


        /*
        |--------------------------------------------------------------------------
        | BIND PARAMETER
        |--------------------------------------------------------------------------
        | s = string
        | i = integer
        |--------------------------------------------------------------------------
        */

        mysqli_stmt_bind_param(
            $stmt,
            "sssssii",
            $nama_member,
            $jenis_kelamin,
            $no_hp,
            $tanggal_daftar,
            $status_aktif,
            $id_user_login,
            $id_area
        );


        /*
        |--------------------------------------------------------------------------
        | EKSEKUSI INSERT
        |--------------------------------------------------------------------------
        */

        if (mysqli_stmt_execute($stmt)) {


            /*
            |--------------------------------------------------------------------------
            | LOG AKTIVITAS
            |--------------------------------------------------------------------------
            */

            $aktivitas =
                "Menambahkan member: " .
                $nama_member;


            $stmt_log = mysqli_prepare(
                $koneksi,
                "
                INSERT INTO tb_log_aktivitas
                (
                    id_user,
                    aktivitas,
                    waktu_aktivitas
                )
                VALUES (?, ?, NOW())
                "
            );


            /*
            |--------------------------------------------------------------------------
            | SIMPAN LOG
            |--------------------------------------------------------------------------
            */

            if ($stmt_log) {

                mysqli_stmt_bind_param(
                    $stmt_log,
                    "is",
                    $id_user_login,
                    $aktivitas
                );

                mysqli_stmt_execute($stmt_log);

                mysqli_stmt_close($stmt_log);
            }


            /*
            |--------------------------------------------------------------------------
            | TUTUP STATEMENT
            |--------------------------------------------------------------------------
            */

            mysqli_stmt_close($stmt);


            /*
            |--------------------------------------------------------------------------
            | REDIRECT SETELAH BERHASIL
            |--------------------------------------------------------------------------
            */

            header(
                "Location: tampil_member.php?status=tambah"
            );

            exit;


        } else {

            /*
            |--------------------------------------------------------------------------
            | JIKA GAGAL INSERT
            |--------------------------------------------------------------------------
            */

            $error =
                'Gagal menambahkan member: ' .
                mysqli_error($koneksi);


            mysqli_stmt_close($stmt);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>

    <!-- Pengaturan karakter -->
    <meta charset="UTF-8">

    <!-- Responsive -->
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <!-- Judul halaman -->
    <title>Tambah Member</title>


    <!--
    --------------------------------------------------------------------------
    CSS FORM GLOBAL
    --------------------------------------------------------------------------
    CSS ini sama dengan yang digunakan oleh form tambah/edit lainnya.
    -->

    <link
        rel="stylesheet"
        href="../assets/css/form.css"
    >

</head>


<body>


<div class="container">

    <div class="form-card">


        <!--
        ======================================================================
        HEADER FORM
        ======================================================================
        -->

        <div class="form-header">

            <h2>
                Tambah Member
            </h2>

            <p>
                Menambahkan member baru ke sistem parkir.
            </p>

        </div>


        <!--
        ======================================================================
        TOOLBAR
        ======================================================================
        -->

        <div class="table-toolbar">

            <div class="toolbar-left">

                <?php

                /*
                ------------------------------------------------------------------
                LINK DASHBOARD
                ------------------------------------------------------------------
                */

                $dashboard_url = "../dashboard_admin.php";

                ?>

                <a
                    href="<?= htmlspecialchars($dashboard_url) ?>"
                    class="btn btn-primary"
                >
                    Dashboard
                </a>

            </div>

        </div>


        <!--
        ======================================================================
        FORM BODY
        ======================================================================
        -->

        <div class="form-body">


            <!--
            ==================================================================
            PESAN ERROR
            ==================================================================
            -->

            <?php if ($error !== ''): ?>

                <div class="form-alert form-alert-error">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!--
            ==================================================================
            INFO BOX
            ==================================================================
            -->

            <div class="info-box">

                Member baru akan otomatis memiliki status
                <strong>Aktif</strong>.

            </div>


            <!--
            ==================================================================
            FORM
            ==================================================================
            -->

            <form method="POST">


                <div class="form-grid">


                    <!--
                    ==========================================================
                    NAMA MEMBER
                    ==========================================================
                    -->

                    <div class="form-group">

                        <label>

                            Nama Member
                            <span class="required">*</span>

                        </label>

                        <input
                            type="text"
                            name="nama_member"
                            value="<?= htmlspecialchars($_POST['nama_member'] ?? '') ?>"
                            placeholder="Masukkan nama member"
                            required
                        >

                    </div>


                    <!--
                    ==========================================================
                    JENIS KELAMIN
                    ==========================================================
                    -->

                    <div class="form-group">

                        <label>

                            Jenis Kelamin
                            <span class="required">*</span>

                        </label>

                        <select
                            name="jenis_kelamin"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option
                                value="L"
                                <?= (($_POST['jenis_kelamin'] ?? '') === 'L')
                                    ? 'selected'
                                    : '' ?>
                            >
                                Laki-laki
                            </option>

                            <option
                                value="P"
                                <?= (($_POST['jenis_kelamin'] ?? '') === 'P')
                                    ? 'selected'
                                    : '' ?>
                            >
                                Perempuan
                            </option>

                        </select>

                    </div>


                    <!--
                    ==========================================================
                    NOMOR HP
                    ==========================================================
                    -->

                    <div class="form-group">

                        <label>
                            Nomor HP
                        </label>

                        <input
                            type="text"
                            name="no_hp"
                            value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>"
                            placeholder="Contoh: 081234567890"
                        >

                        <span class="form-help">
                            Nomor HP boleh dikosongkan.
                        </span>

                    </div>


                    <!--
                    ==========================================================
                    TANGGAL DAFTAR
                    ==========================================================
                    -->

                    <div class="form-group">

                        <label>

                            Tanggal Daftar
                            <span class="required">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_daftar"
                            value="<?= htmlspecialchars(
                                $_POST['tanggal_daftar'] ?? date('Y-m-d')
                            ) ?>"
                            required
                        >

                    </div>


                    <!--
                    ==========================================================
                    STATUS
                    ==========================================================
                    -->

                    <div class="form-group">

                        <label>
                            Status
                        </label>

                        <input
                            type="text"
                            value="Aktif"
                            readonly
                        >

                        <span class="form-help">
                            Status member baru otomatis aktif.
                        </span>

                    </div>


                    <!--
                    ==========================================================
                    AREA PARKIR
                    ==========================================================
                    -->

                    <div class="form-group">

                        <label>

                            Area Parkir
                            <span class="required">*</span>

                        </label>

                        <select
                            name="id_area"
                            required
                        >

                            <option value="">
                                -- Pilih Area Parkir --
                            </option>


                            <?php while ($area = mysqli_fetch_assoc($query_area)): ?>

                                <option
                                    value="<?= (int) $area['id_area'] ?>"
                                    <?= (
                                        (int)($_POST['id_area'] ?? 0)
                                        ===
                                        (int)$area['id_area']
                                    )
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $area['nama_area']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                </div>


                <!--
                ==================================================================
                TOMBOL FORM
                ==================================================================
                -->

                <div class="form-actions">


                    <!-- Tombol kembali -->

                    <a
                        href="tampil_member.php"
                        class="btn btn-secondary"
                    >
                        Kembali
                    </a>


                    <!-- Tombol simpan -->

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan Member
                    </button>


                </div>


            </form>


        </div>

    </div>

</div>


</body>

</html>
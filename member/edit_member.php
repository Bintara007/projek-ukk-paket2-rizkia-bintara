<?php
/*
|--------------------------------------------------------------------------
| FILE : edit_member.php
|--------------------------------------------------------------------------
| Fungsi :
| - Menampilkan data member berdasarkan id_member.
| - Mengubah data member.
| - Hanya admin yang dapat mengedit member.
| - Mencatat aktivitas perubahan ke tb_log_aktivitas.
|--------------------------------------------------------------------------
*/

// Memulai session.
session_start();

// Menghubungkan database.
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
| HANYA ADMIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {

    header("Location: ../login.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| ID USER YANG SEDANG LOGIN
|--------------------------------------------------------------------------
*/

$id_user_login = (int) $_SESSION['id_user'];


/*
|--------------------------------------------------------------------------
| AMBIL ID MEMBER
|--------------------------------------------------------------------------
| Contoh URL:
|
| edit_member.php?id=5
|--------------------------------------------------------------------------
*/

$id_member = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


/*
|--------------------------------------------------------------------------
| VALIDASI ID MEMBER
|--------------------------------------------------------------------------
*/

if (!$id_member || $id_member <= 0) {

    header("Location: tampil_member.php?error=id");

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA MEMBER
|--------------------------------------------------------------------------
*/

$stmt_member = mysqli_prepare(
    $koneksi,
    "
    SELECT
        id_member,
        nama_member,
        jenis_kelamin,
        no_hp,
        tanggal_daftar,
        status_aktif,
        id_area
    FROM tb_member
    WHERE id_member = ?
    LIMIT 1
    "
);


mysqli_stmt_bind_param(
    $stmt_member,
    "i",
    $id_member
);


mysqli_stmt_execute($stmt_member);


$result_member = mysqli_stmt_get_result(
    $stmt_member
);


$member = mysqli_fetch_assoc(
    $result_member
);


mysqli_stmt_close($stmt_member);


/*
|--------------------------------------------------------------------------
| CEK DATA MEMBER
|--------------------------------------------------------------------------
*/

if (!$member) {

    header("Location: tampil_member.php?error=notfound");

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA AREA PARKIR
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
| VARIABEL ERROR
|--------------------------------------------------------------------------
*/

$error = '';


/*
|--------------------------------------------------------------------------
| PROSES EDIT MEMBER
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA FORM
    |--------------------------------------------------------------------------
    */

    $nama_member = trim(
        $_POST['nama_member'] ?? ''
    );

    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';

    $no_hp = trim(
        $_POST['no_hp'] ?? ''
    );

    $tanggal_daftar = $_POST['tanggal_daftar'] ?? '';

    $status_aktif = $_POST['status_aktif'] ?? '';

    $id_area = (int) (
        $_POST['id_area'] ?? 0
    );


    /*
    |--------------------------------------------------------------------------
    | VALIDASI DATA
    |--------------------------------------------------------------------------
    */

    if (
        $nama_member === '' ||
        $jenis_kelamin === '' ||
        $tanggal_daftar === '' ||
        $status_aktif === '' ||
        $id_area <= 0
    ) {

        $error =
            'Data member yang wajib diisi belum lengkap.';
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI JENIS KELAMIN
    |--------------------------------------------------------------------------
    */

    if (
        $error === '' &&
        !in_array(
            $jenis_kelamin,
            ['L', 'P'],
            true
        )
    ) {

        $error =
            'Jenis kelamin tidak valid.';
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI STATUS
    |--------------------------------------------------------------------------
    */

    if (
        $error === '' &&
        !in_array(
            $status_aktif,
            ['aktif', 'suspended'],
            true
        )
    ) {

        $error =
            'Status member tidak valid.';
    }


    /*
    |--------------------------------------------------------------------------
    | CEK AREA
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


        mysqli_stmt_execute(
            $stmt_area
        );


        $result_area = mysqli_stmt_get_result(
            $stmt_area
        );


        if (
            mysqli_num_rows($result_area)
            === 0
        ) {

            $error =
                'Area parkir yang dipilih tidak ditemukan.';
        }


        mysqli_stmt_close(
            $stmt_area
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATA MEMBER
    |--------------------------------------------------------------------------
    */

    if ($error === '') {


        /*
        ----------------------------------------------------------------------
        | PREPARE UPDATE
        ----------------------------------------------------------------------
        */

        $stmt = mysqli_prepare(
            $koneksi,
            "
            UPDATE tb_member
            SET
                nama_member = ?,
                jenis_kelamin = ?,
                no_hp = ?,
                tanggal_daftar = ?,
                status_aktif = ?,
                id_area = ?
            WHERE id_member = ?
            "
        );


        /*
        ----------------------------------------------------------------------
        | BIND PARAMETER
        ----------------------------------------------------------------------
        */

        mysqli_stmt_bind_param(
            $stmt,
            "sssssii",
            $nama_member,
            $jenis_kelamin,
            $no_hp,
            $tanggal_daftar,
            $status_aktif,
            $id_area,
            $id_member
        );


        /*
        ----------------------------------------------------------------------
        | EKSEKUSI UPDATE
        ----------------------------------------------------------------------
        */

        if (mysqli_stmt_execute($stmt)) {


            /*
            ------------------------------------------------------------------
            | LOG AKTIVITAS
            ------------------------------------------------------------------
            */

            $aktivitas =
                "Mengubah member: " .
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
            ------------------------------------------------------------------
            | SIMPAN LOG
            ------------------------------------------------------------------
            */

            if ($stmt_log) {

                mysqli_stmt_bind_param(
                    $stmt_log,
                    "is",
                    $id_user_login,
                    $aktivitas
                );


                mysqli_stmt_execute(
                    $stmt_log
                );


                mysqli_stmt_close(
                    $stmt_log
                );
            }


            /*
            ------------------------------------------------------------------
            | TUTUP STATEMENT
            ------------------------------------------------------------------
            */

            mysqli_stmt_close($stmt);


            /*
            ------------------------------------------------------------------
            | REDIRECT
            ------------------------------------------------------------------
            */

            header(
                "Location: tampil_member.php?status=edit"
            );

            exit;


        } else {


            /*
            ------------------------------------------------------------------
            | ERROR UPDATE
            ------------------------------------------------------------------
            */

            $error =
                'Gagal mengubah member: ' .
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


    <!-- Judul -->
    <title>Edit Member</title>


    <!--
    --------------------------------------------------------------------------
    CSS FORM GLOBAL
    --------------------------------------------------------------------------
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
        HEADER
        ======================================================================
        -->

        <div class="form-header">

            <h2>
                Edit Member
            </h2>

            <p>
                Mengubah informasi data member.
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
                | LINK DASHBOARD
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
            ERROR
            ==================================================================
            -->

            <?php if ($error !== ''): ?>

                <div class="form-alert form-alert-error">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!--
            ==================================================================
            INFO
            ==================================================================
            -->

            <div class="info-box">

                Perbarui data member sesuai informasi terbaru.

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
                            value="<?= htmlspecialchars(
                                $_POST['nama_member']
                                ?? $member['nama_member']
                            ) ?>"
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
                                <?= (
                                    ($_POST['jenis_kelamin']
                                    ?? $member['jenis_kelamin'])
                                    === 'L'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Laki-laki
                            </option>


                            <option
                                value="P"
                                <?= (
                                    ($_POST['jenis_kelamin']
                                    ?? $member['jenis_kelamin'])
                                    === 'P'
                                )
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
                            value="<?= htmlspecialchars(
                                $_POST['no_hp']
                                ?? ($member['no_hp'] ?? '')
                            ) ?>"
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
                                $_POST['tanggal_daftar']
                                ?? $member['tanggal_daftar']
                            ) ?>"
                            required
                        >

                    </div>


                    <!--
                    ==========================================================
                    STATUS MEMBER
                    ==========================================================
                    -->

                    <div class="form-group">

                        <label>

                            Status
                            <span class="required">*</span>

                        </label>


                        <select
                            name="status_aktif"
                            required
                        >

                            <option
                                value="aktif"
                                <?= (
                                    ($_POST['status_aktif']
                                    ?? $member['status_aktif'])
                                    === 'aktif'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Aktif
                            </option>


                            <option
                                value="suspended"
                                <?= (
                                    ($_POST['status_aktif']
                                    ?? $member['status_aktif'])
                                    === 'suspended'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Suspended
                            </option>

                        </select>

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
                                        (int)(
                                            $_POST['id_area']
                                            ?? $member['id_area']
                                        )
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
                TOMBOL
                ==================================================================
                -->

                <div class="form-actions">


                    <!-- Kembali -->

                    <a
                        href="tampil_member.php"
                        class="btn btn-secondary"
                    >
                        Kembali
                    </a>


                    <!-- Update -->

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Member
                    </button>


                </div>


            </form>


        </div>

    </div>

</div>


</body>

</html>
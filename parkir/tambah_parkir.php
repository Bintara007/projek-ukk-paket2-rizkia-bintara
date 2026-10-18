<?php
session_start();

require_once "../koneksi.php";

/*
|--------------------------------------------------------------------------
| CEK KONEKSI DATABASE
|--------------------------------------------------------------------------
*/
if (!isset($koneksi) || !$koneksi) {
    die("Koneksi database gagal.");
}

/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA SESSION
|--------------------------------------------------------------------------
*/
$username = trim($_SESSION['username']);
$id_user  = isset($_SESSION['id_user']) ? (int) $_SESSION['id_user'] : 0;
$role     = strtolower(trim($_SESSION['role'] ?? ''));

/*
|--------------------------------------------------------------------------
| JIKA SESSION ROLE TIDAK ADA
|--------------------------------------------------------------------------
| Ambil role langsung dari tb_user berdasarkan username.
| Ini mencegah petugas dilempar ke dashboard hanya karena
| session role belum tersimpan dengan benar.
|--------------------------------------------------------------------------
*/
if ($role === '' || $id_user <= 0) {

    $stmt_user = mysqli_prepare(
        $koneksi,
        "
        SELECT
            id_user,
            username,
            nama_lengkap,
            role,
            status_aktif
        FROM tb_user
        WHERE username = ?
        LIMIT 1
        "
    );

    if ($stmt_user) {

        mysqli_stmt_bind_param(
            $stmt_user,
            "s",
            $username
        );

        mysqli_stmt_execute($stmt_user);

        $result_user = mysqli_stmt_get_result($stmt_user);

        if ($result_user && mysqli_num_rows($result_user) > 0) {

            $user_session = mysqli_fetch_assoc($result_user);

            $id_user = (int) $user_session['id_user'];
            $role    = strtolower(trim($user_session['role']));

            /*
            | Simpan kembali ke session
            */
            $_SESSION['id_user']      = $id_user;
            $_SESSION['username']     = $user_session['username'];
            $_SESSION['nama_lengkap'] = $user_session['nama_lengkap'];
            $_SESSION['role']         = $user_session['role'];

        }

        mysqli_stmt_close($stmt_user);
    }
}

/*
|--------------------------------------------------------------------------
| CEK ROLE
|--------------------------------------------------------------------------
| Hanya ADMIN dan PETUGAS yang boleh menambah parkir.
|--------------------------------------------------------------------------
*/
if ($role !== 'admin' && $role !== 'petugas') {

    /*
    | Kalau role tidak ditemukan, kembali ke login.
    */
    if ($role === '') {
        header("Location: ../login.php");
        exit;
    }

    /*
    | Kalau role memang owner, kembali ke dashboard owner.
    */
    if ($role === 'owner') {
        header("Location: ../dashboard_owner.php");
        exit;
    }

    /*
    | Role lain yang tidak dikenali.
    */
    header("Location: ../dashboard_petugas.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| DATA USER AKTIF
|--------------------------------------------------------------------------
*/
$nama_user = $_SESSION['nama_lengkap'] ?? $username;

/*
|--------------------------------------------------------------------------
| VARIABEL
|--------------------------------------------------------------------------
*/
$error = '';

$old_id_member       = '';
$old_id_area         = '';
$old_plat_nomor      = '';
$old_warna_kendaraan = '';
$old_id_tarif        = '';

/*
|--------------------------------------------------------------------------
| PROSES SIMPAN
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |---------------------------------------------------------------
    | Ambil data form
    |---------------------------------------------------------------
    */
    $old_id_member       = isset($_POST['id_member']) ? trim($_POST['id_member']) : '';
    $old_id_area         = isset($_POST['id_area']) ? trim($_POST['id_area']) : '';
    $old_plat_nomor      = isset($_POST['plat_nomor']) ? trim($_POST['plat_nomor']) : '';
    $old_warna_kendaraan = isset($_POST['warna_kendaraan']) ? trim($_POST['warna_kendaraan']) : '';
    $old_id_tarif        = isset($_POST['id_tarif']) ? trim($_POST['id_tarif']) : '';

    /*
    |---------------------------------------------------------------
    | Member kosong dianggap NULL
    |---------------------------------------------------------------
    */
    $id_member = ($old_id_member !== '')
        ? (int) $old_id_member
        : null;

    /*
    |---------------------------------------------------------------
    | Area wajib dipilih
    |---------------------------------------------------------------
    */
    $id_area = (int) $old_id_area;

    /*
    |---------------------------------------------------------------
    | Tarif wajib dipilih
    |---------------------------------------------------------------
    */
    $id_tarif = (int) $old_id_tarif;

    /*
    |---------------------------------------------------------------
    | Normalisasi plat nomor
    |---------------------------------------------------------------
    */
    $plat_nomor = strtoupper($old_plat_nomor);
    $plat_nomor = preg_replace('/\s+/', ' ', $plat_nomor);
    $plat_nomor = trim($plat_nomor);

    /*
    |---------------------------------------------------------------
    | Validasi dasar
    |---------------------------------------------------------------
    */
    if ($id_area <= 0) {

        $error = "Silakan pilih area parkir.";

    } elseif ($plat_nomor === '') {

        $error = "Plat nomor wajib diisi.";

    } elseif ($old_warna_kendaraan === '') {

        $error = "Warna kendaraan wajib diisi.";

    } elseif ($id_tarif <= 0) {

        $error = "Jenis kendaraan/tarif wajib dipilih.";

    } elseif ($id_user <= 0) {

        $error = "Data user login tidak ditemukan. Silakan logout dan login kembali.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | CEK AREA PARKIR
        |--------------------------------------------------------------------------
        | Menghitung kendaraan aktif berdasarkan:
        | tb_parkir.id_area
        | dan belum memiliki transaksi keluar.
        |--------------------------------------------------------------------------
        */
        $stmt_area = mysqli_prepare(
            $koneksi,
            "
            SELECT
                a.id_area,
                a.nama_area,
                a.kapasitas,
                COUNT(
                    CASE
                        WHEN p.id_parkir IS NOT NULL
                        AND t.id_parkir IS NULL
                        THEN 1
                    END
                ) AS terisi
            FROM tb_area_parkir AS a
            LEFT JOIN tb_parkir AS p
                ON p.id_area = a.id_area
            LEFT JOIN tb_transaksi AS t
                ON t.id_parkir = p.id_parkir
            WHERE a.id_area = ?
            GROUP BY
                a.id_area,
                a.nama_area,
                a.kapasitas
            LIMIT 1
            "
        );

        if (!$stmt_area) {

            $error = "Gagal memeriksa area parkir: " . mysqli_error($koneksi);

        } else {

            mysqli_stmt_bind_param(
                $stmt_area,
                "i",
                $id_area
            );

            mysqli_stmt_execute($stmt_area);

            $result_area = mysqli_stmt_get_result($stmt_area);

            if (!$result_area || mysqli_num_rows($result_area) === 0) {

                $error = "Area parkir yang dipilih tidak ditemukan.";

            } else {

                $data_area = mysqli_fetch_assoc($result_area);

                $kapasitas = (int) $data_area['kapasitas'];
                $terisi    = (int) $data_area['terisi'];

                /*
                |-------------------------------------------------------
                | Cek kapasitas
                |-------------------------------------------------------
                */
                if ($kapasitas > 0 && $terisi >= $kapasitas) {

                    $error =
                        "Area " .
                        htmlspecialchars($data_area['nama_area']) .
                        " sudah penuh.";

                }
            }

            mysqli_stmt_close($stmt_area);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CEK MEMBER JIKA DIPILIH
    |--------------------------------------------------------------------------
    */
    if ($error === '' && $id_member !== null) {

        $stmt_member = mysqli_prepare(
            $koneksi,
            "
            SELECT
                id_member,
                nama_member,
                status_aktif
            FROM tb_member
            WHERE id_member = ?
            LIMIT 1
            "
        );

        if (!$stmt_member) {

            $error = "Gagal memeriksa member.";

        } else {

            mysqli_stmt_bind_param(
                $stmt_member,
                "i",
                $id_member
            );

            mysqli_stmt_execute($stmt_member);

            $result_member = mysqli_stmt_get_result($stmt_member);

            if (!$result_member || mysqli_num_rows($result_member) === 0) {

                $error = "Member yang dipilih tidak ditemukan.";

            } else {

                $data_member = mysqli_fetch_assoc($result_member);

                if (
                    isset($data_member['status_aktif']) &&
                    strtolower($data_member['status_aktif']) !== 'aktif'
                ) {
                    $error = "Member yang dipilih sedang tidak aktif.";
                }
            }

            mysqli_stmt_close($stmt_member);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CEK TARIF
    |--------------------------------------------------------------------------
    | Jenis kendaraan tidak diketik manual.
    | Diambil dari tb_tarif berdasarkan id_tarif.
    |--------------------------------------------------------------------------
    */
    $jenis_kendaraan = '';
    $tarif_per_jam   = 0;

    if ($error === '') {

        $stmt_tarif = mysqli_prepare(
            $koneksi,
            "
            SELECT
                id_tarif,
                jenis_kendaraan,
                tarif_per_jam
            FROM tb_tarif
            WHERE id_tarif = ?
            LIMIT 1
            "
        );

        if (!$stmt_tarif) {

            $error = "Gagal memeriksa tarif kendaraan.";

        } else {

            mysqli_stmt_bind_param(
                $stmt_tarif,
                "i",
                $id_tarif
            );

            mysqli_stmt_execute($stmt_tarif);

            $result_tarif = mysqli_stmt_get_result($stmt_tarif);

            if (!$result_tarif || mysqli_num_rows($result_tarif) === 0) {

                $error = "Tarif kendaraan tidak ditemukan.";

            } else {

                $data_tarif = mysqli_fetch_assoc($result_tarif);

                $jenis_kendaraan = $data_tarif['jenis_kendaraan'];
                $tarif_per_jam   = (int) $data_tarif['tarif_per_jam'];
            }

            mysqli_stmt_close($stmt_tarif);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CEK PLAT NOMOR
    |--------------------------------------------------------------------------
    | Satu kendaraan tidak boleh memiliki parkir aktif ganda.
    |--------------------------------------------------------------------------
    */
    if ($error === '') {

        $plat_normalisasi = strtoupper(
            preg_replace('/\s+/', '', $plat_nomor)
        );

        $stmt_duplicate = mysqli_prepare(
            $koneksi,
            "
            SELECT
                p.id_parkir
            FROM tb_parkir AS p
            LEFT JOIN tb_transaksi AS t
                ON t.id_parkir = p.id_parkir
            WHERE
                REPLACE(UPPER(p.plat_nomor), ' ', '') = ?
                AND t.id_parkir IS NULL
            LIMIT 1
            "
        );

        if (!$stmt_duplicate) {

            $error = "Gagal memeriksa plat nomor.";

        } else {

            mysqli_stmt_bind_param(
                $stmt_duplicate,
                "s",
                $plat_normalisasi
            );

            mysqli_stmt_execute($stmt_duplicate);

            $result_duplicate = mysqli_stmt_get_result($stmt_duplicate);

            if (
                $result_duplicate &&
                mysqli_num_rows($result_duplicate) > 0
            ) {
                $error = "Kendaraan dengan plat nomor " .
                         htmlspecialchars($plat_nomor) .
                         " masih berada di area parkir.";
            }

            mysqli_stmt_close($stmt_duplicate);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA PARKIR
    |--------------------------------------------------------------------------
    */
    if ($error === '') {

        $waktu_masuk = date('Y-m-d H:i:s');

        /*
        |---------------------------------------------------------------
        | Gunakan transaksi database supaya data parkir dan log
        | diproses dengan lebih aman.
        |---------------------------------------------------------------
        */
        mysqli_begin_transaction($koneksi);

        try {

            /*
            |-----------------------------------------------------------
            | INSERT TB_PARKIR
            |-----------------------------------------------------------
            */
            $stmt_insert = mysqli_prepare(
                $koneksi,
                "
                INSERT INTO tb_parkir
                (
                    id_member,
                    id_area,
                    plat_nomor,
                    warna_kendaraan,
                    jenis_kendaraan,
                    id_tarif,
                    waktu_masuk,
                    id_user_masuk
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                "
            );

            if (!$stmt_insert) {
                throw new Exception(
                    "Gagal menyiapkan penyimpanan parkir: " .
                    mysqli_error($koneksi)
                );
            }

            /*
            |-----------------------------------------------------------
            | Jika member kosong, gunakan NULL
            |-----------------------------------------------------------
            */
            mysqli_stmt_bind_param(
                $stmt_insert,
                "iisssisi",
                $id_member,
                $id_area,
                $plat_nomor,
                $old_warna_kendaraan,
                $jenis_kendaraan,
                $id_tarif,
                $waktu_masuk,
                $id_user
            );

            if (!mysqli_stmt_execute($stmt_insert)) {

                throw new Exception(
                    "Gagal menyimpan data parkir: " .
                    mysqli_stmt_error($stmt_insert)
                );
            }

            $id_parkir_baru = mysqli_insert_id($koneksi);

            mysqli_stmt_close($stmt_insert);

            /*
            |-----------------------------------------------------------
            | CATAT AKTIVITAS
            |-----------------------------------------------------------
            */
            $aktivitas =
                "Menambahkan kendaraan masuk parkir " .
                $plat_nomor .
                " - Area " .
                ($data_area['nama_area'] ?? '');

            $stmt_log = mysqli_prepare(
                $koneksi,
                "
                INSERT INTO tb_log_aktivitas
                (
                    id_user,
                    aktivitas,
                    waktu_aktivitas
                )
                VALUES (?, ?, ?)
                "
            );

            if ($stmt_log) {

                mysqli_stmt_bind_param(
                    $stmt_log,
                    "iss",
                    $id_user,
                    $aktivitas,
                    $waktu_masuk
                );

                if (!mysqli_stmt_execute($stmt_log)) {

                    /*
                    | Log gagal tidak membuat transaksi parkir gagal.
                    */
                }

                mysqli_stmt_close($stmt_log);
            }

            /*
            |-----------------------------------------------------------
            | COMMIT
            |-----------------------------------------------------------
            */
            mysqli_commit($koneksi);

            /*
            |-----------------------------------------------------------
            | KEMBALI KE DATA PARKIR
            |-----------------------------------------------------------
            */
            header("Location: tampil_parkir.php?status=success");
            exit;

        } catch (Exception $e) {

            /*
            |-----------------------------------------------------------
            | ROLLBACK
            |-----------------------------------------------------------
            */
            mysqli_rollback($koneksi);

            $error = $e->getMessage();
        }
    }
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA MEMBER
|--------------------------------------------------------------------------
*/
$members = [];

$query_member = mysqli_query(
    $koneksi,
    "
    SELECT
        id_member,
        nama_member,
        no_hp,
        status_aktif
    FROM tb_member
    WHERE status_aktif = 'aktif'
    ORDER BY nama_member ASC
    "
);

if ($query_member) {

    while ($row = mysqli_fetch_assoc($query_member)) {
        $members[] = $row;
    }
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA TARIF
|--------------------------------------------------------------------------
*/
$tarifs = [];

$query_tarif = mysqli_query(
    $koneksi,
    "
    SELECT
        id_tarif,
        jenis_kendaraan,
        tarif_per_jam
    FROM tb_tarif
    ORDER BY jenis_kendaraan ASC
    "
);

if ($query_tarif) {

    while ($row = mysqli_fetch_assoc($query_tarif)) {
        $tarifs[] = $row;
    }
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA AREA + JUMLAH TERISI AKTIF
|--------------------------------------------------------------------------
*/
$areas = [];

$query_area = mysqli_query(
    $koneksi,
    "
    SELECT
        a.id_area,
        a.nama_area,
        a.kapasitas,
        COUNT(
            CASE
                WHEN p.id_parkir IS NOT NULL
                AND t.id_parkir IS NULL
                THEN 1
            END
        ) AS terisi
    FROM tb_area_parkir AS a
    LEFT JOIN tb_parkir AS p
        ON p.id_area = a.id_area
    LEFT JOIN tb_transaksi AS t
        ON t.id_parkir = p.id_parkir
    GROUP BY
        a.id_area,
        a.nama_area,
        a.kapasitas
    ORDER BY
        a.nama_area ASC
    "
);

if ($query_area) {

    while ($row = mysqli_fetch_assoc($query_area)) {

        $row['kapasitas'] = (int) $row['kapasitas'];
        $row['terisi']    = (int) $row['terisi'];

        $row['tersedia'] =
            max(
                0,
                $row['kapasitas'] - $row['terisi']
            );

        $areas[] = $row;
    }
}

/*
|--------------------------------------------------------------------------
| WAKTU SAAT INI
|--------------------------------------------------------------------------
*/
$waktu_sekarang = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah Parkir - Sistem Parkir</title>

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Arial,
                sans-serif;
            background: #eef7ff;
            color: #17324d;
        }

        .page {
            width: 100%;
            min-height: 100vh;
            padding: 30px;
        }

        .container {
            max-width: 950px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 22px;
        }

        .title-area h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 750;
            color: #12395b;
        }

        .title-area p {
            margin: 6px 0 0;
            color: #66829b;
            font-size: 14px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 12px;
            text-decoration: none;
            background: #ffffff;
            color: #286da8;
            border: 1px solid #d5e7f7;
            font-weight: 650;
            transition: .2s ease;
        }

        .btn-back:hover {
            background: #e7f4ff;
            transform: translateY(-1px);
        }

        .card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #dcecf8;
            box-shadow: 0 12px 35px rgba(47, 117, 167, .10);
            overflow: hidden;
        }

        .card-header {
            padding: 22px 25px;
            background: linear-gradient(
                135deg,
                #e7f5ff,
                #f7fbff
            );
            border-bottom: 1px solid #dcecf8;
        }

        .card-header h2 {
            margin: 0;
            font-size: 18px;
            color: #184d76;
        }

        .card-header p {
            margin: 5px 0 0;
            color: #6d879d;
            font-size: 13px;
        }

        .card-body {
            padding: 28px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .alert-error {
            background: #fff1f1;
            color: #a72828;
            border: 1px solid #ffd3d3;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: 700;
            color: #244c69;
        }

        .required {
            color: #e04a4a;
        }

        input,
        select {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1px solid #cfe1ef;
            border-radius: 11px;
            background: #ffffff;
            color: #1d405c;
            outline: none;
            font-size: 14px;
            transition: .2s ease;
        }

        input:focus,
        select:focus {
            border-color: #55a7df;
            box-shadow: 0 0 0 4px rgba(74, 166, 225, .12);
        }

        input[readonly] {
            background: #f3f8fc;
            color: #607b91;
        }

        .area-info {
            margin-top: 6px;
            padding: 10px 12px;
            border-radius: 9px;
            background: #f2f9ff;
            color: #47718e;
            font-size: 12px;
        }

        .area-info.full {
            background: #fff2f2;
            color: #b23838;
        }

        .form-footer {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #e4eef5;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn {
            border: none;
            border-radius: 11px;
            padding: 12px 19px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: .2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-cancel {
            background: #edf4f9;
            color: #527086;
        }

        .btn-cancel:hover {
            background: #dfeaf2;
        }

        .btn-save {
            background: #248bd1;
            color: white;
            box-shadow: 0 7px 18px rgba(36, 139, 209, .22);
        }

        .btn-save:hover {
            background: #1979ba;
            transform: translateY(-1px);
        }

        .btn-save:disabled {
            background: #a9c8dc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .info-box {
            margin-top: 22px;
            padding: 15px 17px;
            background: #f1f8fd;
            border: 1px solid #d8eaf6;
            border-radius: 13px;
            color: #56758b;
            font-size: 13px;
            line-height: 1.6;
        }

        .info-box strong {
            color: #245879;
        }

        @media (max-width: 700px) {

            .page {
                padding: 16px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .card-body {
                padding: 20px;
            }

            .form-footer {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<div class="page">

    <div class="container">

        <!-- =========================================================
             HEADER
             ========================================================= -->
        <div class="topbar">

            <div class="title-area">

                <h1>
                    <i class="bi bi-car-front-fill"></i>
                    Tambah Parkir
                </h1>

                <p>
                    Catat kendaraan yang masuk ke area parkir.
                </p>

            </div>

            <a
                href="tampil_parkir.php"
                class="btn-back"
            >
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>

        </div>


        <!-- =========================================================
             CARD
             ========================================================= -->
        <div class="card">

            <div class="card-header">

                <h2>
                    Data Kendaraan Masuk
                </h2>

                <p>
                    Diisi oleh petugas atau administrator.
                </p>

            </div>


            <div class="card-body">

                <!-- =================================================
                     ERROR
                     ================================================= -->
                <?php if ($error !== ''): ?>

                    <div class="alert alert-error">

                        <i class="bi bi-exclamation-circle"></i>

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     FORM
                     ================================================= -->
                <form
                    method="POST"
                    action=""
                    autocomplete="off"
                >

                    <div class="form-grid">


                        <!-- =========================================
                             MEMBER
                             ========================================= -->
                        <div class="form-group full">

                            <label for="id_member">
                                Member
                            </label>

                            <select
                                name="id_member"
                                id="id_member"
                            >

                                <option value="">
                                    -- Bukan Member / Umum --
                                </option>

                                <?php foreach ($members as $member): ?>

                                    <option
                                        value="<?= (int) $member['id_member'] ?>"
                                        <?= (
                                            (string) $old_id_member ===
                                            (string) $member['id_member']
                                        ) ? 'selected' : '' ?>
                                    >

                                        <?= htmlspecialchars($member['nama_member']) ?>

                                        <?php if (!empty($member['no_hp'])): ?>

                                            -
                                            <?= htmlspecialchars($member['no_hp']) ?>

                                        <?php endif; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- =========================================
                             AREA
                             ========================================= -->
                        <div class="form-group">

                            <label for="id_area">

                                Area Parkir

                                <span class="required">*</span>

                            </label>

                            <select
                                name="id_area"
                                id="id_area"
                                required
                            >

                                <option value="">
                                    -- Pilih Area Parkir --
                                </option>

                                <?php foreach ($areas as $area): ?>

                                    <?php

                                    $is_full =
                                        $area['kapasitas'] > 0 &&
                                        $area['terisi'] >= $area['kapasitas'];

                                    ?>

                                    <option
                                        value="<?= (int) $area['id_area'] ?>"
                                        <?= (
                                            (string) $old_id_area ===
                                            (string) $area['id_area']
                                        ) ? 'selected' : '' ?>
                                        <?= $is_full ? 'disabled' : '' ?>
                                    >

                                        <?= htmlspecialchars($area['nama_area']) ?>

                                        -
                                        Terisi:
                                        <?= (int) $area['terisi'] ?>

                                        /

                                        <?= (int) $area['kapasitas'] ?>

                                        <?= $is_full ? ' (PENUH)' : '' ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- =========================================
                             PLAT NOMOR
                             ========================================= -->
                        <div class="form-group">

                            <label for="plat_nomor">

                                Plat Nomor

                                <span class="required">*</span>

                            </label>

                            <input
                                type="text"
                                name="plat_nomor"
                                id="plat_nomor"
                                value="<?= htmlspecialchars($old_plat_nomor) ?>"
                                placeholder="Contoh: D 1234 ABC"
                                maxlength="20"
                                required
                            >

                        </div>


                        <!-- =========================================
                             WARNA
                             ========================================= -->
                        <div class="form-group">

                            <label for="warna_kendaraan">

                                Warna Kendaraan

                                <span class="required">*</span>

                            </label>

                            <input
                                type="text"
                                name="warna_kendaraan"
                                id="warna_kendaraan"
                                value="<?= htmlspecialchars($old_warna_kendaraan) ?>"
                                placeholder="Contoh: Hitam"
                                maxlength="50"
                                required
                            >

                        </div>


                        <!-- =========================================
                             JENIS KENDARAAN
                             ========================================= -->
                        <div class="form-group">

                            <label for="id_tarif">

                                Jenis Kendaraan

                                <span class="required">*</span>

                            </label>

                            <select
                                name="id_tarif"
                                id="id_tarif"
                                required
                            >

                                <option value="">
                                    -- Pilih Jenis Kendaraan --
                                </option>

                                <?php foreach ($tarifs as $tarif): ?>

                                    <option
                                        value="<?= (int) $tarif['id_tarif'] ?>"
                                        <?= (
                                            (string) $old_id_tarif ===
                                            (string) $tarif['id_tarif']
                                        ) ? 'selected' : '' ?>
                                    >

                                        <?= htmlspecialchars($tarif['jenis_kendaraan']) ?>

                                        -
                                        Rp
                                        <?= number_format(
                                            (int) $tarif['tarif_per_jam'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                        / jam

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- =========================================
                             WAKTU MASUK
                             ========================================= -->
                        <div class="form-group">

                            <label for="waktu_masuk">

                                Waktu Masuk

                            </label>

                            <input
                                type="text"
                                id="waktu_masuk"
                                value="<?= date('d-m-Y H:i:s') ?>"
                                readonly
                            >

                        </div>

                    </div>


                    <!-- =================================================
                         INFO
                         ================================================= -->
                    <div class="info-box">

                        <strong>
                            <i class="bi bi-info-circle"></i>
                            Informasi:
                        </strong>

                        Waktu masuk akan disimpan otomatis berdasarkan
                        waktu server. Jenis kendaraan diambil dari data
                        tarif yang dipilih. Saat kendaraan keluar,
                        kapasitas area akan otomatis berkurang berdasarkan
                        transaksi yang telah diproses.

                    </div>


                    <!-- =================================================
                         BUTTON
                         ================================================= -->
                    <div class="form-footer">

                        <a
                            href="tampil_parkir.php"
                            class="btn btn-cancel"
                        >
                            <i class="bi bi-x-circle"></i>
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="btn btn-save"
                            id="btnSimpan"
                        >
                            <i class="bi bi-check-circle"></i>
                            Simpan Parkir
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| UBAH PLAT NOMOR MENJADI HURUF BESAR
|--------------------------------------------------------------------------
*/
const platNomor = document.getElementById('plat_nomor');

if (platNomor) {

    platNomor.addEventListener('input', function () {

        this.value = this.value.toUpperCase();

    });

}


/*
|--------------------------------------------------------------------------
| CEGAH DOUBLE SUBMIT
|--------------------------------------------------------------------------
*/
const form = document.querySelector('form');
const btnSimpan = document.getElementById('btnSimpan');

if (form && btnSimpan) {

    form.addEventListener('submit', function () {

        btnSimpan.disabled = true;

        btnSimpan.innerHTML =
            '<i class="bi bi-hourglass-split"></i> Menyimpan...';

    });

}


/*
|--------------------------------------------------------------------------
| TAMPILKAN INFO AREA
|--------------------------------------------------------------------------
*/
const selectArea = document.getElementById('id_area');

if (selectArea) {

    const areaData = {

        <?php foreach ($areas as $area): ?>

        <?= (int) $area['id_area'] ?>: {

            nama:
                <?= json_encode($area['nama_area']) ?>,

            kapasitas:
                <?= (int) $area['kapasitas'] ?>,

            terisi:
                <?= (int) $area['terisi'] ?>,

            tersedia:
                <?= (int) $area['tersedia'] ?>

        },

        <?php endforeach; ?>

    };

    const oldAreaInfo = document.createElement('div');

    oldAreaInfo.className = 'area-info';

    selectArea.parentNode.appendChild(oldAreaInfo);


    function updateAreaInfo() {

        const id = selectArea.value;

        if (!id || !areaData[id]) {

            oldAreaInfo.innerHTML =
                'Pilih area untuk melihat kapasitas parkir.';

            oldAreaInfo.classList.remove('full');

            return;
        }

        const area = areaData[id];

        if (
            area.kapasitas > 0 &&
            area.terisi >= area.kapasitas
        ) {

            oldAreaInfo.innerHTML =
                '<i class="bi bi-exclamation-triangle"></i> ' +
                area.nama +
                ' penuh.';

            oldAreaInfo.classList.add('full');

        } else {

            oldAreaInfo.innerHTML =
                '<i class="bi bi-p-square"></i> ' +
                area.nama +
                ' — Terisi ' +
                area.terisi +
                ' dari ' +
                area.kapasitas +
                ' — Tersedia ' +
                area.tersedia +
                ' tempat.';

            oldAreaInfo.classList.remove('full');

        }

    }

    selectArea.addEventListener(
        'change',
        updateAreaInfo
    );

    updateAreaInfo();

}

</script>

</body>
</html>
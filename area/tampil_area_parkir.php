<?php
// ============================================================
// area/tampil_area_parkir.php
// Data Area Parkir
//
// LOGIKA:
// - Kapasitas berasal dari tb_area_parkir.kapasitas
// - Terisi dihitung otomatis dari kendaraan yang masih parkir
// - Kendaraan yang sudah memiliki transaksi keluar TIDAK dihitung
// - Sisa = Kapasitas - Terisi
// - Tidak perlu UPDATE kolom terisi secara manual
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../koneksi.php';

// ============================================================
// CEK LOGIN
// ============================================================

if (!isset($_SESSION['id_user'], $_SESSION['role'])) {
    header('Location: ../login.php');
    exit;
}

// ============================================================
// DASHBOARD SESUAI ROLE
// ============================================================

$role = $_SESSION['role'];

if ($role === 'admin') {

    $dashboard = '../dashboard_admin.php';

} elseif ($role === 'petugas') {

    $dashboard = '../dashboard_petugas.php';

} elseif ($role === 'owner') {

    $dashboard = '../dashboard_owner.php';

} else {

    header('Location: ../login.php');
    exit;
}

// ============================================================
// PESAN
// ============================================================

$pesan = '';
$tipe_pesan = '';

// ============================================================
// HAPUS AREA
// ============================================================

if (isset($_GET['hapus'])) {

    // Hanya admin yang boleh menghapus area
    if ($role !== 'admin') {

        $pesan = 'Anda tidak memiliki izin untuk menghapus area parkir.';
        $tipe_pesan = 'danger';

    } else {

        $id_area_hapus = (int) $_GET['hapus'];

        if ($id_area_hapus > 0) {

            // ------------------------------------------------
            // Cek apakah area masih memiliki member
            // ------------------------------------------------

            $stmtCekMember = mysqli_prepare(
                $koneksi,
                "
                SELECT COUNT(*) AS jumlah
                FROM tb_member
                WHERE id_area = ?
                "
            );

            if ($stmtCekMember) {

                mysqli_stmt_bind_param(
                    $stmtCekMember,
                    'i',
                    $id_area_hapus
                );

                mysqli_stmt_execute($stmtCekMember);

                $resultCekMember =
                    mysqli_stmt_get_result($stmtCekMember);

                $dataCekMember =
                    mysqli_fetch_assoc($resultCekMember);

                mysqli_stmt_close($stmtCekMember);

                $jumlahMember =
                    (int) ($dataCekMember['jumlah'] ?? 0);

            } else {

                $jumlahMember = 0;
            }

            // ------------------------------------------------
            // Jika masih digunakan member
            // ------------------------------------------------

            if ($jumlahMember > 0) {

                $pesan =
                    'Area tidak dapat dihapus karena masih digunakan oleh member.';

                $tipe_pesan = 'danger';

            } else {

                // --------------------------------------------
                // Hapus area
                // --------------------------------------------

                $stmtHapus = mysqli_prepare(
                    $koneksi,
                    "
                    DELETE FROM tb_area_parkir
                    WHERE id_area = ?
                    "
                );

                if ($stmtHapus) {

                    mysqli_stmt_bind_param(
                        $stmtHapus,
                        'i',
                        $id_area_hapus
                    );

                    if (mysqli_stmt_execute($stmtHapus)) {

                        $pesan =
                            'Area parkir berhasil dihapus.';

                        $tipe_pesan = 'success';

                    } else {

                        $pesan =
                            'Area parkir gagal dihapus.';

                        $tipe_pesan = 'danger';
                    }

                    mysqli_stmt_close($stmtHapus);

                } else {

                    $pesan =
                        'Terjadi kesalahan saat menghapus area.';

                    $tipe_pesan = 'danger';
                }
            }
        }
    }
}

// ============================================================
// PENCARIAN
// ============================================================

$cari = trim($_GET['cari'] ?? '');

$where = '';

if ($cari !== '') {

    $cariEscaped = mysqli_real_escape_string(
        $koneksi,
        $cari
    );

    $where = "
        WHERE
            a.nama_area LIKE '%$cariEscaped%'
            OR CAST(a.id_area AS CHAR) LIKE '%$cariEscaped%'
    ";
}

// ============================================================
// QUERY AREA PARKIR
//
// TERISI DIHITUNG OTOMATIS:
//
// tb_area_parkir
//       ↓
// tb_member
//       ↓
// tb_parkir
//       ↓
// tb_transaksi
//
// Jika tb_parkir belum memiliki transaksi,
// kendaraan masih dianggap berada di area.
//
// Jika sudah memiliki transaksi,
// kendaraan dianggap sudah keluar.
// ============================================================

$sql = "
    SELECT
        a.id_area,
        a.nama_area,
        a.kapasitas,

        COUNT(
            CASE
                WHEN
                    p.id_parkir IS NOT NULL
                    AND t.id_parkir IS NULL
                THEN 1
            END
        ) AS terisi

    FROM tb_area_parkir AS a

    LEFT JOIN tb_member AS m
        ON a.id_area = m.id_area

    LEFT JOIN tb_parkir AS p
        ON m.id_member = p.id_member

    LEFT JOIN tb_transaksi AS t
        ON p.id_parkir = t.id_parkir

    $where

    GROUP BY
        a.id_area,
        a.nama_area,
        a.kapasitas

    ORDER BY
        a.id_area DESC
";

$result = mysqli_query($koneksi, $sql);

// ============================================================
// CEK QUERY
// ============================================================

if (!$result) {

    die(
        'Query area parkir gagal: ' .
        htmlspecialchars(mysqli_error($koneksi))
    );
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

    <title>Area Parkir - Sistem Parkir</title>

    <!-- ======================================================
         BOOTSTRAP ICONS
         ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- ======================================================
         CSS TABEL
         ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/tabel.css"
    >

    <style>

        /* =====================================================
           RINGKASAN AREA
           ===================================================== */

        .area-summary {

            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 18px;

            margin-bottom: 25px;
        }

        .summary-card {

            background: #ffffff;

            border: 1px solid #e4e9f0;

            border-radius: 16px;

            padding: 20px;

            display: flex;

            align-items: center;

            gap: 15px;

            box-shadow:
                0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .summary-icon {

            width: 48px;
            height: 48px;

            min-width: 48px;

            border-radius: 13px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 21px;
        }

        .summary-icon.blue {

            background: #eaf4ff;

            color: #0b4f9c;
        }

        .summary-icon.green {

            background: #eaf8f0;

            color: #198754;
        }

        .summary-icon.yellow {

            background: #fff8dc;

            color: #b88b00;
        }

        .summary-content {

            display: flex;

            flex-direction: column;

            gap: 4px;
        }

        .summary-content span {

            color: #718096;

            font-size: 13px;

            font-weight: 600;
        }

        .summary-content strong {

            color: #172033;

            font-size: 23px;

            font-weight: 800;
        }

        /* =====================================================
           KARTU KETERANGAN
           ===================================================== */

        .area-info {

            background: #eaf4ff;

            border: 1px solid #cfe5ff;

            color: #073b75;

            border-radius: 14px;

            padding: 14px 17px;

            margin-bottom: 20px;

            display: flex;

            align-items: flex-start;

            gap: 11px;

            font-size: 13px;

            line-height: 1.6;
        }

        .area-info i {

            font-size: 18px;

            margin-top: 1px;
        }

        /* =====================================================
           STATUS AREA
           ===================================================== */

        .area-status {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 6px 10px;

            border-radius: 999px;

            font-size: 12px;

            font-weight: 700;
        }

        .area-status.available {

            color: #157347;

            background: #eaf8f0;
        }

        .area-status.full {

            color: #b02a37;

            background: #fff0f1;
        }

        .area-status.empty {

            color: #6c757d;

            background: #f1f3f5;
        }

        .status-dot {

            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: currentColor;
        }

        /* =====================================================
           PROGRESS
           ===================================================== */

        .capacity-box {

            min-width: 160px;
        }

        .capacity-text {

            display: flex;

            justify-content: space-between;

            gap: 10px;

            margin-bottom: 7px;

            font-size: 12px;
        }

        .capacity-text strong {

            color: #263449;
        }

        .capacity-text span {

            color: #718096;
        }

        .progress {

            width: 100%;

            height: 8px;

            background: #e9eef5;

            border-radius: 999px;

            overflow: hidden;
        }

        .progress-bar {

            height: 100%;

            border-radius: 999px;

            transition: width 0.3s ease;
        }

        .progress-green {

            background: #198754;
        }

        .progress-yellow {

            background: #f5c400;
        }

        .progress-red {

            background: #dc3545;
        }

        /* =====================================================
           ANGKA TERISI / SISA
           ===================================================== */

        .capacity-number {

            font-weight: 800;

            color: #073b75;

            font-size: 14px;
        }

        .remaining-number {

            font-weight: 800;

            color: #198754;

            font-size: 14px;
        }

        /* =====================================================
           RESPONSIVE
           ===================================================== */

        @media (max-width: 800px) {

            .area-summary {

                grid-template-columns: 1fr;
            }

            .capacity-box {

                min-width: 120px;
            }
        }

        @media (max-width: 600px) {

            .capacity-box {

                min-width: 100px;
            }

            .table-card {

                overflow-x: auto;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <!-- ======================================================
         HEADER
         ====================================================== -->

    <div class="page-header">

        <div>

            <h1 class="page-title">

                <i class="bi bi-p-square-fill"></i>

                Area Parkir

            </h1>

            <p class="page-subtitle">

                Kelola kapasitas dan kondisi area parkir
                secara otomatis.

            </p>

        </div>


        <a
            href="<?= htmlspecialchars($dashboard) ?>"
            class="btn-kembali"
        >

            <i class="bi bi-arrow-left"></i>

            Kembali ke Dashboard

        </a>

    </div>


    <!-- ======================================================
         PESAN
         ====================================================== -->

    <?php if ($pesan !== ''): ?>

        <div class="alert alert-<?= $tipe_pesan ?>">

            <?php if ($tipe_pesan === 'success'): ?>

                <i class="bi bi-check-circle-fill"></i>

            <?php else: ?>

                <i class="bi bi-exclamation-circle-fill"></i>

            <?php endif; ?>

            <span>
                <?= htmlspecialchars($pesan) ?>
            </span>

        </div>

    <?php endif; ?>


    <!-- ======================================================
         INFORMASI OTOMATIS
         ====================================================== -->

    <div class="area-info">

        <i class="bi bi-info-circle-fill"></i>

        <div>

            <strong>Kapasitas dihitung otomatis.</strong>

            Jumlah <strong>Terisi</strong> berasal dari kendaraan
            yang masih berada di parkir dan belum memiliki
            transaksi keluar. Setelah kendaraan keluar,
            kapasitas tersedia otomatis bertambah kembali.

        </div>

    </div>


    <!-- ======================================================
         RINGKASAN SELURUH AREA
         ====================================================== -->

    <?php

    // Simpan data terlebih dahulu agar bisa digunakan
    // untuk menghitung ringkasan keseluruhan.

    $data_area = [];

    $total_kapasitas = 0;
    $total_terisi = 0;

    while ($row = mysqli_fetch_assoc($result)) {

        $kapasitas = (int) $row['kapasitas'];

        $terisi = (int) $row['terisi'];

        // Jangan sampai terisi lebih besar dari kapasitas.
        if ($terisi > $kapasitas) {
            $terisi = $kapasitas;
        }

        if ($terisi < 0) {
            $terisi = 0;
        }

        $sisa = $kapasitas - $terisi;

        if ($sisa < 0) {
            $sisa = 0;
        }

        $row['kapasitas_hitung'] = $kapasitas;
        $row['terisi_hitung'] = $terisi;
        $row['sisa_hitung'] = $sisa;

        $data_area[] = $row;

        $total_kapasitas += $kapasitas;
        $total_terisi += $terisi;
    }

    $total_sisa =
        $total_kapasitas - $total_terisi;

    if ($total_sisa < 0) {
        $total_sisa = 0;
    }

    ?>


    <div class="area-summary">

        <!-- TOTAL KAPASITAS -->

        <div class="summary-card">

            <div class="summary-icon blue">

                <i class="bi bi-p-square-fill"></i>

            </div>

            <div class="summary-content">

                <span>
                    Total Kapasitas
                </span>

                <strong>
                    <?= number_format(
                        $total_kapasitas,
                        0,
                        ',',
                        '.'
                    ) ?>
                </strong>

            </div>

        </div>


        <!-- TOTAL TERISI -->

        <div class="summary-card">

            <div class="summary-icon green">

                <i class="bi bi-car-front-fill"></i>

            </div>

            <div class="summary-content">

                <span>
                    Sedang Terisi
                </span>

                <strong>
                    <?= number_format(
                        $total_terisi,
                        0,
                        ',',
                        '.'
                    ) ?>
                </strong>

            </div>

        </div>


        <!-- TOTAL SISA -->

        <div class="summary-card">

            <div class="summary-icon yellow">

                <i class="bi bi-p-square"></i>

            </div>

            <div class="summary-content">

                <span>
                    Tempat Tersedia
                </span>

                <strong>
                    <?= number_format(
                        $total_sisa,
                        0,
                        ',',
                        '.'
                    ) ?>
                </strong>

            </div>

        </div>

    </div>


    <!-- ======================================================
         TOOLBAR
         ====================================================== -->

    <div class="table-card">

        <div class="table-toolbar">

            <form
                method="GET"
                action=""
                class="search-box"
            >

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari area parkir..."
                    value="<?= htmlspecialchars($cari) ?>"
                >

                <?php if ($cari !== ''): ?>

                    <a
                        href="tampil_area_parkir.php"
                        title="Hapus pencarian"
                    >

                        <i class="bi bi-x-circle-fill"></i>

                    </a>

                <?php endif; ?>

            </form>


            <?php if ($role === 'admin'): ?>

                <a
                    href="tambah_area.php"
                    class="btn btn-primary"
                >

                    <i class="bi bi-plus-lg"></i>

                    Tambah Area

                </a>

            <?php endif; ?>

        </div>


        <!-- ==================================================
             TABLE
             ================================================== -->

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th width="70">
                            No
                        </th>

                        <th>
                            Nama Area
                        </th>

                        <th width="160">
                            Kapasitas
                        </th>

                        <th width="200">
                            Terisi
                        </th>

                        <th width="200">
                            Sisa
                        </th>

                        <th width="190">
                            Status
                        </th>

                        <?php if ($role === 'admin'): ?>

                            <th width="170">
                                Aksi
                            </th>

                        <?php endif; ?>

                    </tr>

                </thead>


                <tbody>

                <?php if (count($data_area) > 0): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($data_area as $row): ?>

                        <?php

                        $kapasitas =
                            $row['kapasitas_hitung'];

                        $terisi =
                            $row['terisi_hitung'];

                        $sisa =
                            $row['sisa_hitung'];

                        // ------------------------------------
                        // Persentase terisi
                        // ------------------------------------

                        if ($kapasitas > 0) {

                            $persentase =
                                ($terisi / $kapasitas) * 100;

                        } else {

                            $persentase = 0;
                        }

                        if ($persentase > 100) {
                            $persentase = 100;
                        }

                        // ------------------------------------
                        // Status
                        // ------------------------------------

                        if ($kapasitas <= 0) {

                            $status_text =
                                'Belum Diatur';

                            $status_class =
                                'empty';

                            $progress_class =
                                'progress-green';

                        } elseif ($terisi >= $kapasitas) {

                            $status_text =
                                'Penuh';

                            $status_class =
                                'full';

                            $progress_class =
                                'progress-red';

                        } elseif ($persentase >= 80) {

                            $status_text =
                                'Hampir Penuh';

                            $status_class =
                                'available';

                            $progress_class =
                                'progress-yellow';

                        } else {

                            $status_text =
                                'Tersedia';

                            $status_class =
                                'available';

                            $progress_class =
                                'progress-green';
                        }

                        ?>

                        <tr>

                            <!-- NO -->

                            <td>

                                <?= $no++ ?>

                            </td>


                            <!-- NAMA AREA -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row['nama_area']
                                    ) ?>

                                </strong>

                            </td>


                            <!-- KAPASITAS -->

                            <td>

                                <span class="capacity-number">

                                    <?= number_format(
                                        $kapasitas,
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                    kendaraan

                                </span>

                            </td>


                            <!-- TERISI -->

                            <td>

                                <div class="capacity-box">

                                    <div class="capacity-text">

                                        <strong>

                                            <?= number_format(
                                                $terisi,
                                                0,
                                                ',',
                                                '.'
                                            ) ?>

                                        </strong>

                                        <span>

                                            <?= number_format(
                                                $persentase,
                                                0,
                                                ',',
                                                '.'
                                            ) ?>%

                                        </span>

                                    </div>

                                    <div class="progress">

                                        <div
                                            class="progress-bar <?= $progress_class ?>"
                                            style="width: <?= $persentase ?>%;"
                                        ></div>

                                    </div>

                                </div>

                            </td>


                            <!-- SISA -->

                            <td>

                                <span class="remaining-number">

                                    <?= number_format(
                                        $sisa,
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                    tempat

                                </span>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="area-status <?= $status_class ?>"
                                >

                                    <span class="status-dot"></span>

                                    <?= htmlspecialchars(
                                        $status_text
                                    ) ?>

                                </span>

                            </td>


                            <!-- AKSI -->

                            <?php if ($role === 'admin'): ?>

                                <td>

                                    <div class="aksi">

                                        <a
                                            href="edit_area.php?id=<?= (int) $row['id_area'] ?>"
                                            class="btn btn-warning"
                                            title="Edit area"
                                        >

                                            <i class="bi bi-pencil-square"></i>

                                            Edit

                                        </a>


                                        <a
                                            href="tampil_area_parkir.php?hapus=<?= (int) $row['id_area'] ?>"
                                            class="btn btn-danger"
                                            title="Hapus area"
                                            onclick="return confirm('Yakin ingin menghapus area <?= htmlspecialchars($row['nama_area'], ENT_QUOTES) ?>?');"
                                        >

                                            <i class="bi bi-trash3"></i>

                                            Hapus

                                        </a>

                                    </div>

                                </td>

                            <?php endif; ?>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="<?= $role === 'admin' ? '7' : '6' ?>"
                            style="text-align:center; padding:40px;"
                        >

                            <div class="empty-state">

                                <i class="bi bi-p-square"></i>

                                <h3>
                                    Area parkir belum tersedia
                                </h3>

                                <p>
                                    <?php if ($cari !== ''): ?>

                                        Tidak ditemukan area
                                        dengan kata pencarian
                                        tersebut.

                                    <?php else: ?>

                                        Belum ada data area parkir.

                                    <?php endif; ?>
                                </p>

                            </div>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- ======================================================
         FOOTER
         ====================================================== -->

    <div
        style="
            text-align:center;
            color:#718096;
            font-size:12px;
            margin-top:20px;
            padding-bottom:25px;
        "
    >

        Sistem Parkir
        &bull;
        <?= date('Y') ?>

    </div>

</div>


<!-- ==========================================================
     JAVASCRIPT
     ========================================================== -->

<script>

    // =========================================================
    // Hapus parameter hapus dari URL setelah halaman dimuat
    // =========================================================

    if (window.history.replaceState) {

        const url = new URL(
            window.location.href
        );

        if (url.searchParams.has('hapus')) {

            url.searchParams.delete('hapus');

            window.history.replaceState(
                {},
                document.title,
                url.toString()
            );
        }
    }

</script>

</body>

</html>
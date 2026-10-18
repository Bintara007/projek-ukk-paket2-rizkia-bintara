<?php

/*
|--------------------------------------------------------------------------
| CETAK HISTORI TRANSAKSI
|--------------------------------------------------------------------------
| Halaman laporan transaksi untuk OWNER.
|
| Fitur:
| - Pilih tanggal awal
| - Pilih tanggal akhir
| - Tampilkan transaksi sesuai periode
| - Hitung jumlah transaksi
| - Hitung total pendapatan
| - Cetak laporan
| - Kembali ke dashboard owner
|--------------------------------------------------------------------------
*/

session_start();

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
| HANYA OWNER
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'owner'
) {

    header("Location: ../login.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| DEFAULT TANGGAL
|--------------------------------------------------------------------------
|
| Jika user belum memilih tanggal:
| tanggal awal = hari ini
| tanggal akhir = hari ini
|
|--------------------------------------------------------------------------
*/

$tanggal_awal =
    $_GET['tanggal_awal'] ?? date('Y-m-d');

$tanggal_akhir =
    $_GET['tanggal_akhir'] ?? date('Y-m-d');


/*
|--------------------------------------------------------------------------
| VALIDASI TANGGAL
|--------------------------------------------------------------------------
*/

$error = '';


if (
    !preg_match(
        '/^\d{4}-\d{2}-\d{2}$/',
        $tanggal_awal
    )
) {

    $tanggal_awal = date('Y-m-d');
}


if (
    !preg_match(
        '/^\d{4}-\d{2}-\d{2}$/',
        $tanggal_akhir
    )
) {

    $tanggal_akhir = date('Y-m-d');
}


/*
|--------------------------------------------------------------------------
| CEK RANGE TANGGAL
|--------------------------------------------------------------------------
*/

if ($tanggal_awal > $tanggal_akhir) {

    $error =
        'Tanggal awal tidak boleh lebih besar dari tanggal akhir.';

}


/*
|--------------------------------------------------------------------------
| VARIABEL HASIL
|--------------------------------------------------------------------------
*/

$data_transaksi = null;

$jumlah_transaksi = 0;

$total_pendapatan = 0;

$total_durasi = 0;


/*
|--------------------------------------------------------------------------
| AMBIL DATA TRANSAKSI
|--------------------------------------------------------------------------
*/

if ($error === '') {


    /*
    |--------------------------------------------------------------------------
    | QUERY TRANSAKSI
    |--------------------------------------------------------------------------
    */

    $stmt = mysqli_prepare(
        $koneksi,

        "
        SELECT

            t.id_transaksi,

            t.id_parkir,

            p.plat_nomor,

            p.warna_kendaraan,

            p.jenis_kendaraan,

            t.waktu_keluar,

            t.durasi_jam,

            t.biaya_total,

            u.nama_lengkap

        FROM tb_transaksi AS t

        INNER JOIN tb_parkir AS p
            ON p.id_parkir = t.id_parkir

        LEFT JOIN tb_user AS u
            ON u.id_user = t.id_user

        WHERE DATE(t.waktu_keluar)
        BETWEEN ? AND ?

        ORDER BY
            t.waktu_keluar ASC
        "
    );


    /*
    |--------------------------------------------------------------------------
    | CEK QUERY
    |--------------------------------------------------------------------------
    */

    if (!$stmt) {

        $error =
            'Query transaksi gagal: ' .
            mysqli_error($koneksi);

    } else {


        /*
        |--------------------------------------------------------------------------
        | PARAMETER
        |--------------------------------------------------------------------------
        */

        mysqli_stmt_bind_param(
            $stmt,
            'ss',
            $tanggal_awal,
            $tanggal_akhir
        );


        /*
        |--------------------------------------------------------------------------
        | EKSEKUSI
        |--------------------------------------------------------------------------
        */

        mysqli_stmt_execute($stmt);


        /*
        |--------------------------------------------------------------------------
        | HASIL
        |--------------------------------------------------------------------------
        */

        $data_transaksi =
            mysqli_stmt_get_result($stmt);


        /*
        |--------------------------------------------------------------------------
        | HITUNG REKAP
        |--------------------------------------------------------------------------
        */

        if ($data_transaksi) {


            while (
                $row_rekap =
                mysqli_fetch_assoc(
                    $data_transaksi
                )
            ) {

                $jumlah_transaksi++;

                $total_pendapatan +=
                    (float)$row_rekap['biaya_total'];

                $total_durasi +=
                    (int)$row_rekap['durasi_jam'];
            }


            /*
            |--------------------------------------------------------------------------
            | KEMBALIKAN POINTER HASIL QUERY
            |--------------------------------------------------------------------------
            */

            mysqli_data_seek(
                $data_transaksi,
                0
            );
        }
    }
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


    <title>
        Cetak Histori Transaksi
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            padding: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f3f6fa;

            color: #1f2937;
        }


        .container {

            max-width: 1250px;

            margin: auto;

            padding: 30px;
        }


        /*
        ----------------------------------------------------------
        HEADER
        ----------------------------------------------------------
        */

        .page-header {

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            color: white;

            border-radius: 16px;

            padding: 25px;

            margin-bottom: 20px;

            box-shadow:
                0 8px 25px
                rgba(37, 99, 235, 0.20);
        }


        .page-header h1 {

            margin: 0 0 8px 0;

            font-size: 26px;
        }


        .page-header p {

            margin: 0;

            opacity: 0.9;
        }


        /*
        ----------------------------------------------------------
        BUTTON AREA
        ----------------------------------------------------------
        */

        .top-actions {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 12px;

            margin-bottom: 20px;

            flex-wrap: wrap;
        }


        .button-group {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;
        }


        .btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            padding:
                11px
                18px;

            border: none;

            border-radius: 9px;

            text-decoration: none;

            font-weight: bold;

            cursor: pointer;

            font-size: 14px;
        }


        .btn-back {

            background: #475569;

            color: white;
        }


        .btn-back:hover {

            background: #334155;
        }


        .btn-print {

            background: #16a34a;

            color: white;
        }


        .btn-print:hover {

            background: #15803d;
        }


        .btn-filter {

            background: #2563eb;

            color: white;

            border: none;

            padding:
                12px
                20px;

            border-radius: 9px;

            font-weight: bold;

            cursor: pointer;
        }


        /*
        ----------------------------------------------------------
        FILTER
        ----------------------------------------------------------
        */

        .filter-card {

            background: white;

            border-radius: 16px;

            padding: 25px;

            margin-bottom: 20px;

            box-shadow:
                0 5px 20px
                rgba(15, 23, 42, 0.07);
        }


        .filter-card h3 {

            margin-top: 0;

            margin-bottom: 18px;
        }


        .filter-form {

            display: flex;

            align-items: end;

            gap: 15px;

            flex-wrap: wrap;
        }


        .filter-group {

            display: flex;

            flex-direction: column;

            gap: 7px;
        }


        .filter-group label {

            font-weight: bold;

            font-size: 14px;
        }


        .filter-group input {

            min-width: 200px;

            padding: 11px 13px;

            border:
                1px solid
                #cbd5e1;

            border-radius: 8px;

            font-size: 14px;
        }


        .filter-group input:focus {

            outline: none;

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);
        }


        /*
        ----------------------------------------------------------
        ERROR
        ----------------------------------------------------------
        */

        .error {

            background: #fee2e2;

            color: #991b1b;

            padding: 14px 18px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-weight: bold;
        }


        /*
        ----------------------------------------------------------
        REKAP
        ----------------------------------------------------------
        */

        .summary {

            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    1fr
                );

            gap: 18px;

            margin-bottom: 20px;
        }


        .summary-card {

            background: white;

            border-radius: 15px;

            padding: 22px;

            box-shadow:
                0 5px 20px
                rgba(15, 23, 42, 0.07);
        }


        .summary-card span {

            display: block;

            color: #64748b;

            font-size: 14px;

            margin-bottom: 8px;
        }


        .summary-card strong {

            font-size: 24px;

            color: #1e293b;
        }


        /*
        ----------------------------------------------------------
        LAPORAN
        ----------------------------------------------------------
        */

        .report {

            background: white;

            border-radius: 16px;

            padding: 25px;

            box-shadow:
                0 5px 20px
                rgba(15, 23, 42, 0.07);
        }


        .report-header {

            text-align: center;

            margin-bottom: 25px;
        }


        .report-header h2 {

            margin: 0 0 8px 0;
        }


        .report-header p {

            margin: 4px 0;

            color: #64748b;
        }


        .table-wrapper {

            width: 100%;

            overflow-x: auto;
        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 950px;
        }


        th {

            background: #1e40af;

            color: white;

            padding: 13px 12px;

            text-align: left;

            font-size: 13px;
        }


        td {

            padding: 12px;

            border-bottom:
                1px solid
                #e2e8f0;

            font-size: 13px;
        }


        tbody tr:hover {

            background: #f8fafc;
        }


        .empty {

            text-align: center;

            padding: 35px;

            color: #64748b;
        }


        .total-row td {

            font-weight: bold;

            background: #eff6ff;
        }


        /*
        ----------------------------------------------------------
        RESPONSIVE
        ----------------------------------------------------------
        */

        @media (max-width: 800px) {

            .container {

                padding: 15px;
            }


            .summary {

                grid-template-columns:
                    1fr;
            }


            .filter-form {

                flex-direction: column;

                align-items: stretch;
            }


            .filter-group input {

                width: 100%;

                min-width: 0;
            }


            .btn-filter {

                width: 100%;
            }

        }


        /*
        ----------------------------------------------------------
        PRINT
        ----------------------------------------------------------
        */

        @media print {


            @page {

                size: A4 landscape;

                margin: 12mm;
            }


            body {

                background: white;
            }


            .container {

                max-width: none;

                padding: 0;
            }


            .page-header {

                background: none;

                color: black;

                box-shadow: none;

                padding: 0;

                margin-bottom: 15px;
            }


            .top-actions,
            .filter-card,
            .summary {

                display: none !important;
            }


            .report {

                box-shadow: none;

                padding: 0;

                border-radius: 0;
            }


            .report-header {

                margin-bottom: 15px;
            }


            th {

                background: #e5e7eb !important;

                color: black !important;

                -webkit-print-color-adjust: exact;

                print-color-adjust: exact;
            }


            td {

                padding: 7px;
            }


            .page-header h1 {

                font-size: 20px;
            }

        }

    </style>


</head>


<body>


<div class="container">


    <!-- =====================================================
         HEADER
         ===================================================== -->

    <div class="page-header">

        <h1>
            Laporan Histori Transaksi Parkir
        </h1>

        <p>
            Cetak laporan transaksi berdasarkan periode tanggal.
        </p>

    </div>


    <!-- =====================================================
         BUTTON
         ===================================================== -->

    <div class="top-actions">


        <div class="button-group">


            <!-- KEMBALI KE DASHBOARD -->

            <a
                href="../dashboard_owner.php"
                class="btn btn-back"
            >

                ← Kembali ke Dashboard

            </a>


        </div>


        <div class="button-group">


            <!-- CETAK -->

            <button
                type="button"
                class="btn btn-print"
                onclick="window.print()"
            >

                🖨 Cetak Laporan

            </button>


        </div>


    </div>


    <!-- =====================================================
         FILTER TANGGAL
         ===================================================== -->

    <div class="filter-card">


        <h3>
            Pilih Periode Transaksi
        </h3>


        <form
            method="GET"
            class="filter-form"
        >


            <!-- TANGGAL AWAL -->

            <div class="filter-group">

                <label for="tanggal_awal">

                    Dari Tanggal

                </label>

                <input
                    type="date"
                    id="tanggal_awal"
                    name="tanggal_awal"
                    value="<?= htmlspecialchars($tanggal_awal); ?>"
                    required
                >

            </div>


            <!-- TANGGAL AKHIR -->

            <div class="filter-group">

                <label for="tanggal_akhir">

                    Sampai Tanggal

                </label>

                <input
                    type="date"
                    id="tanggal_akhir"
                    name="tanggal_akhir"
                    value="<?= htmlspecialchars($tanggal_akhir); ?>"
                    required
                >

            </div>


            <!-- TOMBOL TAMPILKAN -->

            <button
                type="submit"
                class="btn-filter"
            >

                Tampilkan Laporan

            </button>


        </form>


    </div>


    <?php if ($error !== ''): ?>


        <div class="error">

            <?= htmlspecialchars($error); ?>

        </div>


    <?php else: ?>


        <!-- =================================================
             REKAP
             ================================================= -->

        <div class="summary">


            <div class="summary-card">

                <span>
                    Jumlah Transaksi
                </span>

                <strong>

                    <?= number_format(
                        $jumlah_transaksi,
                        0,
                        ',',
                        '.'
                    ); ?>

                </strong>

            </div>


            <div class="summary-card">

                <span>
                    Total Durasi
                </span>

                <strong>

                    <?= number_format(
                        $total_durasi,
                        0,
                        ',',
                        '.'
                    ); ?>

                    Jam

                </strong>

            </div>


            <div class="summary-card">

                <span>
                    Total Pendapatan
                </span>

                <strong>

                    Rp
                    <?= number_format(
                        $total_pendapatan,
                        0,
                        ',',
                        '.'
                    ); ?>

                </strong>

            </div>


        </div>


        <!-- =================================================
             LAPORAN
             ================================================= -->

        <div class="report">


            <div class="report-header">

                <h2>
                    HISTORI TRANSAKSI PARKIR
                </h2>


                <p>

                    Periode:

                    <strong>

                        <?= date(
                            'd-m-Y',
                            strtotime($tanggal_awal)
                        ); ?>

                    </strong>

                    sampai

                    <strong>

                        <?= date(
                            'd-m-Y',
                            strtotime($tanggal_akhir)
                        ); ?>

                    </strong>

                </p>


                <p>

                    Dicetak oleh:

                    <strong>

                        <?= htmlspecialchars(
                            $_SESSION['nama_lengkap'] ?? 'Owner'
                        ); ?>

                    </strong>

                </p>

            </div>


            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>No</th>

                            <th>ID Transaksi</th>

                            <th>Plat Nomor</th>

                            <th>Warna</th>

                            <th>Jenis Kendaraan</th>

                            <th>Waktu Keluar</th>

                            <th>Durasi</th>

                            <th>Biaya</th>

                            <th>Petugas</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no = 1;

                    if (
                        $data_transaksi &&
                        mysqli_num_rows(
                            $data_transaksi
                        ) > 0
                    ):

                    ?>


                        <?php while (
                            $row =
                            mysqli_fetch_assoc(
                                $data_transaksi
                            )
                        ): ?>


                            <tr>


                                <td>

                                    <?= $no++; ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['id_transaksi']
                                    ); ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['plat_nomor']
                                    ); ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['warna_kendaraan']
                                    ); ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['jenis_kendaraan']
                                    ); ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['waktu_keluar']
                                    ); ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['durasi_jam']
                                    ); ?>

                                    jam

                                </td>


                                <td>

                                    Rp

                                    <?= number_format(
                                        (float)$row['biaya_total'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['nama_lengkap']
                                        ?? '-'
                                    ); ?>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                        <!-- TOTAL -->

                        <tr class="total-row">


                            <td
                                colspan="7"
                                style="text-align:right;"
                            >

                                TOTAL PENDAPATAN

                            </td>


                            <td>

                                Rp
                                <?= number_format(
                                    $total_pendapatan,
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </td>


                            <td>

                                -

                            </td>


                        </tr>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="9"
                                class="empty"
                            >

                                Tidak ada transaksi
                                pada periode tanggal
                                yang dipilih.

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>


                </table>


            </div>


        </div>


    <?php endif; ?>


</div>


<script>

/*
|--------------------------------------------------------------------------
| VALIDASI TANGGAL DI BROWSER
|--------------------------------------------------------------------------
*/

const form =
    document.querySelector('.filter-form');

if (form) {

    form.addEventListener(
        'submit',
        function (event) {

            const awal =
                document.getElementById(
                    'tanggal_awal'
                ).value;

            const akhir =
                document.getElementById(
                    'tanggal_akhir'
                ).value;


            if (
                awal !== '' &&
                akhir !== '' &&
                awal > akhir
            ) {

                event.preventDefault();

                alert(
                    'Tanggal awal tidak boleh lebih besar dari tanggal akhir.'
                );

            }

        }
    );

}

</script>


</body>

</html>


<?php

if (
    isset($stmt) &&
    $stmt
) {

    mysqli_stmt_close($stmt);

}

?>
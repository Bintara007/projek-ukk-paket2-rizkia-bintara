<?php
session_start();
include '../koneksi.php';

// =====================================================
// CEK ID PARKIR
// =====================================================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID parkir tidak ditemukan.");
}

$id_parkir = (int) $_GET['id'];

// =====================================================
// AMBIL DATA PARKIR
// =====================================================
$query = mysqli_query($koneksi, "
    SELECT 
        p.*,
        t.tarif_per_jam
    FROM tb_parkir AS p
    LEFT JOIN tb_tarif AS t
        ON p.id_tarif = t.id_tarif
    WHERE p.id_parkir = '$id_parkir'
    LIMIT 1
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data parkir tidak ditemukan.");
}

// =====================================================
// CEK APAKAH SUDAH DIPROSES KELUAR
// =====================================================
$cek_transaksi = mysqli_query($koneksi, "
    SELECT id_transaksi
    FROM tb_transaksi
    WHERE id_parkir = '$id_parkir'
    LIMIT 1
");

if (mysqli_num_rows($cek_transaksi) > 0) {
    die("Kendaraan ini sudah diproses keluar.");
}

// =====================================================
// DATA PARKIR
// =====================================================
$plat_nomor       = $data['plat_nomor'];
$jenis_kendaraan  = $data['jenis_kendaraan'];
$waktu_masuk      = $data['waktu_masuk'];
$tarif_per_jam    = (float) $data['tarif_per_jam'];

// =====================================================
// WAKTU KELUAR
// =====================================================
$waktu_keluar = date('Y-m-d H:i:s');

// =====================================================
// HITUNG DURASI PARKIR
// =====================================================
$timestamp_masuk  = strtotime($waktu_masuk);
$timestamp_keluar = strtotime($waktu_keluar);

$selisih_detik = $timestamp_keluar - $timestamp_masuk;

// Minimal 1 jam
$durasi_jam = ceil($selisih_detik / 3600);

if ($durasi_jam < 1) {
    $durasi_jam = 1;
}

// =====================================================
// HITUNG TOTAL BIAYA
// =====================================================
$biaya_total = $durasi_jam * $tarif_per_jam;

// =====================================================
// ID USER YANG MEMPROSES
// =====================================================
$id_user = isset($_SESSION['id_user'])
    ? (int) $_SESSION['id_user']
    : 0;

// =====================================================
// SIMPAN KE TB_TRANSAKSI
// =====================================================
$stmt = mysqli_prepare($koneksi, "
    INSERT INTO tb_transaksi
    (
        id_parkir,
        waktu_keluar,
        durasi_jam,
        biaya_total,
        id_user
    )
    VALUES (?, ?, ?, ?, ?)
");

mysqli_stmt_bind_param(
    $stmt,
    "isidi",
    $id_parkir,
    $waktu_keluar,
    $durasi_jam,
    $biaya_total,
    $id_user
);

if (!mysqli_stmt_execute($stmt)) {
    die("Gagal menyimpan transaksi: " . mysqli_error($koneksi));
}

mysqli_stmt_close($stmt);

// =====================================================
// TAMPILKAN INFORMASI TRANSAKSI
// =====================================================
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Parkir - Proses Keluar</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .success {
            text-align: center;
            margin-bottom: 25px;
        }

        .icon {
            width: 70px;
            height: 70px;
            margin: auto;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            font-weight: bold;
        }

        .success h2 {
            margin: 15px 0 5px;
            color: #15803d;
        }

        .success p {
            margin: 0;
            color: #64748b;
        }

        .info {
            margin-top: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 13px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .row:last-child {
            border-bottom: none;
        }

        .label {
            color: #64748b;
        }

        .value {
            font-weight: 600;
            text-align: right;
        }

        .total {
            margin-top: 20px;
            padding: 20px;
            border-radius: 14px;
            background: #eff6ff;
            text-align: center;
        }

        .total span {
            display: block;
            color: #64748b;
            margin-bottom: 5px;
        }

        .total strong {
            font-size: 30px;
            color: #2563eb;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 13px;
            border-radius: 10px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        /* KHUSUS SAAT DICETAK (PRINT) */
        @media print {
            body {
                background: white;
            }
            .container {
                width: 100%;
                max-width: 100%;
                margin: 0;
            }
            .card {
                box-shadow: none;
                padding: 0;
            }
            .buttons {
                display: none; /* Tombol disembunyikan saat dicetak */
            }
        }

        @media (max-width: 500px) {
            .container {
                width: 94%;
                margin: 20px auto;
            }
            .card {
                padding: 20px;
            }
            .row {
                flex-direction: column;
                gap: 5px;
            }
            .value {
                text-align: left;
            }
            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="card">

        <!-- INFORMASI BERHASIL -->
        <div class="success">
            <div class="icon">✓</div>
            <h2>Proses Keluar Berhasil</h2>
            <p>Kendaraan berhasil diproses keluar.</p>
        </div>

        <!-- DETAIL KENDARAAN -->
        <div class="info">
            <div class="row">
                <div class="label">Plat Nomor</div>
                <div class="value"><?= htmlspecialchars($plat_nomor); ?></div>
            </div>
            <div class="row">
                <div class="label">Jenis Kendaraan</div>
                <div class="value"><?= htmlspecialchars($jenis_kendaraan); ?></div>
            </div>
            <div class="row">
                <div class="label">Waktu Masuk</div>
                <div class="value"><?= date('d-m-Y H:i', strtotime($waktu_masuk)); ?></div>
            </div>
            <div class="row">
                <div class="label">Waktu Keluar</div>
                <div class="value"><?= date('d-m-Y H:i', strtotime($waktu_keluar)); ?></div>
            </div>
            <div class="row">
                <div class="label">Durasi Parkir</div>
                <div class="value"><?= $durasi_jam; ?> Jam</div>
            </div>
            <div class="row">
                <div class="label">Tarif / Jam</div>
                <div class="value">Rp <?= number_format($tarif_per_jam, 0, ',', '.'); ?></div>
            </div>
        </div>

        <!-- TOTAL BIAYA -->
        <div class="total">
            <span>Total Biaya Parkir</span>
            <strong>Rp <?= number_format($biaya_total, 0, ',', '.'); ?></strong>
        </div>

        <!-- TOMBOL -->
        <div class="buttons">
            <!-- Tombol Cetak Manual -->
            <button onclick="window.print()" class="btn btn-success">Cetak Struk</button>
            
            <a href="../transaksi/tampil_transaksi.php" class="btn btn-primary">Lihat Transaksi</a>
            
            <a href="tampil_parkir.php" class="btn btn-secondary">Kembali ke Parkir</a>
        </div>

    </div>
</div>

</body>
</html>
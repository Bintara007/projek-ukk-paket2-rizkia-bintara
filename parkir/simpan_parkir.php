<?php

/*
|--------------------------------------------------------------------------
| KONEKSI DATABASE
|--------------------------------------------------------------------------
*/

include '../koneksi.php';


/*
|--------------------------------------------------------------------------
| CEK FORM
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: tampil_parkir.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| AMBIL DATA FORM
|--------------------------------------------------------------------------
*/

$id_member = $_POST['id_member'] ?? '';
$plat_nomor = $_POST['plat_nomor'] ?? '';
$warna_kendaraan = $_POST['warna_kendaraan'] ?? '';
$jenis_kendaraan = $_POST['jenis_kendaraan'] ?? '';
$waktu_masuk = $_POST['waktu_masuk'] ?? '';
$id_user_masuk = $_POST['id_user_masuk'] ?? '';


/*
|--------------------------------------------------------------------------
| VALIDASI JENIS KENDARAAN
|--------------------------------------------------------------------------
|
| Harus sesuai dengan ENUM pada database.
|
| Pilihan:
| motor
| mobil
| mini bus
| bus
|--------------------------------------------------------------------------
*/

$jenis_valid = [
    'motor',
    'mobil',
    'mini bus',
    'bus'
];


if (!in_array($jenis_kendaraan, $jenis_valid, true)) {

    die("Jenis kendaraan tidak valid.");

}


/*
|--------------------------------------------------------------------------
| CARI TARIF SECARA OTOMATIS
|--------------------------------------------------------------------------
|
| ID tarif TIDAK perlu dipercaya dari hidden input.
| Sistem mencari tarif berdasarkan jenis kendaraan.
|--------------------------------------------------------------------------
*/

$stmt_tarif = mysqli_prepare(
    $koneksi,
    "SELECT id_tarif
     FROM tb_tarif
     WHERE jenis_kendaraan = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt_tarif,
    "s",
    $jenis_kendaraan
);

mysqli_stmt_execute($stmt_tarif);

$result_tarif = mysqli_stmt_get_result($stmt_tarif);

$data_tarif = mysqli_fetch_assoc($result_tarif);

mysqli_stmt_close($stmt_tarif);


/*
|--------------------------------------------------------------------------
| CEK TARIF
|--------------------------------------------------------------------------
*/

if (!$data_tarif) {

    die(
        "Tarif untuk jenis kendaraan "
        . htmlspecialchars($jenis_kendaraan)
        . " belum tersedia."
    );

}


$id_tarif = $data_tarif['id_tarif'];


/*
|--------------------------------------------------------------------------
| SIMPAN DATA PARKIR
|--------------------------------------------------------------------------
|
| Perhatikan bahwa jenis_kendaraan ikut dimasukkan
| ke dalam query INSERT.
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $koneksi,
    "INSERT INTO tb_parkir
    (
        id_member,
        plat_nomor,
        warna_kendaraan,
        jenis_kendaraan,
        id_tarif,
        waktu_masuk,
        id_user_masuk
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?)"
);


mysqli_stmt_bind_param(
    $stmt,
    "isssisi",
    $id_member,
    $plat_nomor,
    $warna_kendaraan,
    $jenis_kendaraan,
    $id_tarif,
    $waktu_masuk,
    $id_user_masuk
);


/*
|--------------------------------------------------------------------------
| EKSEKUSI
|--------------------------------------------------------------------------
*/

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header(
        "Location: tampil_parkir.php?pesan="
        . urlencode(
            "Data parkir berhasil ditambahkan."
        )
    );

    exit;

} else {

    $error = mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    die(
        "Gagal menyimpan data parkir: "
        . $error
    );

}

?>
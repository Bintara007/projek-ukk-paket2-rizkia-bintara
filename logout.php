<?php

include 'koneksi.php';


/*
|--------------------------------------------------------------------------
| Ambil user yang sedang login
|--------------------------------------------------------------------------
*/

$id_user = $_SESSION['id_user'] ?? 0;


/*
|--------------------------------------------------------------------------
| Catat aktivitas logout
|--------------------------------------------------------------------------
*/

if ($id_user > 0) {

    $aktivitas = "Logout dari sistem";

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO tb_log_aktivitas
        (
            id_user,
            aktivitas,
            waktu_aktivitas
        )
        VALUES
        (
            ?,
            ?,
            NOW()
        )"
    );

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $id_user,
            $aktivitas
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }
}


/*
|--------------------------------------------------------------------------
| Hapus seluruh session
|--------------------------------------------------------------------------
*/

$_SESSION = [];


/*
|--------------------------------------------------------------------------
| Hapus cookie session
|--------------------------------------------------------------------------
*/

if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}


/*
|--------------------------------------------------------------------------
| Hancurkan session
|--------------------------------------------------------------------------
*/

session_destroy();


/*
|--------------------------------------------------------------------------
| Kembali ke login
|--------------------------------------------------------------------------
*/

header("Location: index.php");

exit;

?>
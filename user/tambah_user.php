<?php
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

$error = '';

/*
|--------------------------------------------------------------------------
| PROSES TAMBAH USER
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role         = $_POST['role'] ?? '';

    /*
     * STATUS OTOMATIS AKTIF
     * Tidak mengambil dari form.
     */
    $status_aktif = '1'; // 1 = Aktif, 0 = Nonaktif


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if (
        $nama_lengkap === '' ||
        $username === '' ||
        $password === '' ||
        $role === ''
    ) {
        $error = 'Semua data wajib diisi.';
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI ROLE
    |--------------------------------------------------------------------------
    */

    $role_valid = [
        'admin',
        'petugas',
        'owner'
    ];

    if (
        $error === '' &&
        !in_array($role, $role_valid, true)
    ) {
        $error = 'Role tidak valid.';
    }


    /*
    |--------------------------------------------------------------------------
    | CEK USERNAME
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id_user
             FROM tb_user
             WHERE username = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $username
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = 'Username sudah digunakan.';
        }

        mysqli_stmt_close($stmt);
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN USER
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        /*
         * Password disimpan menggunakan password_hash().
         */
        $password_hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );


        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO tb_user
            (
                nama_lengkap,
                username,
                password,
                role,
                status_aktif
            )
            VALUES (?, ?, ?, ?, ?)"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $nama_lengkap,
            $username,
            $password_hash,
            $role,
            $status_aktif
        );


        if (mysqli_stmt_execute($stmt)) {

            /*
            |--------------------------------------------------------------------------
            | LOG AKTIVITAS
            |--------------------------------------------------------------------------
            */

            $aktivitas =
                "Menambahkan user: " .
                $username .
                " dengan role " .
                $role;


            $stmt_log = mysqli_prepare(
                $koneksi,
                "INSERT INTO tb_log_aktivitas
                (
                    id_user,
                    aktivitas,
                    waktu_aktivitas
                )
                VALUES (?, ?, NOW())"
            );


            mysqli_stmt_bind_param(
                $stmt_log,
                "is",
                $id_user_login,
                $aktivitas
            );


            mysqli_stmt_execute($stmt_log);

            mysqli_stmt_close($stmt_log);


            mysqli_stmt_close($stmt);


            header(
                "Location: tampil_user.php?status=tambah"
            );

            exit;
        } else {

            $error =
                'Gagal menambahkan user: ' .
                mysqli_error($koneksi);
        }


        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Tambah User</title>

    <link
        rel="stylesheet"
        href="../assets/css/form.css">

</head>

<body>

    <div class="container">

        <div class="form-card">

            <div class="form-header">

                <h1>Tambah User</h1>

                <p>
                    Menambahkan pengguna baru ke sistem.
                </p>

            </div>
            <div class="table-toolbar">
                <div class="toolbar-left">
                    <?php
                    // Tentukan link default jika session tidak ditemukan
                    $dashboard_url = "../dashboard.php";

                    // Cek apakah session role/level sudah diset saat login
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin') {
                            $dashboard_url = "../dashboard_admin.php";
                        } elseif ($_SESSION['role'] == 'petugas') {
                            $dashboard_url = "../dashboard_petugas.php";
                        } elseif ($_SESSION['role'] == 'owner') {
                            $dashboard_url = "../dashboard_owner.php";
                        }
                    }
                    ?>

                    <!-- Cetak link secara dinamis -->
                    <a href="<?php echo $dashboard_url; ?>" class="btn btn-primary">
                        Dashboard
                    </a>
                </div>
            </div>


            <div class="form-body">


                <?php if ($error !== ''): ?>

                    <div class="form-alert form-alert-error">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>


                <div class="info-box">

                    User baru akan otomatis memiliki status
                    <strong>Aktif</strong>.

                </div>


                <form method="POST">


                    <div class="form-grid">


                        <!-- NAMA -->

                        <div class="form-group">

                            <label>
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                name="nama_lengkap"
                                value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>"
                                required>

                        </div>


                        <!-- USERNAME -->

                        <div class="form-group">

                            <label>
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                required>

                        </div>


                        <!-- PASSWORD -->

                        <div class="form-group">

                            <label>
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                required>

                        </div>


                        <!-- ROLE -->

                        <div class="form-group">

                            <label>
                                Role
                            </label>

                            <select
                                name="role"
                                required>

                                <option value="">
                                    -- Pilih Role --
                                </option>

                                <option
                                    value="admin"
                                    <?= (($_POST['role'] ?? '') === 'admin')
                                        ? 'selected'
                                        : '' ?>>
                                    Admin
                                </option>

                                <option
                                    value="petugas"
                                    <?= (($_POST['role'] ?? '') === 'petugas')
                                        ? 'selected'
                                        : '' ?>>
                                    Petugas
                                </option>

                                <option
                                    value="owner"
                                    <?= (($_POST['role'] ?? '') === 'owner')
                                        ? 'selected'
                                        : '' ?>>
                                    Owner
                                </option>

                            </select>

                        </div>


                        <!-- STATUS -->

                        <div class="form-group">

                            <label>
                                Status
                            </label>

                            <input
                                type="text"
                                value="Aktif"
                                readonly
                                class="readonly">

                        </div>


                    </div>


                    <div class="form-actions">

                        <a
                            href="tampil_user.php"
                            class="btn btn-secondary">
                            Kembali
                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary">
                            Simpan User
                        </button>

                    </div>


                </form>

            </div>

        </div>

    </div>

</body>

</html>
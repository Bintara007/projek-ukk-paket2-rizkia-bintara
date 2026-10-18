<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id_user_login = (int) $_SESSION['id_user'];

$id_user = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id_user <= 0) {
    header("Location: tampil_user.php");
    exit;
}

/* =========================================================
   AMBIL DATA USER
   ========================================================= */

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT
        id_user,
        nama_lengkap,
        username,
        role,
        status_aktif
     FROM tb_user
     WHERE id_user = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_user
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: tampil_user.php");
    exit;
}

$error = '';

/* =========================================================
   UPDATE
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role         = $_POST['role'] ?? '';
    $status_aktif = $_POST['status_aktif'] ?? '';

    if (
        $nama_lengkap === '' ||
        $username === '' ||
        $role === '' ||
        $status_aktif === ''
    ) {
        $error = 'Data wajib diisi.';
    }

    $role_valid = ['admin', 'petugas', 'owner'];
    $status_valid = ['aktif', 'nonaktif'];

    if ($error === '' && !in_array($role, $role_valid, true)) {
        $error = 'Role tidak valid.';
    }

    if ($error === '' && !in_array($status_aktif, $status_valid, true)) {
        $error = 'Status tidak valid.';
    }

    /* Cek username */

    if ($error === '') {

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id_user
             FROM tb_user
             WHERE username = ?
             AND id_user != ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $username,
            $id_user
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = 'Username sudah digunakan user lain.';
        }

        mysqli_stmt_close($stmt);
    }

    if ($error === '') {

        if ($password !== '') {

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE tb_user
                 SET
                    nama_lengkap = ?,
                    username = ?,
                    password = ?,
                    role = ?,
                    status_aktif = ?
                 WHERE id_user = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssssi",
                $nama_lengkap,
                $username,
                $password_hash,
                $role,
                $status_aktif,
                $id_user
            );
        } else {

            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE tb_user
                 SET
                    nama_lengkap = ?,
                    username = ?,
                    role = ?,
                    status_aktif = ?
                 WHERE id_user = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssssi",
                $nama_lengkap,
                $username,
                $role,
                $status_aktif,
                $id_user
            );
        }

        if (mysqli_stmt_execute($stmt)) {

            $aktivitas =
                "Mengubah data user: " . $username;

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

            header("Location: tampil_user.php?status=edit");
            exit;
        }

        $error = 'Gagal mengubah user: ' .
            mysqli_error($koneksi);

        mysqli_stmt_close($stmt);
    }

    $data['nama_lengkap'] = $nama_lengkap;
    $data['username'] = $username;
    $data['role'] = $role;
    $data['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit User</title>

    <link
        rel="stylesheet"
        href="../assets/css/form.css">

</head>

<body>

    <div class="container">

        <div class="form-card">

            <div class="form-header">

                <h1>Edit User</h1>

                <p>
                    Mengubah data pengguna sistem.
                </p>

            </div>

            <div class="form-body">

                <?php if ($error !== ''): ?>

                    <div class="form-alert form-alert-error">
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endif; ?>


                <form method="POST">

                    <div class="form-grid">

                        <div class="form-group">

                            <label>
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                name="nama_lengkap"
                                value="<?= htmlspecialchars($data['nama_lengkap']) ?>"
                                required>

                        </div>


                        <div class="form-group">

                            <label>
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                value="<?= htmlspecialchars($data['username']) ?>"
                                required>

                        </div>


                        <div class="form-group">

                            <label>
                                Password Baru
                            </label>

                            <input
                                type="password"
                                name="password"
                                placeholder="Kosongkan jika tidak ingin mengubah">

                        </div>


                        <div class="form-group">

                            <label>
                                Role
                            </label>

                            <select name="role" required>

                                <option
                                    value="admin"
                                    <?= $data['role'] === 'admin' ? 'selected' : '' ?>>
                                    Admin
                                </option>

                                <option
                                    value="petugas"
                                    <?= $data['role'] === 'petugas' ? 'selected' : '' ?>>
                                    Petugas
                                </option>

                                <option
                                    value="owner"
                                    <?= $data['role'] === 'owner' ? 'selected' : '' ?>>
                                    Owner
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label>
                                Status
                            </label>

                            <select name="status_aktif" required>

                                <option
                                    value="1"
                                    <?= (int)$data['status_aktif'] === 1 ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option
                                    value="0"
                                    <?= (int)$data['status_aktif'] === 0 ? 'selected' : '' ?>>
                                    Nonaktif
                                </option>

                            </select>

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
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>
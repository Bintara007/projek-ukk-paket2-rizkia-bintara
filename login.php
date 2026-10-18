<?php
// ============================================================
// login.php
// Login Sistem Parkir
// Status akun menggunakan nilai NUMERIC:
// 1 = Aktif
// 0 = Tidak Aktif
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';

// ============================================================
// JIKA SUDAH LOGIN, LANGSUNG ARAHKAN KE DASHBOARD SESUAI ROLE
// ============================================================
if (isset($_SESSION['id_user'], $_SESSION['role'])) {

    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard_admin.php');
        exit;
    }

    if ($_SESSION['role'] === 'petugas') {
        header('Location: dashboard_petugas.php');
        exit;
    }

    if ($_SESSION['role'] === 'owner') {
        header('Location: dashboard_owner.php');
        exit;
    }
}

// ============================================================
// VARIABEL
// ============================================================
$error = '';
$success = '';

// ============================================================
// PROSES LOGIN
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input
    if ($username === '' || $password === '') {

        $error = 'Username dan password wajib diisi.';

    } else {

        // ====================================================
        // AMBIL DATA USER
        // ====================================================
        $sql = "
            SELECT
                id_user,
                nama_lengkap,
                username,
                password,
                role,
                status_aktif
            FROM tb_user
            WHERE username = ?
            LIMIT 1
        ";

        $stmt = mysqli_prepare($koneksi, $sql);

        if (!$stmt) {

            $error = 'Terjadi kesalahan pada sistem database.';

        } else {

            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            // =================================================
            // USER TIDAK DITEMUKAN
            // =================================================
            if (!$user) {

                $error = 'Username atau password salah.';

            } else {

                $passwordDatabase = (string) $user['password'];
                $passwordBenar = false;

                // =================================================
                // CEK PASSWORD HASH
                // =================================================
                if (
                    $passwordDatabase !== '' &&
                    password_verify($password, $passwordDatabase)
                ) {

                    $passwordBenar = true;

                }

                // =================================================
                // KOMPATIBILITAS PASSWORD PLAIN TEXT
                // Dipakai jika database lama masih menyimpan password
                // dalam bentuk biasa.
                // =================================================
                elseif (
                    hash_equals(
                        $passwordDatabase,
                        (string) $password
                    )
                ) {

                    $passwordBenar = true;
                }

                // =================================================
                // PASSWORD SALAH
                // =================================================
                if (!$passwordBenar) {

                    $error = 'Username atau password salah.';

                }

                // =================================================
                // PASSWORD BENAR
                // =================================================
                else {

                    // =================================================
                    // CEK STATUS AKUN
                    //
                    // Database menggunakan:
                    // 1 = aktif
                    // 0 = tidak aktif
                    // =================================================
                    if ((int) $user['status_aktif'] !== 1) {

                        $error = 'Akun Anda tidak aktif. Silakan hubungi administrator.';

                    } else {

                        // =================================================
                        // REGENERATE SESSION ID
                        // =================================================
                        session_regenerate_id(true);

                        // =================================================
                        // SIMPAN SESSION
                        // =================================================
                        $_SESSION['id_user'] = (int) $user['id_user'];
                        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];

                        // =================================================
                        // SIMPAN LOG LOGIN
                        // =================================================
                        $aktivitas = 'Login ke sistem';

                        $stmtLog = mysqli_prepare(
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

                        if ($stmtLog) {

                            $idUserLog = (int) $user['id_user'];

                            mysqli_stmt_bind_param(
                                $stmtLog,
                                'is',
                                $idUserLog,
                                $aktivitas
                            );

                            mysqli_stmt_execute($stmtLog);
                            mysqli_stmt_close($stmtLog);
                        }

                        // =================================================
                        // REDIRECT SESUAI ROLE
                        // =================================================
                        if ($user['role'] === 'admin') {

                            header('Location: dashboard_admin.php');
                            exit;

                        } elseif ($user['role'] === 'petugas') {

                            header('Location: dashboard_petugas.php');
                            exit;

                        } elseif ($user['role'] === 'owner') {

                            header('Location: dashboard_owner.php');
                            exit;

                        } else {

                            // Jika role tidak dikenali
                            session_unset();
                            session_destroy();

                            $error = 'Role akun tidak dikenali.';
                        }
                    }
                }
            }
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

    <title>Login - Sistem Parkir</title>

    <!-- ======================================================
         BOOTSTRAP ICONS
         ====================================================== -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Helvetica,
                Arial,
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #061d3f 0%,
                    #073b75 45%,
                    #0b4f9c 100%
                );

            color: #172033;

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 25px;

            overflow-x: hidden;
        }

        /* =====================================================
           BACKGROUND DECORATION
           ===================================================== */

        body::before {
            content: "";
            position: fixed;

            width: 500px;
            height: 500px;

            border-radius: 50%;

            background: rgba(245, 196, 0, 0.08);

            top: -220px;
            left: -180px;

            pointer-events: none;
        }

        body::after {
            content: "";

            position: fixed;

            width: 450px;
            height: 450px;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.05);

            right: -180px;
            bottom: -200px;

            pointer-events: none;
        }

        /* =====================================================
           LOGIN CONTAINER
           ===================================================== */

        .login-wrapper {
            width: 100%;
            max-width: 1050px;

            min-height: 610px;

            background: #ffffff;

            border-radius: 28px;

            overflow: hidden;

            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                minmax(0, 1fr);

            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.28);

            position: relative;

            z-index: 2;

            animation: loginAppear 0.7s ease;
        }

        @keyframes loginAppear {

            from {
                opacity: 0;
                transform: translateY(25px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* =====================================================
           LEFT PANEL
           ===================================================== */

        .login-left {

            position: relative;

            padding: 55px;

            color: #ffffff;

            display: flex;

            flex-direction: column;

            justify-content: center;

            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    #061d3f 0%,
                    #073b75 48%,
                    #0b4f9c 100%
                );
        }

        .login-left::before {

            content: "";

            position: absolute;

            width: 350px;
            height: 350px;

            border-radius: 50%;

            background: rgba(255,255,255,0.04);

            top: -130px;
            right: -130px;
        }

        .login-left::after {

            content: "";

            position: absolute;

            width: 280px;
            height: 280px;

            border-radius: 50%;

            background: rgba(245,196,0,0.08);

            bottom: -130px;
            left: -100px;
        }

        .brand {

            position: relative;

            z-index: 2;

            margin-bottom: 40px;
        }

        .logo-box {

            width: 105px;
            height: 105px;

            background: rgba(255,255,255,0.97);

            border-radius: 25px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 13px;

            margin-bottom: 25px;

            box-shadow:
                0 15px 35px rgba(0,0,0,0.22);
        }

        .logo-box img {

            width: 100%;
            height: 100%;

            object-fit: contain;

            display: block;
        }

        .brand h1 {

            font-size: 38px;

            line-height: 1.15;

            font-weight: 800;

            letter-spacing: -1px;

            margin-bottom: 12px;
        }

        .brand h1 span {

            color: #f5c400;
        }

        .brand p {

            color: rgba(255,255,255,0.78);

            font-size: 16px;

            line-height: 1.7;

            max-width: 430px;
        }

        .feature-list {

            position: relative;

            z-index: 2;

            display: flex;

            flex-direction: column;

            gap: 16px;

            margin-top: 10px;
        }

        .feature {

            display: flex;

            align-items: center;

            gap: 14px;

            color: rgba(255,255,255,0.9);

            font-size: 14px;

            font-weight: 500;
        }

        .feature-icon {

            width: 40px;
            height: 40px;

            min-width: 40px;

            border-radius: 12px;

            display: flex;

            align-items: center;
            justify-content: center;

            background: rgba(255,255,255,0.1);

            color: #f5c400;

            font-size: 18px;
        }

        /* =====================================================
           RIGHT PANEL
           ===================================================== */

        .login-right {

            padding: 55px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #ffffff;
        }

        .login-form {

            width: 100%;

            max-width: 390px;
        }

        .form-heading {

            margin-bottom: 32px;
        }

        .form-heading h2 {

            color: #073b75;

            font-size: 30px;

            font-weight: 800;

            margin-bottom: 8px;

            letter-spacing: -0.5px;
        }

        .form-heading p {

            color: #7a8494;

            font-size: 14px;

            line-height: 1.6;
        }

        /* =====================================================
           ALERT
           ===================================================== */

        .alert {

            padding: 14px 16px;

            border-radius: 13px;

            margin-bottom: 22px;

            display: flex;

            align-items: flex-start;

            gap: 11px;

            font-size: 13px;

            line-height: 1.5;
        }

        .alert-error {

            color: #a4262c;

            background: #fff1f2;

            border: 1px solid #ffd5d8;
        }

        .alert i {

            font-size: 18px;

            margin-top: 1px;
        }

        /* =====================================================
           FORM GROUP
           ===================================================== */

        .form-group {

            margin-bottom: 20px;
        }

        .form-label {

            display: block;

            color: #263449;

            font-size: 13px;

            font-weight: 700;

            margin-bottom: 9px;
        }

        .input-wrapper {

            position: relative;
        }

        .input-icon {

            position: absolute;

            left: 15px;
            top: 50%;

            transform: translateY(-50%);

            color: #8b96a8;

            font-size: 18px;

            pointer-events: none;

            transition: 0.2s ease;
        }

        .form-input {

            width: 100%;

            height: 53px;

            border: 1px solid #dce2ea;

            border-radius: 13px;

            outline: none;

            padding:
                0 48px
                0 45px;

            font-size: 14px;

            color: #1e293b;

            background: #f9fbfd;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }

        .form-input:hover {

            border-color: #b8c5d6;

            background: #ffffff;
        }

        .form-input:focus {

            border-color: #0b4f9c;

            background: #ffffff;

            box-shadow:
                0 0 0 4px rgba(11,79,156,0.10);
        }

        .form-input:focus + .input-icon {

            color: #0b4f9c;
        }

        .password-toggle {

            position: absolute;

            right: 14px;
            top: 50%;

            transform: translateY(-50%);

            border: 0;

            background: transparent;

            color: #8b96a8;

            width: 32px;
            height: 32px;

            border-radius: 8px;

            cursor: pointer;

            display: flex;

            align-items: center;
            justify-content: center;

            transition: 0.2s ease;
        }

        .password-toggle:hover {

            color: #0b4f9c;

            background: #edf5ff;
        }

        /* =====================================================
           LOGIN BUTTON
           ===================================================== */

        .login-button {

            width: 100%;

            height: 54px;

            border: none;

            border-radius: 13px;

            cursor: pointer;

            margin-top: 7px;

            background:
                linear-gradient(
                    135deg,
                    #073b75,
                    #0b4f9c
                );

            color: #ffffff;

            font-size: 14px;

            font-weight: 700;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 9px;

            box-shadow:
                0 10px 22px rgba(7,59,117,0.22);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .login-button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 14px 28px rgba(7,59,117,0.30);
        }

        .login-button:active {

            transform: translateY(0);

            box-shadow:
                0 7px 15px rgba(7,59,117,0.20);
        }

        .login-button i {

            font-size: 17px;
        }

        /* =====================================================
           FOOTER
           ===================================================== */

        .login-footer {

            text-align: center;

            margin-top: 25px;

            color: #9aa4b2;

            font-size: 12px;

            line-height: 1.6;
        }

        .login-footer strong {

            color: #073b75;
        }

        /* =====================================================
           RESPONSIVE
           ===================================================== */

        @media (max-width: 850px) {

            body {
                padding: 15px;
            }

            .login-wrapper {

                grid-template-columns: 1fr;

                max-width: 550px;
            }

            .login-left {

                padding: 40px;

                min-height: 390px;
            }

            .login-right {

                padding: 40px;
            }

            .brand h1 {

                font-size: 32px;
            }

            .feature-list {

                gap: 12px;
            }
        }

        @media (max-width: 500px) {

            body {
                padding: 10px;
            }

            .login-wrapper {

                border-radius: 20px;
            }

            .login-left {

                padding: 32px 25px;

                min-height: 350px;
            }

            .login-right {

                padding: 32px 25px;
            }

            .logo-box {

                width: 82px;
                height: 82px;

                border-radius: 20px;
            }

            .brand {

                margin-bottom: 25px;
            }

            .brand h1 {

                font-size: 27px;
            }

            .brand p {

                font-size: 14px;
            }

            .feature {

                font-size: 13px;
            }

            .feature-icon {

                width: 35px;
                height: 35px;

                min-width: 35px;

                border-radius: 10px;
            }

            .form-heading h2 {

                font-size: 26px;
            }
        }

    </style>

</head>

<body>

    <div class="login-wrapper">

        <!-- ==================================================
             PANEL KIRI
             ================================================== -->
        <div class="login-left">

            <div class="brand">

                <div class="logo-box">

                    <img
                        src="assets/img/logo-persib.png"
                        alt="Logo Persib Bandung"
                        onerror="this.style.display='none';"
                    >

                </div>

                <h1>
                    Sistem <span>Parkir</span>
                </h1>

                <p>
                    Sistem pengelolaan parkir untuk membantu
                    mengatur kendaraan, member, tarif,
                    transaksi, dan aktivitas pengguna.
                </p>

            </div>

            <div class="feature-list">

                <div class="feature">

                    <div class="feature-icon">
                        <i class="bi bi-car-front-fill"></i>
                    </div>

                    <span>
                        Kelola kendaraan masuk dan keluar
                    </span>

                </div>

                <div class="feature">

                    <div class="feature-icon">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <span>
                        Catat transaksi parkir secara terstruktur
                    </span>

                </div>

                <div class="feature">

                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <span>
                        Hak akses berdasarkan role pengguna
                    </span>

                </div>

            </div>

        </div>


        <!-- ==================================================
             PANEL KANAN
             ================================================== -->
        <div class="login-right">

            <div class="login-form">

                <div class="form-heading">

                    <h2>
                        Selamat Datang
                    </h2>

                    <p>
                        Silakan masuk menggunakan akun Anda
                        untuk melanjutkan ke sistem.
                    </p>

                </div>


                <!-- ==================================================
                     PESAN ERROR
                     ================================================== -->
                <?php if ($error !== ''): ?>

                    <div class="alert alert-error">

                        <i class="bi bi-exclamation-circle-fill"></i>

                        <span>
                            <?= htmlspecialchars($error) ?>
                        </span>

                    </div>

                <?php endif; ?>


                <!-- ==================================================
                     FORM LOGIN
                     ================================================== -->
                <form
                    method="POST"
                    action=""
                    autocomplete="off"
                >

                    <!-- USERNAME -->
                    <div class="form-group">

                        <label
                            for="username"
                            class="form-label"
                        >
                            Username
                        </label>

                        <div class="input-wrapper">

                            <input
                                type="text"
                                name="username"
                                id="username"
                                class="form-input"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                maxlength="100"
                                required
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            >

                            <i class="bi bi-person input-icon"></i>

                        </div>

                    </div>


                    <!-- PASSWORD -->
                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Password
                        </label>

                        <div class="input-wrapper">

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-input"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >

                            <i class="bi bi-lock input-icon"></i>

                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Tampilkan password"
                            >
                                <i
                                    class="bi bi-eye"
                                    id="passwordIcon"
                                ></i>
                            </button>

                        </div>

                    </div>


                    <!-- BUTTON LOGIN -->
                    <button
                        type="submit"
                        class="login-button"
                    >

                        <i class="bi bi-box-arrow-in-right"></i>

                        <span>
                            Masuk ke Sistem
                        </span>

                    </button>

                </form>


                <div class="login-footer">

                    Sistem Parkir
                    <strong>•</strong>
                    Manajemen Parkir Terintegrasi

                </div>

            </div>

        </div>

    </div>


    <!-- ======================================================
         JAVASCRIPT
         ====================================================== -->
    <script>

        // =====================================================
        // TOGGLE PASSWORD
        // =====================================================

        const togglePassword =
            document.getElementById('togglePassword');

        const password =
            document.getElementById('password');

        const passwordIcon =
            document.getElementById('passwordIcon');


        if (
            togglePassword &&
            password &&
            passwordIcon
        ) {

            togglePassword.addEventListener(
                'click',
                function () {

                    if (password.type === 'password') {

                        password.type = 'text';

                        passwordIcon.classList.remove(
                            'bi-eye'
                        );

                        passwordIcon.classList.add(
                            'bi-eye-slash'
                        );

                        togglePassword.setAttribute(
                            'aria-label',
                            'Sembunyikan password'
                        );

                    } else {

                        password.type = 'password';

                        passwordIcon.classList.remove(
                            'bi-eye-slash'
                        );

                        passwordIcon.classList.add(
                            'bi-eye'
                        );

                        togglePassword.setAttribute(
                            'aria-label',
                            'Tampilkan password'
                        );
                    }

                }
            );

        }

    </script>

</body>

</html>
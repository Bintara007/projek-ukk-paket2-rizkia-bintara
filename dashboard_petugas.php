<?php
// ============================================================
// dashboard_petugas.php
// Dashboard khusus Petugas
// Menu petugas:
// - Dashboard
// - Data Parkir
// - Transaksi
// - Logout
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';

// ============================================================
// CEK LOGIN
// ============================================================

if (!isset($_SESSION['id_user'], $_SESSION['role'])) {
    header('Location: index.php');
    exit;
}

// ============================================================
// CEK ROLE
// ============================================================

if ($_SESSION['role'] !== 'petugas') {
    header('Location: index.php');
    exit;
}

// ============================================================
// DATA USER LOGIN
// ============================================================

$nama_user = $_SESSION['nama_lengkap'] ?? 'Petugas';
$username  = $_SESSION['username'] ?? 'petugas';

// ============================================================
// FUNGSI MENGAMBIL JUMLAH DATA
// ============================================================

function jumlah_data_petugas($koneksi, $sql)
{
    $result = mysqli_query($koneksi, $sql);

    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_assoc($result);

    return (int) ($row['jumlah'] ?? 0);
}

// ============================================================
// TOTAL KENDARAAN YANG MASIH PARKIR
// Kendaraan dianggap masih parkir jika belum memiliki
// transaksi keluar.
// ============================================================

$total_parkir = jumlah_data_petugas(
    $koneksi,
    "
    SELECT COUNT(*) AS jumlah
    FROM tb_parkir AS p
    LEFT JOIN tb_transaksi AS t
        ON p.id_parkir = t.id_parkir
    WHERE t.id_parkir IS NULL
    "
);

// ============================================================
// TOTAL TRANSAKSI
// ============================================================

$total_transaksi = jumlah_data_petugas(
    $koneksi,
    "
    SELECT COUNT(*) AS jumlah
    FROM tb_transaksi
    "
);

// ============================================================
// KENDARAAN MASUK HARI INI
// ============================================================

$kendaraan_hari_ini = jumlah_data_petugas(
    $koneksi,
    "
    SELECT COUNT(*) AS jumlah
    FROM tb_parkir
    WHERE DATE(waktu_masuk) = CURDATE()
    "
);

// ============================================================
// TRANSAKSI HARI INI
// ============================================================

$transaksi_hari_ini = jumlah_data_petugas(
    $koneksi,
    "
    SELECT COUNT(*) AS jumlah
    FROM tb_transaksi
    WHERE DATE(waktu_keluar) = CURDATE()
    "
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Petugas - Sistem Parkir</title>

    <!-- ======================================================
         BOOTSTRAP ICONS
         ====================================================== -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- ======================================================
         CSS DASHBOARD
         ====================================================== -->
    <link
        rel="stylesheet"
        href="assets/css/dashboard.css"
    >

</head>

<body>

<div class="dashboard-wrapper">

    <!-- ======================================================
         SIDEBAR
         ====================================================== -->

    <aside class="sidebar">

        <!-- BRAND -->
        <div class="brand">

            <div class="brand-icon">
                <i class="bi bi-car-front-fill"></i>
            </div>

            <div>

                <div class="brand-title">
                    Sistem Parkir
                </div>

                <div class="brand-subtitle">
                    Parking Management
                </div>

            </div>

        </div>


        <!-- ROLE -->
        <div class="sidebar-role">

            <span class="role-dot"></span>

            PETUGAS

        </div>


        <!-- MENU -->
        <nav class="sidebar-menu">

            <!-- DASHBOARD -->
            <a
                href="dashboard_petugas.php"
                class="menu-item active"
            >

                <span class="menu-icon">
                    <i class="bi bi-grid-1x2-fill"></i>
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            <!-- DATA PARKIR -->
            <a
                href="parkir/tampil_parkir.php"
                class="menu-item"
            >

                <span class="menu-icon">
                    <i class="bi bi-car-front-fill"></i>
                </span>

                <span>
                    Data Parkir
                </span>

            </a>


            <!-- TRANSAKSI -->
            <a
                href="transaksi/tampil_transaksi.php"
                class="menu-item"
            >

                <span class="menu-icon">
                    <i class="bi bi-receipt"></i>
                </span>

                <span>
                    Transaksi
                </span>

            </a>

        </nav>


        <!-- SIDEBAR BOTTOM -->
        <div class="sidebar-bottom">

            <a
                href="logout.php"
                class="logout-button"
                onclick="return confirm('Apakah Anda yakin ingin keluar?');"
            >

                <span class="menu-icon">
                    <i class="bi bi-box-arrow-right"></i>
                </span>

                <span>
                    Logout
                </span>

            </a>

        </div>

    </aside>


    <!-- ======================================================
         MAIN CONTENT
         ====================================================== -->

    <main class="main-content">

        <!-- ==================================================
             TOPBAR
             ================================================== -->

        <header class="topbar">

            <div>

                <div class="breadcrumb">

                    <i class="bi bi-house-door-fill"></i>

                    <span>Dashboard</span>

                    <i class="bi bi-chevron-right"></i>

                    <span>Petugas</span>

                </div>

                <h1 class="dashboard-title">
                    Dashboard Petugas
                </h1>

            </div>


            <!-- PROFILE -->
            <div class="profile-box">

                <div class="profile-avatar">

                    <?= strtoupper(
                        substr(
                            $nama_user,
                            0,
                            1
                        )
                    ) ?>

                </div>

                <div class="profile-info">

                    <strong>
                        <?= htmlspecialchars($nama_user) ?>
                    </strong>

                    <span>
                        Petugas
                    </span>

                </div>

            </div>

        </header>


        <!-- ==================================================
             WELCOME CARD
             ================================================== -->

        <section class="welcome-card">

            <div>

                <div class="welcome-label">
                    SELAMAT DATANG
                </div>

                <h2>
                    Halo, <?= htmlspecialchars($nama_user) ?> 👋
                </h2>

                <p>
                    Kelola kendaraan masuk, kendaraan keluar,
                    dan transaksi parkir melalui dashboard ini.
                </p>

            </div>


            <div class="welcome-decoration">

                <i class="bi bi-car-front-fill"></i>

            </div>

        </section>


        <!-- ==================================================
             STATISTIK
             ================================================== -->

        <section class="section">

            <div class="section-heading">

                <div>

                    <h2>
                        Ringkasan Parkir
                    </h2>

                    <p>
                        Informasi aktivitas parkir
                    </p>

                </div>

            </div>


            <div class="stat-grid">

                <!-- ==========================================
                     PARKIR AKTIF
                     ========================================== -->

                <div class="stat-card blue">

                    <div class="stat-icon">

                        <i class="bi bi-car-front-fill"></i>

                    </div>

                    <div class="stat-content">

                        <span>
                            Parkir Aktif
                        </span>

                        <strong>
                            <?= number_format($total_parkir, 0, ',', '.') ?>
                        </strong>

                    </div>

                </div>


                <!-- ==========================================
                     TOTAL TRANSAKSI
                     ========================================== -->

                <div class="stat-card green">

                    <div class="stat-icon">

                        <i class="bi bi-receipt"></i>

                    </div>

                    <div class="stat-content">

                        <span>
                            Total Transaksi
                        </span>

                        <strong>
                            <?= number_format($total_transaksi, 0, ',', '.') ?>
                        </strong>

                    </div>

                </div>


                <!-- ==========================================
                     KENDARAAN MASUK HARI INI
                     ========================================== -->

                <div class="stat-card yellow">

                    <div class="stat-icon">

                        <i class="bi bi-box-arrow-in-right"></i>

                    </div>

                    <div class="stat-content">

                        <span>
                            Masuk Hari Ini
                        </span>

                        <strong>
                            <?= number_format($kendaraan_hari_ini, 0, ',', '.') ?>
                        </strong>

                    </div>

                </div>


                <!-- ==========================================
                     TRANSAKSI HARI INI
                     ========================================== -->

                <div class="stat-card blue">

                    <div class="stat-icon">

                        <i class="bi bi-calendar-check"></i>

                    </div>

                    <div class="stat-content">

                        <span>
                            Transaksi Hari Ini
                        </span>

                        <strong>
                            <?= number_format($transaksi_hari_ini, 0, ',', '.') ?>
                        </strong>

                    </div>

                </div>

            </div>

        </section>


        <!-- ==================================================
             MENU CEPAT
             HANYA DATA PARKIR DAN TRANSAKSI
             ================================================== -->

        <section class="section">

            <div class="section-heading">

                <div>

                    <h2>
                        Menu Petugas
                    </h2>

                    <p>
                        Akses fitur yang tersedia untuk petugas
                    </p>

                </div>

            </div>


            <div class="quick-grid">

                <!-- ==========================================
                     DATA PARKIR
                     ========================================== -->

                <a
                    href="parkir/tampil_parkir.php"
                    class="quick-card"
                >

                    <div class="quick-icon blue-icon">

                        <i class="bi bi-car-front-fill"></i>

                    </div>

                    <div>

                        <strong>
                            Data Parkir
                        </strong>

                        <span>
                            Kelola kendaraan yang sedang parkir
                        </span>

                    </div>

                    <div class="arrow">

                        <i class="bi bi-arrow-right"></i>

                    </div>

                </a>


                <!-- ==========================================
                     TRANSAKSI
                     ========================================== -->

                <a
                    href="transaksi/tampil_transaksi.php"
                    class="quick-card"
                >

                    <div class="quick-icon green-icon">

                        <i class="bi bi-receipt"></i>

                    </div>

                    <div>

                        <strong>
                            Transaksi
                        </strong>

                        <span>
                            Lihat seluruh transaksi parkir
                        </span>

                    </div>

                    <div class="arrow">

                        <i class="bi bi-arrow-right"></i>

                    </div>

                </a>

            </div>

        </section>


        <!-- ==================================================
             FOOTER
             ================================================== -->

        <footer class="dashboard-footer">

            <span>
                © <?= date('Y') ?> Sistem Parkir
            </span>

            <span>
                Login sebagai <strong>Petugas</strong>
            </span>

        </footer>

    </main>

</div>

</body>

</html>
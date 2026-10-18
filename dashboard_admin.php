<?php
session_start();

include 'koneksi.php';

/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| CEK ROLE
|--------------------------------------------------------------------------
*/
if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}

$nama_user = $_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Administrator';


/*
|--------------------------------------------------------------------------
| FUNGSI HITUNG DATA
|--------------------------------------------------------------------------
*/
function jumlah_data($koneksi, $query)
{
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        return 0;
    }

    $data = mysqli_fetch_assoc($result);

    return (int)($data['jumlah'] ?? 0);
}


/*
|--------------------------------------------------------------------------
| STATISTIK DASHBOARD
|--------------------------------------------------------------------------
*/

/* Total user */
$total_user = jumlah_data(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM tb_user"
);

/* Total member */
$total_member = jumlah_data(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM tb_member"
);

/* Total area */
$total_area = jumlah_data(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM tb_area_parkir"
);

/* Total tarif */
$total_tarif = jumlah_data(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM tb_tarif"
);

/*
|--------------------------------------------------------------------------
| PARKIR AKTIF
|--------------------------------------------------------------------------
*/
$total_parkir_aktif = jumlah_data(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM tb_parkir AS p
     LEFT JOIN tb_transaksi AS t
        ON p.id_parkir = t.id_parkir
     WHERE t.id_parkir IS NULL"
);


/* Total transaksi */
$total_transaksi = jumlah_data(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM tb_transaksi"
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

    <title>Dashboard Admin - PARKIR</title>

    <!-- CSS DASHBOARD -->
    <link
        rel="stylesheet"
        href="assets/css/dashboard.css?v=4"
    >

    <!-- BOOTSTRAP ICONS -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>

<body>

<div class="dashboard-wrapper">

    <!-- =====================================================
         SIDEBAR
         ===================================================== -->
    <aside class="sidebar">

        <!-- BRAND -->
        <div class="brand">

            <div class="brand-icon">
                <i class="bi bi-p-square-fill"></i>
            </div>

            <div>
                <div class="brand-title">
                    PARKIR
                </div>

                <div class="brand-subtitle">
                    Management System
                </div>
            </div>

        </div>


        <!-- ROLE -->
        <div class="sidebar-role">

            <span class="role-dot"></span>

            Administrator

        </div>


        <!-- MENU -->
        <nav class="sidebar-menu">

            <a
                href="dashboard_admin.php"
                class="menu-item active"
            >

                <div class="menu-icon">
                    <i class="bi bi-grid-1x2-fill"></i>
                </div>

                <span>Dashboard</span>

            </a>


            <a
                href="user/tampil_user.php"
                class="menu-item"
            >

                <div class="menu-icon">
                    <i class="bi bi-people-fill"></i>
                </div>

                <span>Data User</span>

            </a>


            <a
                href="member/tampil_member.php"
                class="menu-item"
            >

                <div class="menu-icon">
                    <i class="bi bi-person-badge-fill"></i>
                </div>

                <span>Data Member</span>

            </a>


            <a
                href="area/tampil_area_parkir.php"
                class="menu-item"
            >

                <div class="menu-icon">
                    <i class="bi bi-p-square-fill"></i>
                </div>

                <span>Area Parkir</span>

            </a>


            <a
                href="tarif/tampil_tarif.php"
                class="menu-item"
            >

                <div class="menu-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <span>Data Tarif</span>

            </a>


            <a
                href="parkir/tampil_parkir.php"
                class="menu-item"
            >

                <div class="menu-icon">
                    <i class="bi bi-car-front-fill"></i>
                </div>

                <span>Data Parkir</span>

            </a>


            <a
                href="transaksi/tampil_transaksi.php"
                class="menu-item"
            >

                <div class="menu-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <span>Transaksi</span>

            </a>


            <a
                href="log_aktivitas/tampil_log.php"
                class="menu-item"
            >

                <div class="menu-icon">
                    <i class="bi bi-clock-history"></i>
                </div>

                <span>Log Aktivitas</span>

            </a>

        </nav>


        <!-- LOGOUT -->
        <div class="sidebar-bottom">

            <a
                href="logout.php"
                class="logout-button"
            >
                <i class="bi bi-box-arrow-right"></i>
                &nbsp;
                Logout
            </a>

        </div>

    </aside>


    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->
    <main class="main-content">


        <!-- TOPBAR -->
        <div class="topbar">

            <div>

                <div class="breadcrumb">
                    Dashboard / Admin
                </div>

                <div class="dashboard-title">
                    Dashboard Admin
                </div>

            </div>


            <!-- PROFILE -->
            <div class="profile-box">

                <div class="profile-avatar">
                    <?= strtoupper(substr($nama_user, 0, 1)); ?>
                </div>

                <div class="profile-info">

                    <strong>
                        <?= htmlspecialchars($nama_user); ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </div>


        <!-- =================================================
             WELCOME
             ================================================= -->
        <section class="welcome-card">

            <div class="welcome-label">
                PANEL ADMINISTRATOR
            </div>

            <h2>
                Selamat datang, <?= htmlspecialchars($nama_user); ?>
            </h2>

            <p>
                Kelola data pengguna, member, tarif, area parkir,
                kendaraan dan aktivitas sistem dari satu tempat.
            </p>

            <div class="welcome-decoration">
                <i class="bi bi-shield-check"></i>
            </div>

        </section>


        <!-- =================================================
             STATISTIK
             ================================================= -->
        <section class="section">

            <div class="section-heading">

                <div>
                    <h3>
                        Ringkasan Sistem
                    </h3>

                    <p>
                        Informasi data sistem parkir saat ini
                    </p>
                </div>

            </div>


            <div class="stat-grid">


                <!-- USER -->
                <div class="stat-card blue">

                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            Total User
                        </span>

                        <strong>
                            <?= number_format($total_user); ?>
                        </strong>

                        <small>
                            Pengguna sistem
                        </small>

                    </div>

                </div>


                <!-- MEMBER -->
                <div class="stat-card green">

                    <div class="stat-icon">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            Total Member
                        </span>

                        <strong>
                            <?= number_format($total_member); ?>
                        </strong>

                        <small>
                            Member parkir
                        </small>

                    </div>

                </div>


                <!-- AREA -->
                <div class="stat-card yellow">

                    <div class="stat-icon">
                        <i class="bi bi-p-square-fill"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            Area Parkir
                        </span>

                        <strong>
                            <?= number_format($total_area); ?>
                        </strong>

                        <small>
                            Area terdaftar
                        </small>

                    </div>

                </div>


                <!-- TARIF -->
                <div class="stat-card blue">

                    <div class="stat-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            Data Tarif
                        </span>

                        <strong>
                            <?= number_format($total_tarif); ?>
                        </strong>

                        <small>
                            Tarif kendaraan
                        </small>

                    </div>

                </div>


                <!-- PARKIR -->
                <div class="stat-card green">

                    <div class="stat-icon">
                        <i class="bi bi-car-front-fill"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            Parkir Aktif
                        </span>

                        <strong>
                            <?= number_format($total_parkir_aktif); ?>
                        </strong>

                        <small>
                            Kendaraan sedang parkir
                        </small>

                    </div>

                </div>


                <!-- TRANSAKSI -->
                <div class="stat-card yellow">

                    <div class="stat-icon">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            Transaksi
                        </span>

                        <strong>
                            <?= number_format($total_transaksi); ?>
                        </strong>

                        <small>
                            Transaksi selesai
                        </small>

                    </div>

                </div>


            </div>

        </section>


        <!-- =================================================
             MENU PENGELOLAAN
             ================================================= -->
        <section class="section">

            <div class="section-heading">

                <div>

                    <h3>
                        Menu Pengelolaan
                    </h3>

                    <p>
                        Akses cepat ke data sistem.
                    </p>

                </div>

            </div>


            <div class="quick-grid">


                <!-- USER -->
                <a
                    href="user/tampil_user.php"
                    class="quick-card"
                >

                    <div class="quick-icon blue-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <div>

                        <strong>
                            Data User
                        </strong>

                        <span>
                            Kelola pengguna sistem
                        </span>

                    </div>

                    <div class="arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>

                </a>


                <!-- MEMBER -->
                <a
                    href="member/tampil_member.php"
                    class="quick-card"
                >

                    <div class="quick-icon green-icon">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>

                    <div>

                        <strong>
                            Data Member
                        </strong>

                        <span>
                            Kelola member parkir
                        </span>

                    </div>

                    <div class="arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>

                </a>


                <!-- AREA -->
                <a
                    href="area/tampil_area_parkir.php"
                    class="quick-card"
                >

                    <div class="quick-icon yellow-icon">
                        <i class="bi bi-p-square-fill"></i>
                    </div>

                    <div>

                        <strong>
                            Area Parkir
                        </strong>

                        <span>
                            Kelola area parkir
                        </span>

                    </div>

                    <div class="arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>

                </a>


                <!-- TARIF -->
                <a
                    href="tarif/tampil_tarif.php"
                    class="quick-card"
                >

                    <div class="quick-icon blue-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div>

                        <strong>
                            Data Tarif
                        </strong>

                        <span>
                            Kelola tarif kendaraan
                        </span>

                    </div>

                    <div class="arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>

                </a>


                <!-- PARKIR -->
                <a
                    href="parkir/tampil_parkir.php"
                    class="quick-card"
                >

                    <div class="quick-icon green-icon">
                        <i class="bi bi-car-front-fill"></i>
                    </div>

                    <div>

                        <strong>
                            Data Parkir
                        </strong>

                        <span>
                            Pantau kendaraan masuk
                        </span>

                    </div>

                    <div class="arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>

                </a>


                <!-- LOG -->
                <a
                    href="log_aktivitas/tampil_log.php"
                    class="quick-card"
                >

                    <div class="quick-icon yellow-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div>

                        <strong>
                            Log Aktivitas
                        </strong>

                        <span>
                            Monitor aktivitas sistem
                        </span>

                    </div>

                    <div class="arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>

                </a>


            </div>

        </section>


        <!-- FOOTER -->
        <footer class="dashboard-footer">

            PARKIR Management System
            <strong>• Administrator</strong>

        </footer>


    </main>

</div>

</body>
</html>
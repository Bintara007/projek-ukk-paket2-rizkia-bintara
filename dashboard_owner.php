<?php
session_start();

include 'koneksi.php';

/*
|--------------------------------------------------------------------------
| CEK LOGIN & ROLE
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'owner') {
    header("Location: index.php");
    exit;
}

$nama_user = $_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Owner';

/*
|--------------------------------------------------------------------------
| FUNGSI DATA OWNER
|--------------------------------------------------------------------------
*/
function data_owner($koneksi, $query)
{
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        return [];
    }

    return mysqli_fetch_assoc($result) ?: [];
}

/*
|--------------------------------------------------------------------------
| STATISTIK OWNER
|--------------------------------------------------------------------------
*/
$data_transaksi = data_owner($koneksi, "SELECT COUNT(*) AS jumlah FROM tb_transaksi");
$total_transaksi = (int)($data_transaksi['jumlah'] ?? 0);

$data_parkir = data_owner($koneksi, "SELECT COUNT(*) AS jumlah FROM tb_parkir");
$total_parkir = (int)($data_parkir['jumlah'] ?? 0);

$data_member = data_owner($koneksi, "SELECT COUNT(*) AS jumlah FROM tb_member");
$total_member = (int)($data_member['jumlah'] ?? 0);

$data_pendapatan = data_owner($koneksi, "SELECT COALESCE(SUM(biaya_total), 0) AS total FROM tb_transaksi");
$total_pendapatan = (int)($data_pendapatan['total'] ?? 0);

$data_pendapatan_hari_ini = data_owner(
    $koneksi,
    "SELECT COALESCE(SUM(biaya_total), 0) AS total
     FROM tb_transaksi
     WHERE DATE(waktu_keluar) = CURDATE()"
);
$pendapatan_hari_ini = (int)($data_pendapatan_hari_ini['total'] ?? 0);

$data_transaksi_hari_ini = data_owner(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM tb_transaksi
     WHERE DATE(waktu_keluar) = CURDATE()"
);
$transaksi_hari_ini = (int)($data_transaksi_hari_ini['jumlah'] ?? 0);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - PARKIR</title>

    <!-- CSS DASHBOARD -->
    <link rel="stylesheet" href="assets/css/dashboard.css?v=6">

    <!-- BOOTSTRAP ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

    <div class="dashboard-wrapper">

        <!-- =====================================================
             SIDEBAR
             ===================================================== -->
        <aside class="sidebar">
            <!-- BRAND & TOMBOL CLOSE -->
            <div class="brand-header">
                <div class="brand" id="sidebarBrand" role="button" tabindex="0" aria-label="Buka sidebar" aria-expanded="false">
                    <div class="brand-icon">
                        <i class="bi bi-p-square-fill"></i>
                    </div>
                    <div>
                        <div class="brand-title">PARKIR</div>
                        <div class="brand-subtitle">Management System</div>
                    </div>
                </div>

                <button type="button"
                        class="sidebar-close"
                        id="sidebarClose"
                        aria-label="Tutup menu">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round">
                        <path d="m18 6-12 12M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- ROLE -->
            <div class="sidebar-role">
                <span class="role-dot"></span>
                Owner
            </div>

            <!-- MENU -->
            <nav class="sidebar-menu">
                <a href="dashboard_owner.php" class="menu-item active">
                    <div class="menu-icon"><i class="bi bi-grid-1x2-fill"></i></div>
                    <span>Dashboard</span>
                </a>

                <a href="transaksi/tampil_transaksi.php" class="menu-item">
                    <div class="menu-icon"><i class="bi bi-receipt"></i></div>
                    <span>Transaksi</span>
                </a>

                <a href="log_aktivitas/tampil_log.php" class="menu-item">
                    <div class="menu-icon"><i class="bi bi-clock-history"></i></div>
                    <span>Log Aktivitas</span>
                </a>
            </nav>

            <!-- LOGOUT -->
            <div class="sidebar-bottom">
                <a href="logout.php" class="logout-button">
                    <i class="bi bi-box-arrow-right"></i>
                    &nbsp; Logout
                </a>
            </div>
        </aside>

        <!-- OVERLAY SIDEBAR -->
        <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

        <!-- =====================================================
             MAIN CONTENT
             ===================================================== -->
        <main class="main-content">

            <!-- TOPBAR -->
            <div class="topbar">
                <div class="topbar-left">
                    <button type="button"
                            class="sidebar-toggle"
                            id="sidebarToggle"
                            aria-label="Buka menu"
                            aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round">
                            <path d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div>
                        <div class="breadcrumb">Dashboard / Owner</div>
                        <div class="dashboard-title">Dashboard Owner</div>
                    </div>
                </div>

                <!-- PROFILE -->
                <div class="profile-box">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($nama_user, 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <strong><?= htmlspecialchars($nama_user); ?></strong>
                        <span>Owner</span>
                    </div>
                </div>
            </div>

            <!-- WELCOME -->
            <section class="welcome-card">
                <div class="welcome-label">PANEL OWNER</div>
                <h2>Selamat datang, <?= htmlspecialchars($nama_user); ?></h2>
                <p>
                    Pantau transaksi, aktivitas dan informasi
                    operasional sistem parkir.
                </p>
                <div class="welcome-decoration">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
            </section>

            <!-- STATISTIK -->
            <section class="section">
                <div class="section-heading">
                    <div>
                        <h3>Ringkasan Bisnis</h3>
                        <p>Informasi transaksi dan pendapatan parkir.</p>
                    </div>
                </div>

                <div class="stat-grid">
                    <!-- PENDAPATAN -->
                    <div class="stat-card blue">
                        <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                        <div class="stat-content">
                            <span>Total Pendapatan</span>
                            <strong>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></strong>
                            <small>Seluruh transaksi</small>
                        </div>
                    </div>

                    <!-- HARI INI -->
                    <div class="stat-card yellow">
                        <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                        <div class="stat-content">
                            <span>Pendapatan Hari Ini</span>
                            <strong>Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.'); ?></strong>
                            <small>Pendapatan hari ini</small>
                        </div>
                    </div>

                    <!-- TRANSAKSI -->
                    <div class="stat-card green">
                        <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                        <div class="stat-content">
                            <span>Total Transaksi</span>
                            <strong><?= number_format($total_transaksi); ?></strong>
                            <small>Semua transaksi</small>
                        </div>
                    </div>

                    <!-- TRANSAKSI HARI INI -->
                    <div class="stat-card blue">
                        <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                        <div class="stat-content">
                            <span>Transaksi Hari Ini</span>
                            <strong><?= number_format($transaksi_hari_ini); ?></strong>
                            <small>Transaksi hari ini</small>
                        </div>
                    </div>

                    <!-- PARKIR -->
                    <div class="stat-card green">
                        <div class="stat-icon"><i class="bi bi-car-front-fill"></i></div>
                        <div class="stat-content">
                            <span>Data Parkir</span>
                            <strong><?= number_format($total_parkir); ?></strong>
                            <small>Data kendaraan</small>
                        </div>
                    </div>

                    <!-- MEMBER -->
                    <div class="stat-card yellow">
                        <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
                        <div class="stat-content">
                            <span>Total Member</span>
                            <strong><?= number_format($total_member); ?></strong>
                            <small>Member terdaftar</small>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="dashboard-footer">
                PARKIR Management System
                <strong>• Owner</strong>
            </footer>

        </main>
    </div>

    <!-- =====================================================
         JAVASCRIPT SIDEBAR
         ===================================================== -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle  = document.getElementById('sidebarToggle');
        const close   = document.getElementById('sidebarClose');
        const brand   = document.getElementById('sidebarBrand');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            document.body.classList.add('sidebar-open');
            toggle?.setAttribute('aria-expanded', 'true');
            brand?.setAttribute('aria-expanded', 'true');
            brand?.setAttribute('aria-label', 'Tutup sidebar');
            overlay?.setAttribute('aria-hidden', 'false');
        }

        function closeSidebar() {
            document.body.classList.remove('sidebar-open');
            toggle?.setAttribute('aria-expanded', 'false');
            brand?.setAttribute('aria-expanded', 'false');
            brand?.setAttribute('aria-label', 'Buka sidebar');
            overlay?.setAttribute('aria-hidden', 'true');
        }

        function toggleSidebar() {
            document.body.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
        }

        toggle?.addEventListener('click', openSidebar);
        close?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        brand?.addEventListener('click', toggleSidebar);
        brand?.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleSidebar();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        document.querySelectorAll('.sidebar-menu .menu-item').forEach(function (item) {
            item.addEventListener('click', closeSidebar);
        });
    });
    </script>
</body>
</html>
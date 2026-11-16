<?php
// ============================================================
// dashboard_petugas.php
// Dashboard khusus Petugas
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';

// ============================================================
// CEK LOGIN & ROLE
// ============================================================
if (!isset($_SESSION['id_user'], $_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header('Location: index.php');
    exit;
}

// ============================================================
// DATA USER LOGIN
// ============================================================
$nama_user = $_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Petugas';

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
// STATISTIK PETUGAS
// ============================================================
/* Kendaraan yang masih parkir (belum memiliki transaksi keluar) */
$total_parkir = jumlah_data_petugas(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM tb_parkir AS p
     LEFT JOIN tb_transaksi AS t
        ON p.id_parkir = t.id_parkir
     WHERE t.id_parkir IS NULL"
);

/* Total Transaksi */
$total_transaksi = jumlah_data_petugas(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM tb_transaksi"
);

/* Kendaraan Masuk Hari Ini */
$kendaraan_hari_ini = jumlah_data_petugas(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM tb_parkir
     WHERE DATE(waktu_masuk) = CURDATE()"
);

/* Transaksi Hari Ini */
$transaksi_hari_ini = jumlah_data_petugas(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM tb_transaksi
     WHERE DATE(waktu_keluar) = CURDATE()"
);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - PARKIR</title>

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
                Petugas
            </div>

            <!-- MENU -->
            <nav class="sidebar-menu">
                <a href="dashboard_petugas.php" class="menu-item active">
                    <div class="menu-icon"><i class="bi bi-grid-1x2-fill"></i></div>
                    <span>Dashboard</span>
                </a>

                <a href="parkir/tampil_parkir.php" class="menu-item">
                    <div class="menu-icon"><i class="bi bi-car-front-fill"></i></div>
                    <span>Data Parkir</span>
                </a>

                <a href="transaksi/tampil_transaksi.php" class="menu-item">
                    <div class="menu-icon"><i class="bi bi-receipt"></i></div>
                    <span>Transaksi</span>
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
                        <div class="breadcrumb">Dashboard / Petugas</div>
                        <div class="dashboard-title">Dashboard Petugas</div>
                    </div>
                </div>

                <!-- PROFILE -->
                <div class="profile-box">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($nama_user, 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <strong><?= htmlspecialchars($nama_user); ?></strong>
                        <span>Petugas</span>
                    </div>
                </div>
            </div>

            <!-- WELCOME -->
            <section class="welcome-card">
                <div class="welcome-label">PANEL PETUGAS</div>
                <h2>Selamat datang, <?= htmlspecialchars($nama_user); ?></h2>
                <p>
                    Kelola kendaraan masuk, kendaraan keluar,
                    dan transaksi parkir melalui dashboard ini.
                </p>
                <div class="welcome-decoration">
                    <i class="bi bi-car-front-fill"></i>
                </div>
            </section>

            <!-- STATISTIK -->
            <section class="section">
                <div class="section-heading">
                    <div>
                        <h3>Ringkasan Parkir</h3>
                        <p>Informasi aktivitas parkir saat ini</p>
                    </div>
                </div>

                <div class="stat-grid">
                    <!-- PARKIR AKTIF -->
                    <div class="stat-card blue">
                        <div class="stat-icon"><i class="bi bi-car-front-fill"></i></div>
                        <div class="stat-content">
                            <span>Parkir Aktif</span>
                            <strong><?= number_format($total_parkir, 0, ',', '.'); ?></strong>
                            <small>Kendaraan sedang parkir</small>
                        </div>
                    </div>

                    <!-- TOTAL TRANSAKSI -->
                    <div class="stat-card green">
                        <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                        <div class="stat-content">
                            <span>Total Transaksi</span>
                            <strong><?= number_format($total_transaksi, 0, ',', '.'); ?></strong>
                            <small>Seluruh transaksi</small>
                        </div>
                    </div>

                    <!-- KENDARAAN MASUK HARI INI -->
                    <div class="stat-card yellow">
                        <div class="stat-icon"><i class="bi bi-box-arrow-in-right"></i></div>
                        <div class="stat-content">
                            <span>Masuk Hari Ini</span>
                            <strong><?= number_format($kendaraan_hari_ini, 0, ',', '.'); ?></strong>
                            <small>Kendaraan masuk hari ini</small>
                        </div>
                    </div>

                    <!-- TRANSAKSI HARI INI -->
                    <div class="stat-card blue">
                        <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                        <div class="stat-content">
                            <span>Transaksi Hari Ini</span>
                            <strong><?= number_format($transaksi_hari_ini, 0, ',', '.'); ?></strong>
                            <small>Transaksi selesai hari ini</small>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="dashboard-footer">
                PARKIR Management System
                <strong>• Petugas</strong>
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
<?php // admin/laporan.php
require_once '../config/koneksi.php';
if(!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Filter tanggal
 $dari = isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01');
 $sampai = isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-d');

// Statistik
 $stmt_booking = $pdo->prepare("SELECT COUNT(*) as total, COALESCE(SUM(total_harga),0) as pendapatan FROM booking WHERE tanggal_main BETWEEN ? AND ? AND status IN ('Dikonfirmasi','Selesai')");
 $stmt_booking->execute([$dari, $sampai]);
 $stat_booking = $stmt_booking->fetch();

 $stmt_bayar = $pdo->prepare("SELECT COUNT(*) as total, COALESCE(SUM(jumlah_bayar),0) as terbayar FROM pembayaran p JOIN booking b ON p.id_booking=b.id_booking WHERE b.tanggal_main BETWEEN ? AND ? AND p.status_pembayaran='Dikonfirmasi'");
 $stmt_bayar->execute([$dari, $sampai]);
 $stat_bayar = $stmt_bayar->fetch();

// Detail per lapangan
 $stmt_lap = $pdo->prepare("SELECT l.nama_lapangan, COUNT(b.id_booking) as total_booking, COALESCE(SUM(b.total_harga),0) as pendapatan FROM lapangan l LEFT JOIN booking b ON l.id_lapangan=b.id_lapangan AND b.tanggal_main BETWEEN ? AND ? AND b.status IN ('Dikonfirmasi','Selesai') GROUP BY l.id_lapangan ORDER BY pendapatan DESC");
 $stmt_lap->execute([$dari, $sampai]);
 $lap_stats = $stmt_lap->fetchAll();

// Detail booking
 $stmt_detail = $pdo->prepare("SELECT b.*, l.nama_lapangan, u.nama as nama_user, p.status_pembayaran FROM booking b JOIN lapangan l ON b.id_lapangan=l.id_lapangan JOIN user u ON b.id_user=u.id_user LEFT JOIN pembayaran p ON b.id_booking=p.id_booking WHERE b.tanggal_main BETWEEN ? AND ? ORDER BY b.tanggal_main DESC");
 $stmt_detail->execute([$dari, $sampai]);
 $detail = $stmt_detail->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - FutsalZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-brand"><i class="fas fa-futbol"></i><h2>Futsal<span>Zone</span></h2></div>
        <div class="sidebar-label">Menu Utama</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="lapangan.php"><i class="fas fa-futbol"></i> Kelola Lapangan</a></li>
            <li><a href="jadwal.php"><i class="fas fa-calendar-alt"></i> Kelola Jadwal</a></li>
            <li><a href="booking.php"><i class="fas fa-book"></i> Kelola Booking</a></li>
            <li><a href="pembayaran.php"><i class="fas fa-credit-card"></i> Pembayaran</a></li>
        </ul>
        <div class="sidebar-label">Lainnya</div>
        <ul class="sidebar-menu">
            <li><a href="ulasan.php"><i class="fas fa-star"></i> Ulasan</a></li>
            <li><a href="laporan.php" class="active"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <button class="sidebar-toggle btn btn-sm btn-outline"><i class="fas fa-bars"></i></button>
                <h3>Laporan Booking & Pembayaran</h3>
            </div>
        </header>

        <div class="admin-content">
            <!-- Filter -->
            <div class="card mb-3" style="padding:20px">
                <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
                    </div>
                    <button type="submit" class="btn btn-accent"><i class="fas fa-filter"></i> Filter</button>
                </form>
            </div>

            <!-- Statistik Ringkasan -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-book"></i></div>
                    <div class="stat-info">
                        <h4><?= $stat_booking['total'] ?></h4>
                        <p>Total Booking</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h4><?= rupiah($stat_booking['pendapatan']) ?></h4>
                        <p>Total Pendapatan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h4><?= $stat_bayar['total'] ?></h4>
                        <p>Pembayaran Dikonfirmasi</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info">
                        <h4><?= rupiah($stat_bayar['terbayar']) ?></h4>
                        <p>Total Terbayar</p>
                    </div>
                </div>
            </div>

            <!-- Per Lapangan -->
            <div class="card mb-3" style="padding:24px">
                <h3 style="font-size:18px;color:var(--white);margin-bottom:16px"><i class="fas fa-futbol"></i> Pendapatan Per Lapangan</h3>
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Lapangan</th><th>Total Booking</th><th>Pendapatan</th></tr></thead>
                        <tbody>
                        <?php foreach($lap_stats as $ls): ?>
                            <tr>
                                <td style="color:var(--white);font-weight:600"><?= htmlspecialchars($ls['nama_lapangan']) ?></td>
                                <td><?= $ls['total_booking'] ?></td>
                                <td style="color:var(--accent);font-weight:600"><?= rupiah($ls['pendapatan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detail -->
            <div class="card" style="padding:24px">
                <h3 style="font-size:18px;color:var(--white);margin-bottom:16px"><i class="fas fa-list"></i> Detail Booking</h3>
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Kode</th><th>User</th><th>Lapangan</th><th>Tanggal</th><th>Total</th><th>Booking</th><th>Pembayaran</th></tr></thead>
                        <tbody>
                        <?php foreach($detail as $d): ?>
                            <tr>
                                <td style="font-weight:600;color:var(--white)">#BK<?= str_pad($d['id_booking'],4,'0',STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($d['nama_user']) ?></td>
                                <td><?= htmlspecialchars($d['nama_lapangan']) ?></td>
                                <td><?= date('d M Y', strtotime($d['tanggal_main'])) ?></td>
                                <td style="font-weight:600"><?= rupiah($d['total_harga']) ?></td>
                                <td><span class="status-dot status-<?= strtolower($d['status']) ?>"><?= $d['status'] ?></span></td>
                                <td><span class="status-dot status-<?= strtolower($d['status_pembayaran'] ?? 'menunggu') ?>"><?= $d['status_pembayaran'] ?? 'Menunggu' ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(!$detail): ?>
                            <tr><td colspan="7" class="text-center text-muted" style="padding:40px">Tidak ada data pada periode ini</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
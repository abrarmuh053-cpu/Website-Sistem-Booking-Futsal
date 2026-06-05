<?php // admin/dashboard.php
require_once '../config/koneksi.php';
if(!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

 $total_booking = $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
 $total_pendapatan = $pdo->query("SELECT COALESCE(SUM(jumlah_bayar),0) FROM pembayaran WHERE status_pembayaran='Dikonfirmasi'")->fetchColumn();
 $total_lapangan = $pdo->query("SELECT COUNT(*) FROM lapangan WHERE status='Aktif'")->fetchColumn();
 $total_user = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
 $booking_pending = $pdo->query("SELECT COUNT(*) FROM booking WHERE status='Pending'")->fetchColumn();
 $bayar_menunggu = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE status_pembayaran='Menunggu'")->fetchColumn();

 $recent = $pdo->query("SELECT b.*, l.nama_lapangan, u.nama as nama_user FROM booking b JOIN lapangan l ON b.id_lapangan=l.id_lapangan JOIN user u ON b.id_user=u.id_user ORDER BY b.tgl_booking DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FutsalZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-futbol"></i>
            <h2>Futsal<span>Zone</span></h2>
        </div>
        <div class="sidebar-label">Menu Utama</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="lapangan.php"><i class="fas fa-futbol"></i> Kelola Lapangan</a></li>
            <li><a href="jadwal.php"><i class="fas fa-calendar-alt"></i> Kelola Jadwal</a></li>
            <li><a href="booking.php"><i class="fas fa-book"></i> Kelola Booking</a></li>
            <li><a href="pembayaran.php"><i class="fas fa-credit-card"></i> Pembayaran <span class="notif-badge" style="position:static"><?= $bayar_menunggu ?></span></a></li>
        </ul>
        <div class="sidebar-label">Lainnya</div>
        <ul class="sidebar-menu">
            <li><a href="ulasan.php"><i class="fas fa-star"></i> Ulasan</a></li>
            <li><a href="laporan.php"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <button class="sidebar-toggle btn btn-sm btn-outline"><i class="fas fa-bars"></i></button>
                <h3>Dashboard</h3>
            </div>
            <div class="admin-topbar-right">
                <span class="text-muted" style="font-size:13px"><i class="fas fa-calendar"></i> <?= date('d F Y') ?></span>
                <span style="color:var(--white);font-weight:600;font-size:14px"><i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['admin_nama']) ?></span>
            </div>
        </header>

        <div class="admin-content">
            <?php showAlert(); ?>

            <!-- Stat Cards -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-book"></i></div>
                    <div class="stat-info">
                        <h4><?= $total_booking ?></h4>
                        <p>Total Booking</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h4><?= rupiah($total_pendapatan) ?></h4>
                        <p>Total Pendapatan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="fas fa-futbol"></i></div>
                    <div class="stat-info">
                        <h4><?= $total_lapangan ?></h4>
                        <p>Lapangan Aktif</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h4><?= $total_user ?></h4>
                        <p>Member Terdaftar</p>
                    </div>
                </div>
            </div>

            <!-- Alert Cards -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:32px">
                <?php if($booking_pending > 0): ?>
                <div class="card" style="padding:20px;border-left:4px solid var(--warning)">
                    <div style="display:flex;align-items:center;gap:12px">
                        <i class="fas fa-exclamation-triangle" style="font-size:24px;color:var(--warning)"></i>
                        <div>
                            <h4 style="color:var(--white);font-family:Poppins;font-size:16px"><?= $booking_pending ?> Booking Pending</h4>
                            <p style="color:var(--text-muted);font-size:13px">Menunggu pembayaran dari customer</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if($bayar_menunggu > 0): ?>
                <div class="card" style="padding:20px;border-left:4px solid var(--info)">
                    <div style="display:flex;align-items:center;gap:12px">
                        <i class="fas fa-receipt" style="font-size:24px;color:var(--info)"></i>
                        <div>
                            <h4 style="color:var(--white);font-family:Poppins;font-size:16px"><?= $bayar_menunggu ?> Pembayaran Menunggu</h4>
                            <p style="color:var(--text-muted);font-size:13px">Perlu verifikasi bukti transfer</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Recent Bookings -->
            <div class="card" style="padding:24px">
                <div class="flex-between mb-3">
                    <h3 style="font-size:18px;color:var(--white)"><i class="fas fa-clock"></i> Booking Terbaru</h3>
                    <a href="booking.php" class="btn btn-outline btn-sm">Lihat Semua</a>
                </div>
                <?php if($recent): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>User</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent as $r): ?>
                            <tr>
                                <td style="font-weight:600;color:var(--white)">#BK<?= str_pad($r['id_booking'],4,'0',STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($r['nama_user']) ?></td>
                                <td><?= htmlspecialchars($r['nama_lapangan']) ?></td>
                                <td><?= date('d M Y', strtotime($r['tanggal_main'])) ?><br><small class="text-muted"><?= date('H:i', strtotime($r['jam_mulai'])) ?>-<?= date('H:i', strtotime($r['jam_selesai'])) ?></small></td>
                                <td style="color:var(--accent);font-weight:600"><?= rupiah($r['total_harga']) ?></td>
                                <td><span class="status-dot status-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state"><p class="text-muted">Belum ada booking</p></div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
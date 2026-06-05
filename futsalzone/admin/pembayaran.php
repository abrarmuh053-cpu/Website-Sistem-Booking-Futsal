<?php // admin/pembayaran.php
require_once '../config/koneksi.php';
if(!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Konfirmasi / Tolak pembayaran
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if($action == 'konfirmasi') {
        $pdo->prepare("UPDATE pembayaran SET status_pembayaran='Dikonfirmasi' WHERE id_pembayaran=?")->execute([$id]);
        
        // Update booking status juga
        $bid = $pdo->prepare("SELECT id_booking FROM pembayaran WHERE id_pembayaran=?");
        $bid->execute([$id]);
        $booking_id = $bid->fetchColumn();
        if($booking_id) {
            $pdo->prepare("UPDATE booking SET status='Dikonfirmasi' WHERE id_booking=?")->execute([$booking_id]);
            
            // Update jadwal
            $jid = $pdo->prepare("SELECT id_jadwal FROM booking WHERE id_booking=?");
            $jid->execute([$booking_id]);
            $jadwal_id = $jid->fetchColumn();
            if($jadwal_id) {
                $pdo->prepare("UPDATE jadwal SET status='Dibooking' WHERE id_jadwal=?")->execute([$jadwal_id]);
            }

            // Notifikasi user
            $bk = $pdo->prepare("SELECT id_user, l.nama_lapangan FROM booking b JOIN lapangan l ON b.id_lapangan=l.id_lapangan WHERE b.id_booking=?");
            $bk->execute([$booking_id]);
            $booking = $bk->fetch();
            if($booking) {
                $pdo->prepare("INSERT INTO notifikasi (id_user, judul, pesan) VALUES (?, ?, ?)")
                    ->execute([$booking['id_user'], 'Pembayaran Dikonfirmasi', "Pembayaran untuk lapangan {$booking['nama_lapangan']} telah dikonfirmasi. Booking Anda aktif!"]);
            }
        }
        alert('Pembayaran berhasil dikonfirmasi!', 'success');

    } elseif($action == 'tolak') {
        $pdo->prepare("UPDATE pembayaran SET status_pembayaran='Ditolak' WHERE id_pembayaran=?")->execute([$id]);
        
        $bid = $pdo->prepare("SELECT id_booking FROM pembayaran WHERE id_pembayaran=?");
        $bid->execute([$id]);
        $booking_id = $bid->fetchColumn();
        if($booking_id) {
            $pdo->prepare("UPDATE booking SET status='Dibatalkan' WHERE id_booking=?")->execute([$booking_id]);
            
            $jid = $pdo->prepare("SELECT id_jadwal FROM booking WHERE id_booking=?");
            $jid->execute([$booking_id]);
            $jadwal_id = $jid->fetchColumn();
            if($jadwal_id) {
                $pdo->prepare("UPDATE jadwal SET status='Tersedia' WHERE id_jadwal=?")->execute([$jadwal_id]);
            }

            $bk = $pdo->prepare("SELECT id_user FROM booking WHERE id_booking=?");
            $bk->execute([$booking_id]);
            $uid = $bk->fetchColumn();
            if($uid) {
                $pdo->prepare("INSERT INTO notifikasi (id_user, judul, pesan) VALUES (?, ?, ?)")
                    ->execute([$uid, 'Pembayaran Ditolak', 'Bukti pembayaran Anda ditolak. Silakan upload ulang atau hubungi admin.']);
            }
        }
        alert('Pembayaran ditolak.', 'warning');
    }
    header('Location: pembayaran.php');
    exit;
}

 $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
if($filter) {
    $stmt = $pdo->prepare("SELECT p.*, b.total_harga, b.tanggal_main, b.jam_mulai, b.jam_selesai, b.status as booking_status, l.nama_lapangan, u.nama as nama_user FROM pembayaran p JOIN booking b ON p.id_booking=b.id_booking JOIN lapangan l ON b.id_lapangan=l.id_lapangan JOIN user u ON b.id_user=u.id_user WHERE p.status_pembayaran=? ORDER BY p.tgl_bayar DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query("SELECT p.*, b.total_harga, b.tanggal_main, b.jam_mulai, b.jam_selesai, b.status as booking_status, l.nama_lapangan, u.nama as nama_user FROM pembayaran p JOIN booking b ON p.id_booking=b.id_booking JOIN lapangan l ON b.id_lapangan=l.id_lapangan JOIN user u ON b.id_user=u.id_user ORDER BY p.tgl_bayar DESC");
}
 $payments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - FutsalZone</title>
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
            <li><a href="pembayaran.php" class="active"><i class="fas fa-credit-card"></i> Pembayaran</a></li>
        </ul>
        <div class="sidebar-label">Lainnya</div>
        <ul class="sidebar-menu">
            <li><a href="ulasan.php"><i class="fas fa-star"></i> Ulasan</a></li>
            <li><a href="laporan.php"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <button class="sidebar-toggle btn btn-sm btn-outline"><i class="fas fa-bars"></i></button>
                <h3>Konfirmasi Pembayaran</h3>
            </div>
            <select class="form-control" style="width:auto" onchange="window.location='pembayaran.php?filter='+this.value">
                <option value="">Semua Status</option>
                <option value="Menunggu" <?= $filter=='Menunggu'?'selected':'' ?>>Menunggu</option>
                <option value="Dikonfirmasi" <?= $filter=='Dikonfirmasi'?'selected':'' ?>>Dikonfirmasi</option>
                <option value="Ditolak" <?= $filter=='Ditolak'?'selected':'' ?>>Ditolak</option>
            </select>
        </header>

        <div class="admin-content">
            <?php showAlert(); ?>
            <div class="card" style="padding:24px">
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>#</th><th>User</th><th>Lapangan</th><th>Metode</th><th>Jumlah</th><th>Bukti</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php foreach($payments as $i => $p): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td style="color:var(--white);font-weight:600"><?= htmlspecialchars($p['nama_user']) ?></td>
                                <td>
                                    <?= htmlspecialchars($p['nama_lapangan']) ?><br>
                                    <small class="text-muted"><?= date('d M Y', strtotime($p['tanggal_main'])) ?> <?= date('H:i', strtotime($p['jam_mulai'])) ?>-<?= date('H:i', strtotime($p['jam_selesai'])) ?></small>
                                </td>
                                <td><?= $p['metode_pembayaran'] ?></td>
                                <td style="color:var(--accent);font-weight:600"><?= rupiah($p['jumlah_bayar']) ?></td>
                                <td>
                                    <?php if($p['bukti_pembayaran']): ?>
                                    <img src="../assets/upload/pembayaran/<?= $p['bukti_pembayaran'] ?>" class="payment-proof-img" onclick="window.open('../assets/upload/pembayaran/<?= $p['bukti_pembayaran'] ?>','_blank')" alt="Bukti">
                                    <?php else: ?>
                                    <span class="text-muted">Belum upload</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-dot status-<?= strtolower($p['status_pembayaran']) ?>"><?= $p['status_pembayaran'] ?></span></td>
                                <td>
                                    <?php if($p['status_pembayaran'] == 'Menunggu' && $p['bukti_pembayaran']): ?>
                                    <a href="pembayaran.php?action=konfirmasi&id=<?= $p['id_pembayaran'] ?>" class="btn btn-sm" style="background:var(--success);color:white" onclick="return confirm('Konfirmasi pembayaran ini?')"><i class="fas fa-check"></i></a>
                                    <a href="pembayaran.php?action=tolak&id=<?= $p['id_pembayaran'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak pembayaran ini?')"><i class="fas fa-times"></i></a>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(!$payments): ?>
                            <tr><td colspan="8" class="text-center text-muted" style="padding:40px">Tidak ada data pembayaran</td></tr>
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
<?php // admin/booking.php
require_once '../config/koneksi.php';
if(!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Update status booking
if(isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    
    if(in_array($status, ['Dikonfirmasi','Dibatalkan','Selesai'])) {
        $pdo->prepare("UPDATE booking SET status=? WHERE id_booking=?")->execute([$status, $id]);
        
        // Update jadwal status
        if($status == 'Dibatalkan') {
            $jadwal_id = $pdo->prepare("SELECT id_jadwal FROM booking WHERE id_booking=?");
            $jadwal_id->execute([$id]);
            $jid = $jadwal_id->fetchColumn();
            if($jid) {
                $pdo->prepare("UPDATE jadwal SET status='Tersedia' WHERE id_jadwal=?")->execute([$jid]);
            }
        } elseif($status == 'Dikonfirmasi') {
            $jadwal_id = $pdo->prepare("SELECT id_jadwal FROM booking WHERE id_booking=?");
            $jadwal_id->execute([$id]);
            $jid = $jadwal_id->fetchColumn();
            if($jid) {
                $pdo->prepare("UPDATE jadwal SET status='Dibooking' WHERE id_jadwal=?")->execute([$jid]);
            }
            
            // Notifikasi ke user
            $b = $pdo->prepare("SELECT id_user, l.nama_lapangan FROM booking b JOIN lapangan l ON b.id_lapangan=l.id_lapangan WHERE b.id_booking=?");
            $b->execute([$id]);
            $bk = $b->fetch();
            if($bk) {
                $pdo->prepare("INSERT INTO notifikasi (id_user, judul, pesan) VALUES (?, ?, ?)")
                    ->execute([$bk['id_user'], 'Booking Dikonfirmasi', "Booking lapangan {$bk['nama_lapangan']} Anda telah dikonfirmasi admin."]);
            }
        } elseif($status == 'Selesai') {
            $jadwal_id = $pdo->prepare("SELECT id_jadwal FROM booking WHERE id_booking=?");
            $jadwal_id->execute([$id]);
            $jid = $jadwal_id->fetchColumn();
            if($jid) {
                $pdo->prepare("UPDATE jadwal SET status='Tersedia' WHERE id_jadwal=?")->execute([$jid]);
            }
        }
        
        alert("Status booking berhasil diubah menjadi $status!", 'success');
    }
    header('Location: booking.php');
    exit;
}

 $bookings = $pdo->query("SELECT b.*, l.nama_lapangan, u.nama as nama_user, u.email, u.no_hp FROM booking b JOIN lapangan l ON b.id_lapangan=l.id_lapangan JOIN user u ON b.id_user=u.id_user ORDER BY b.tgl_booking DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Booking - FutsalZone</title>
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
            <li><a href="booking.php" class="active"><i class="fas fa-book"></i> Kelola Booking</a></li>
            <li><a href="pembayaran.php"><i class="fas fa-credit-card"></i> Pembayaran</a></li>
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
                <h3>Kelola Booking</h3>
            </div>
            <span class="text-muted"><?= count($bookings) ?> data booking</span>
        </header>

        <div class="admin-content">
            <?php showAlert(); ?>
            <div class="card" style="padding:24px">
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Kode</th><th>User</th><th>Lapangan</th><th>Tanggal Main</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php foreach($bookings as $b): ?>
                            <tr>
                                <td style="font-weight:600;color:var(--white)">#BK<?= str_pad($b['id_booking'],4,'0',STR_PAD_LEFT) ?></td>
                                <td>
                                    <strong style="color:var(--white)"><?= htmlspecialchars($b['nama_user']) ?></strong><br>
                                    <small class="text-muted"><?= $b['email'] ?></small>
                                </td>
                                <td><?= htmlspecialchars($b['nama_lapangan']) ?></td>
                                <td>
                                    <?= date('d M Y', strtotime($b['tanggal_main'])) ?><br>
                                    <small class="text-muted"><?= date('H:i', strtotime($b['jam_mulai'])) ?> - <?= date('H:i', strtotime($b['jam_selesai'])) ?></small>
                                </td>
                                <td style="color:var(--accent);font-weight:600"><?= rupiah($b['total_harga']) ?></td>
                                <td><span class="status-dot status-<?= strtolower($b['status']) ?>"><?= $b['status'] ?></span></td>
                                <td>
                                    <?php if($b['status'] == 'Pending'): ?>
                                    <a href="booking.php?status=Dikonfirmasi&id=<?= $b['id_booking'] ?>" class="btn btn-sm" style="background:var(--success);color:white" onclick="return confirm('Konfirmasi booking?')"><i class="fas fa-check"></i></a>
                                    <a href="booking.php?status=Dibatalkan&id=<?= $b['id_booking'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan booking?')"><i class="fas fa-times"></i></a>
                                    <?php elseif($b['status'] == 'Dikonfirmasi'): ?>
                                    <a href="booking.php?status=Selesai&id=<?= $b['id_booking'] ?>" class="btn btn-sm" style="background:var(--info);color:white" onclick="return confirm('Tandai selesai?')"><i class="fas fa-flag-checkered"></i></a>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
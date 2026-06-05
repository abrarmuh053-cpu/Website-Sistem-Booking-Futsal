<?php // admin/ulasan.php
require_once '../config/koneksi.php';
if(!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Hapus ulasan
if(isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM ulasan WHERE id_ulasan=?")->execute([$id]);
    alert('Ulasan berhasil dihapus!', 'success');
    header('Location: ulasan.php');
    exit;
}

 $ulasan = $pdo->query("SELECT u.*, l.nama_lapangan, us.nama as nama_user, us.email FROM ulasan u JOIN lapangan l ON u.id_lapangan=l.id_lapangan JOIN user us ON u.id_user=us.id_user ORDER BY u.tgl_ulasan DESC")->fetchAll();
 $avg_rating = $pdo->query("SELECT AVG(rating) as avg_r, COUNT(*) as total FROM ulasan")->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ulasan - FutsalZone</title>
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
            <li><a href="ulasan.php" class="active"><i class="fas fa-star"></i> Ulasan</a></li>
            <li><a href="laporan.php"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <button class="sidebar-toggle btn btn-sm btn-outline"><i class="fas fa-bars"></i></button>
                <h3>Kelola Ulasan</h3>
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="rating-display">
                    <i class="fas fa-star" style="color:#fbbf24"></i>
                    <span style="font-size:20px;color:var(--accent);font-weight:700"><?= number_format($avg_rating['avg_r'], 1) ?></span>
                </div>
                <span class="text-muted"><?= $avg_rating['total'] ?> ulasan</span>
            </div>
        </header>

        <div class="admin-content">
            <?php showAlert(); ?>

            <?php foreach($ulasan as $u): ?>
            <div class="card mb-2" style="padding:20px">
                <div class="flex-between">
                    <div style="display:flex;gap:12px;align-items:center">
                        <div class="review-avatar"><?= strtoupper(substr($u['nama_user'],0,1)) ?></div>
                        <div>
                            <h4 style="color:var(--white);font-size:15px;font-family:Poppins;font-weight:600"><?= htmlspecialchars($u['nama_user']) ?></h4>
                            <small class="text-muted"><?= htmlspecialchars($u['nama_lapangan']) ?> - <?= date('d M Y', strtotime($u['tgl_ulasan'])) ?></small>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px">
                        <div class="rating-display">
                            <?php for($i=1;$i<=5;$i++): ?>
                            <i class="fas fa-star" style="color:<?= $i <= $u['rating'] ? '#fbbf24' : '#3a3a4a' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <a href="ulasan.php?hapus=<?= $u['id_ulasan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus ulasan ini?')"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
                <p style="color:var(--text);font-size:14px;margin-top:12px"><?= htmlspecialchars($u['komentar']) ?></p>
            </div>
            <?php endforeach; ?>

            <?php if(!$ulasan): ?>
            <div class="empty-state">
                <i class="fas fa-comment-slash"></i>
                <h3>Belum ada ulasan</h3>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
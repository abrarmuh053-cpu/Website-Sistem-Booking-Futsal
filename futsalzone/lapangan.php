<?php // lapangan.php
require_once 'config/koneksi.php';

 $filter = isset($_GET['tipe']) ? $_GET['tipe'] : '';
if($filter) {
    $stmt = $pdo->prepare("SELECT * FROM lapangan WHERE status='Aktif' AND tipe=? ORDER BY nama_lapangan");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query("SELECT * FROM lapangan WHERE status='Aktif' ORDER BY nama_lapangan");
}
 $lapangan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangan - FutsalZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="breadcrumb"><a href="index.php">Beranda</a> <i class="fas fa-chevron-right"></i> Lapangan</div>
    <h1>Daftar Lapangan Futsal</h1>
    <p>Pilih lapangan yang sesuai dengan kebutuhanmu</p>
</div>

<section class="section">
    <div class="container">
        <div class="flex-between mb-3">
            <div>
                <a href="lapangan.php" class="btn btn-sm <?= !$filter ? 'btn-accent' : 'btn-outline' ?>">Semua</a>
                <a href="lapangan.php?tipe=Indoor" class="btn btn-sm <?= $filter=='Indoor' ? 'btn-accent' : 'btn-outline' ?>">Indoor</a>
                <a href="lapangan.php?tipe=Outdoor" class="btn btn-sm <?= $filter=='Outdoor' ? 'btn-accent' : 'btn-outline' ?>">Outdoor</a>
            </div>
            <span class="text-muted"><?= count($lapangan) ?> lapangan ditemukan</span>
        </div>

        <?php if($lapangan): ?>
        <div class="card-grid">
            <?php foreach($lapangan as $l):
                $avg = $pdo->prepare("SELECT AVG(rating) as avg_r, COUNT(*) as total FROM ulasan WHERE id_lapangan=?");
                $avg->execute([$l['id_lapangan']]);
                $r = $avg->fetch();
            ?>
            <div class="card">
                <div class="card-img-wrapper">
                    <img class="card-img" src="<?= $l['gambar'] != 'default-lapangan.jpg' ? 'assets/upload/lapangan/'.$l['gambar'] : 'https://picsum.photos/seed/lap-'.$l['id_lapangan'].'/600/400.jpg' ?>" alt="<?= htmlspecialchars($l['nama_lapangan']) ?>">
                    <span class="card-badge badge-<?= strtolower($l['tipe']) ?>"><?= $l['tipe'] ?></span>
                </div>
                <div class="card-body">
                    <h3><?= htmlspecialchars($l['nama_lapangan']) ?></h3>
                    <p><?= htmlspecialchars($l['deskripsi']) ?></p>
                    <div class="rating-display mb-2">
                        <?php for($i=1;$i<=5;$i++): ?>
                        <i class="fas fa-star" style="color:<?= $i <= round($r['avg_r']?:0) ? '#fbbf24' : '#3a3a4a' ?>"></i>
                        <?php endfor; ?>
                        <span>(<?= $r['total'] ?> ulasan)</span>
                    </div>
                    <div class="card-price"><?= rupiah($l['harga_per_jam']) ?> <span>/ jam</span></div>
                </div>
                <div class="card-footer">
                    <a href="jadwal.php?id=<?= $l['id_lapangan'] ?>" class="btn btn-outline btn-sm"><i class="fas fa-clock"></i> Jadwal</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="booking.php?id=<?= $l['id_lapangan'] ?>" class="btn btn-accent btn-sm"><i class="fas fa-book"></i> Booking</a>
                    <?php else: ?>
                    <a href="login.php" class="btn btn-accent btn-sm"><i class="fas fa-book"></i> Booking</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-futbol"></i>
            <h3>Belum ada lapangan</h3>
            <p>Lapangan akan segera tersedia</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
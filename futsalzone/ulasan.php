<?php // ulasan.php
require_once 'config/koneksi.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Submit ulasan
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_booking = (int)$_POST['id_booking'];
    $id_lapangan = (int)$_POST['id_lapangan'];
    $rating = (int)$_POST['rating'];
    $komentar = trim($_POST['komentar']);

    if($rating < 1 || $rating > 5) {
        alert('Rating harus 1-5!', 'danger');
    } else {
        $cek = $pdo->prepare("SELECT id_ulasan FROM ulasan WHERE id_booking=? AND id_user=?");
        $cek->execute([$id_booking, $_SESSION['user_id']]);
        if($cek->fetch()) {
            alert('Anda sudah memberikan ulasan untuk booking ini!', 'warning');
        } else {
            $ins = $pdo->prepare("INSERT INTO ulasan (id_user, id_lapangan, id_booking, rating, komentar) VALUES (?, ?, ?, ?, ?)");
            $ins->execute([$_SESSION['user_id'], $id_lapangan, $id_booking, $rating, $komentar]);
            alert('Ulasan berhasil dikirim! Terima kasih.', 'success');
        }
    }
    header('Location: ulasan.php');
    exit;
}

// Ambil ulasan user
 $ulasan_saya = $pdo->prepare("SELECT u.*, l.nama_lapangan, b.tanggal_main FROM ulasan u JOIN lapangan l ON u.id_lapangan=l.id_lapangan JOIN booking b ON u.id_booking=b.id_booking WHERE u.id_user=? ORDER BY u.tgl_ulasan DESC");
 $ulasan_saya->execute([$_SESSION['user_id']]);
 $my_reviews = $ulasan_saya->fetchAll();

// Semua ulasan terbaru
 $all_reviews = $pdo->query("SELECT u.*, l.nama_lapangan, us.nama FROM ulasan u JOIN lapangan l ON u.id_lapangan=l.id_lapangan JOIN user us ON u.id_user=us.id_user ORDER BY u.tgl_ulasan DESC LIMIT 10")->fetchAll();

// Booking yang selesai dan belum diulas
 $booking_ulasan = null;
if(isset($_GET['booking']) && isset($_GET['lapangan'])) {
    $bid = (int)$_GET['booking'];
    $lid = (int)$_GET['lapangan'];
    $cek = $pdo->prepare("SELECT b.*, l.nama_lapangan FROM booking b JOIN lapangan l ON b.id_lapangan=l.id_lapangan WHERE b.id_booking=? AND b.id_user=? AND b.status='Selesai'");
    $cek->execute([$bid, $_SESSION['user_id']]);
    $booking_ulasan = $cek->fetch();
    
    // Cek sudah ulasan belum
    if($booking_ulasan) {
        $cek_ulasan = $pdo->prepare("SELECT id_ulasan FROM ulasan WHERE id_booking=? AND id_user=?");
        $cek_ulasan->execute([$bid, $_SESSION['user_id']]);
        if($cek_ulasan->fetch()) {
            $booking_ulasan = null;
            alert('Anda sudah memberikan ulasan untuk booking ini!', 'warning');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan - FutsalZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="breadcrumb"><a href="index.php">Beranda</a> <i class="fas fa-chevron-right"></i> Ulasan</div>
    <h1>Ulasan Lapangan</h1>
    <p>Berikan ulasan untuk pengalaman bermain Anda</p>
</div>

<section class="section">
    <div class="container" style="max-width:800px">
        <?php showAlert(); ?>

        <!-- Form Ulasan -->
        <?php if($booking_ulasan): ?>
        <div class="card mb-4" style="padding:28px">
            <h3 style="font-size:20px;color:var(--white);margin-bottom:4px"><i class="fas fa-pen"></i> Tulis Ulasan</h3>
            <p class="text-muted mb-3"><?= htmlspecialchars($booking_ulasan['nama_lapangan']) ?> - <?= date('d F Y', strtotime($booking_ulasan['tanggal_main'])) ?></p>
            <form method="POST" data-validate>
                <input type="hidden" name="id_booking" value="<?= $booking_ulasan['id_booking'] ?>">
                <input type="hidden" name="id_lapangan" value="<?= $booking_ulasan['id_lapangan'] ?>">
                <div class="form-group">
                    <label>Rating</label>
                    <div class="rating-input" style="display:flex;gap:8px;align-items:center">
                        <?php for($i=1;$i<=5;$i++): ?>
                        <i class="fas fa-star" style="font-size:28px;cursor:pointer;color:#3a3a4a" data-val="<?= $i ?>"></i>
                        <?php endfor; ?>
                        <input type="hidden" name="rating" id="ratingInput" value="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Komentar</label>
                    <textarea name="komentar" class="form-control" rows="4" placeholder="Bagikan pengalaman Anda bermain di lapangan ini..." required></textarea>
                </div>
                <button type="submit" class="btn btn-accent"><i class="fas fa-paper-plane"></i> Kirim Ulasan</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Ulasan Saya -->
        <?php if($my_reviews): ?>
        <h3 style="font-size:22px;color:var(--white);margin-bottom:16px"><i class="fas fa-user"></i> Ulasan Saya</h3>
        <?php foreach($my_reviews as $u): ?>
        <div class="review-card">
            <div class="review-header">
                <div class="review-user">
                    <div class="review-avatar"><?= strtoupper(substr($_SESSION['user_nama'],0,1)) ?></div>
                    <div>
                        <h4><?= htmlspecialchars($_SESSION['user_nama']) ?></h4>
                        <small><?= htmlspecialchars($u['nama_lapangan']) ?> - <?= date('d M Y', strtotime($u['tanggal_main'])) ?></small>
                    </div>
                </div>
                <div class="rating-display">
                    <?php for($i=1;$i<=5;$i++): ?>
                    <i class="fas fa-star" style="color:<?= $i <= $u['rating'] ? '#fbbf24' : '#3a3a4a' ?>"></i>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="review-body">
                <p><?= htmlspecialchars($u['komentar']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
        <hr style="border-color:rgba(255,255,255,0.08);margin:32px 0">
        <?php endif; ?>

        <!-- Semua Ulasan -->
        <h3 style="font-size:22px;color:var(--white);margin-bottom:16px"><i class="fas fa-globe"></i> Semua Ulasan</h3>
        <?php if($all_reviews): ?>
        <?php foreach($all_reviews as $a): ?>
        <div class="review-card">
            <div class="review-header">
                <div class="review-user">
                    <div class="review-avatar"><?= strtoupper(substr($a['nama'],0,1)) ?></div>
                    <div>
                        <h4><?= htmlspecialchars($a['nama']) ?></h4>
                        <small><?= htmlspecialchars($a['nama_lapangan']) ?> - <?= date('d M Y', strtotime($a['tgl_ulasan'])) ?></small>
                    </div>
                </div>
                <div class="rating-display">
                    <?php for($i=1;$i<=5;$i++): ?>
                    <i class="fas fa-star" style="color:<?= $i <= $a['rating'] ? '#fbbf24' : '#3a3a4a' ?>"></i>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="review-body">
                <p><?= htmlspecialchars($a['komentar']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-comment-slash"></i>
            <h3>Belum ada ulasan</h3>
            <p>Jadilah yang pertama memberikan ulasan!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
<script>
// Rating stars interaction
document.querySelectorAll('.rating-input i').forEach(star => {
    star.addEventListener('click', function() {
        const val = parseInt(this.dataset.val);
        document.getElementById('ratingInput').value = val;
        document.querySelectorAll('.rating-input i').forEach((s, i) => {
            s.style.color = i < val ? '#fbbf24' : '#3a3a4a';
        });
    });
    star.addEventListener('mouseenter', function() {
        const val = parseInt(this.dataset.val);
        document.querySelectorAll('.rating-input i').forEach((s, i) => {
            s.style.color = i <= val ? '#fbbf24' : '#3a3a4a';
        });
    });
});
document.querySelector('.rating-input')?.addEventListener('mouseleave', function() {
    const val = parseInt(document.getElementById('ratingInput').value) || 0;
    document.querySelectorAll('.rating-input i').forEach((s, i) => {
        s.style.color = i < val ? '#fbbf24' : '#3a3a4a';
    });
});
</script>
</body>
</html>
<?php // index.php
require_once 'config/koneksi.php';

 $lapangan = $pdo->query("SELECT * FROM lapangan WHERE status='Aktif' LIMIT 4")->fetchAll();
 $ulasan = $pdo->query("SELECT u.*, l.nama_lapangan, us.nama FROM ulasan u JOIN lapangan l ON u.id_lapangan=l.id_lapangan JOIN user us ON u.id_user=us.id_user ORDER BY u.tgl_ulasan DESC LIMIT 3")->fetchAll();
 $total_booking = $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
 $total_lapangan = $pdo->query("SELECT COUNT(*) FROM lapangan WHERE status='Aktif'")->fetchColumn();
 $total_user = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FutsalZone - Booking Lapangan Futsal Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <a href="index.php" class="nav-brand">
        <i class="fas fa-futbol"></i>
        Futsal<span>Zone</span>
    </a>
    <ul class="nav-menu" id="navMenu">
        <li><a href="index.php" class="active">Beranda</a></li>
        <li><a href="lapangan.php">Lapangan</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="contact.php">Kontak</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li><a href="ulasan.php">Ulasan</a></li>
        <?php endif; ?>
    </ul>
    <div class="nav-user">
        <?php if(isset($_SESSION['user_id'])): ?>
            <div style="position:relative">
                <button class="notif-btn">
                    <i class="fas fa-bell"></i>
                    <?php
                    $notif_count = $pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE id_user=? AND dibaca=0");
                    $notif_count->execute([$_SESSION['user_id']]);
                    $nc = $notif_count->fetchColumn();
                    if($nc > 0): ?>
                    <span class="notif-badge"><?= $nc ?></span>
                    <?php endif; ?>
                </button>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">Notifikasi</div>
                    <?php
                    $notifs = $pdo->prepare("SELECT * FROM notifikasi WHERE id_user=? ORDER BY tgl_notifikasi DESC LIMIT 5");
                    $notifs->execute([$_SESSION['user_id']]);
                    $notifs_data = $notifs->fetchAll();
                    if($notifs_data):
                        foreach($notifs_data as $n): ?>
                    <div class="notif-item <?= $n['dibaca'] ? '' : 'unread' ?>">
                        <h4><?= htmlspecialchars($n['judul']) ?></h4>
                        <p><?= htmlspecialchars($n['pesan']) ?></p>
                        <small><?= date('d M Y, H:i', strtotime($n['tgl_notifikasi'])) ?></small>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px">Tidak ada notifikasi</div>
                    <?php endif; ?>
                </div>
            </div>
            <a href="riwayat.php" class="btn btn-outline btn-sm"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_nama']) ?></a>
            <a href="logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i></a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline btn-sm">Masuk</a>
            <a href="register.php" class="btn btn-accent btn-sm">Daftar</a>
        <?php endif; ?>
        <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-particles"></div>
    <div class="hero-content">
        <div class="hero-badge"><i class="fas fa-bolt"></i> Platform Booking #1</div>
        <h1>Booking Lapangan <span>Futsal</span> Terpercaya</h1>
        <p>Pesan lapangan futsal favoritmu dengan mudah, cepat, dan aman. Nikmati pengalaman bermain tanpa ribet.</p>
        <div class="hero-actions">
            <a href="lapangan.php" class="btn btn-accent btn-lg"><i class="fas fa-calendar-check"></i> Booking Sekarang</a>
            <a href="jadwal.php" class="btn btn-outline btn-lg"><i class="fas fa-clock"></i> Lihat Jadwal</a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <h3 data-count="<?= $total_booking ?>">0</h3>
                <p>Total Booking</p>
            </div>
            <div class="hero-stat">
                <h3 data-count="<?= $total_lapangan ?>">0</h3>
                <p>Lapangan Tersedia</p>
            </div>
            <div class="hero-stat">
                <h3 data-count="<?= $total_user ?>">0</h3>
                <p>Member Aktif</p>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section" style="background:var(--dark-2)">
    <div class="container">
        <div class="section-header">
            <h2>Kenapa Pilih Kami</h2>
            <p>Fasilitas dan layanan terbaik untuk permainan futsal Anda</p>
            <div class="line"></div>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-bolt"></i>
                <h3>Booking Instan</h3>
                <p>Pesan lapangan dalam hitungan detik tanpa perlu datang langsung ke lokasi.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Pembayaran Aman</h3>
                <p>Sistem pembayaran terverifikasi dengan konfirmasi otomatis dan transparan.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Jadwal Real-time</h3>
                <p>Lihat ketersediaan lapangan secara langsung tanpa perlu menunggu konfirmasi.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-star"></i>
                <h3>Ulasan Terpercaya</h3>
                <p>Baca pengalaman nyata dari pemain lain sebelum memilih lapangan.</p>
            </div>
        </div>
    </div>
</section>

<!-- LAPANGAN -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Lapangan Unggulan</h2>
            <p>Pilih lapangan terbaik untuk permainan Anda</p>
            <div class="line"></div>
        </div>
        <div class="card-grid">
            <?php foreach($lapangan as $l):
                $avg = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM ulasan WHERE id_lapangan=?");
                $avg->execute([$l['id_lapangan']]);
                $rating = $avg->fetch();
            ?>
            <div class="card">
                <div class="card-img-wrapper">
                    <img class="card-img" src="<?= $l['gambar'] != 'default-lapangan.jpg' ? 'assets/upload/lapangan/'.$l['gambar'] : 'https://picsum.photos/seed/lapangan-'.$l['id_lapangan'].'/600/400.jpg' ?>" alt="<?= htmlspecialchars($l['nama_lapangan']) ?>">
                    <span class="card-badge badge-<?= strtolower($l['tipe']) ?>"><?= $l['tipe'] ?></span>
                </div>
                <div class="card-body">
                    <h3><?= htmlspecialchars($l['nama_lapangan']) ?></h3>
                    <p><?= htmlspecialchars($l['deskripsi']) ?></p>
                    <div class="rating-display mb-2">
                        <?php for($i=1;$i<=5;$i++): ?>
                        <i class="fas fa-star" style="color:<?= $i <= round($rating['avg_rating']) ? '#fbbf24' : '#3a3a4a' ?>"></i>
                        <?php endfor; ?>
                        <span>(<?= $rating['total'] ?>)</span>
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
        <div class="text-center mt-4">
            <a href="lapangan.php" class="btn btn-primary btn-lg"><i class="fas fa-th"></i> Lihat Semua Lapangan</a>
        </div>
    </div>
</section>

<!-- ULASAN -->
<section class="section" style="background:var(--dark-2)">
    <div class="container">
        <div class="section-header">
            <h2>Apa Kata Mereka</h2>
            <p>Testimoni dari para pemain futsal setia kami</p>
            <div class="line"></div>
        </div>
        <?php foreach($ulasan as $u): ?>
        <div class="review-card">
            <div class="review-header">
                <div class="review-user">
                    <div class="review-avatar"><?= strtoupper(substr($u['nama'],0,1)) ?></div>
                    <div>
                        <h4><?= htmlspecialchars($u['nama']) ?></h4>
                        <small><?= htmlspecialchars($u['nama_lapangan']) ?> - <?= date('d M Y', strtotime($u['tgl_ulasan'])) ?></small>
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
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <h3><i class="fas fa-futbol"></i> Futsal<span>Zone</span></h3>
            <p>Platform booking lapangan futsal terpercaya. Pesan lapangan favoritmu dengan mudah dan cepat.</p>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Navigasi</h4>
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="lapangan.php">Lapangan</a></li>
                <li><a href="jadwal.php">Jadwal</a></li>
                <li><a href="booking.php">Booking</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Akun</h4>
            <ul>
                <li><a href="login.php">Masuk</a></li>
                <li><a href="register.php">Daftar</a></li>
                <li><a href="riwayat.php">Riwayat</a></li>
                <li><a href="ulasan.php">Ulasan</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Kontak</h4>
            <ul>
                <li><a href="#"><i class="fas fa-map-marker-alt"></i> Jl. Futsal No. 123, Jakarta</a></li>
                <li><a href="#"><i class="fas fa-phone"></i> +62 812-3456-7890</a></li>
                <li><a href="#"><i class="fas fa-envelope"></i> info@futsalzone.com</a></li>
                <li><a href="#"><i class="fas fa-clock"></i> 07:00 - 23:00 WIB</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2026 FutsalZone. Semua hak cipta dilindungi.
    </div>
</footer>

<script src="assets/js/main.js"></script>
<script>
function toggleNotif() {
    document.getElementById('notifDropdown').classList.toggle('show');
}
</script>
</body>
</html>
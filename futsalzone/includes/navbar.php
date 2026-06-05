<?php // includes/navbar.php
if(!isset($pdo)) require_once __DIR__ . '/../config/koneksi.php';
?>
<nav class="navbar" id="navbar">
    <a href="index.php" class="nav-brand">
        <i class="fas fa-futbol"></i>
        Futsal<span>Zone</span>
    </a>
    <ul class="nav-menu" id="navMenu">
        <li><a href="index.php">Beranda</a></li>
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
                <button class="notif-btn" onclick="toggleNotif()">
                    <i class="fas fa-bell"></i>
                    <?php
                    $nc = $pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE id_user=? AND dibaca=0");
                    $nc->execute([$_SESSION['user_id']]);
                    $cnt = $nc->fetchColumn();
                    if($cnt > 0): ?>
                    <span class="notif-badge"><?= $cnt ?></span>
                    <?php endif; ?>
                </button>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">Notifikasi</div>
                    <?php
                    $nfs = $pdo->prepare("SELECT * FROM notifikasi WHERE id_user=? ORDER BY tgl_notifikasi DESC LIMIT 5");
                    $nfs->execute([$_SESSION['user_id']]);
                    $notif_data = $nfs->fetchAll();
                    if($notif_data):
                        foreach($notif_data as $n): ?>
                    <div class="notif-item <?= $n['dibaca'] ? '' : 'unread' ?>">
                        <h4><?= htmlspecialchars($n['judul']) ?></h4>
                        <p><?= htmlspecialchars($n['pesan']) ?></p>
                        <small><?= date('d M Y, H:i', strtotime($n['tgl_notifikasi'])) ?></small>
                    </div>
                    <?php endforeach; else: ?>
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
<script>
function toggleNotif(){document.getElementById('notifDropdown').classList.toggle('show')}
</script>
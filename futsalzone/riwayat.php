<?php // riwayat.php
require_once 'config/koneksi.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Upload bukti pembayaran
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_bukti'])) {
    $id_pembayaran = (int)$_POST['id_pembayaran'];
    $id_booking = (int)$_POST['id_booking'];

    if(isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','webp'];
        if(in_array(strtolower($ext), $allowed)) {
            $filename = 'bukti_' . time() . '_' . $id_booking . '.' . $ext;
            $upload_dir = 'assets/upload/pembayaran/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            if(move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_dir . $filename)) {
                $upd = $pdo->prepare("UPDATE pembayaran SET bukti_pembayaran=?, status_pembayaran='Menunggu' WHERE id_pembayaran=?");
                $upd->execute([$filename, $id_pembayaran]);

                // Notifikasi
                $notif = $pdo->prepare("INSERT INTO notifikasi (id_user, judul, pesan) VALUES (?, ?, ?)");
                $notif->execute([$_SESSION['user_id'], 'Bukti Pembayaran Terupload', 'Bukti pembayaran Anda telah diterima. Menunggu konfirmasi admin.']);

                alert('Bukti pembayaran berhasil diupload!', 'success');
            } else {
                alert('Gagal mengupload file!', 'danger');
            }
        } else {
            alert('Format file tidak didukung! (JPG, PNG, WebP)', 'danger');
        }
    } else {
        alert('Pilih file terlebih dahulu!', 'danger');
    }
    header('Location: riwayat.php');
    exit;
}

// Batalkan booking
if(isset($_GET['batal'])) {
    $id = (int)$_GET['batal'];
    $upd = $pdo->prepare("UPDATE booking SET status='Dibatalkan' WHERE id_booking=? AND id_user=? AND status='Pending'");
    $upd->execute([$id, $_SESSION['user_id']]);
    if($upd->rowCount()) {
        $notif = $pdo->prepare("INSERT INTO notifikasi (id_user, judul, pesan) VALUES (?, ?, ?)");
        $notif->execute([$_SESSION['user_id'], 'Booking Dibatalkan', 'Booking Anda telah berhasil dibatalkan.']);
        alert('Booking berhasil dibatalkan.', 'success');
    }
    header('Location: riwayat.php');
    exit;
}

 $stmt = $pdo->prepare("SELECT b.*, l.nama_lapangan, l.tipe, l.gambar, p.id_pembayaran, p.metode_pembayaran, p.jumlah_bayar, p.bukti_pembayaran, p.status_pembayaran 
    FROM booking b 
    JOIN lapangan l ON b.id_lapangan=l.id_lapangan 
    LEFT JOIN pembayaran p ON b.id_booking=p.id_booking 
    WHERE b.id_user=? 
    ORDER BY b.tgl_booking DESC");
 $stmt->execute([$_SESSION['user_id']]);
 $bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - FutsalZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="breadcrumb"><a href="index.php">Beranda</a> <i class="fas fa-chevron-right"></i> Riwayat</div>
    <h1>Riwayat Booking</h1>
    <p>Lihat semua riwayat booking dan pembayaran Anda</p>
</div>

<section class="section">
    <div class="container">
        <?php showAlert(); ?>

        <?php if($bookings): ?>
        <?php foreach($bookings as $b): ?>
        <div class="card mb-3" style="padding:24px">
            <div class="flex-between mb-2" style="flex-wrap:wrap;gap:12px">
                <div>
                    <h3 style="font-size:20px;color:var(--white)"><?= htmlspecialchars($b['nama_lapangan']) ?></h3>
                    <span class="text-muted" style="font-size:13px">
                        <i class="fas fa-calendar"></i> <?= date('d F Y', strtotime($b['tanggal_main'])) ?> &bull;
                        <i class="fas fa-clock"></i> <?= date('H:i', strtotime($b['jam_mulai'])) ?> - <?= date('H:i', strtotime($b['jam_selesai'])) ?>
                    </span>
                </div>
                <span class="status-dot status-<?= strtolower($b['status']) ?>"><?= $b['status'] ?></span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-top:16px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.06)">
                <div>
                    <small class="text-muted">Kode Booking</small>
                    <p style="color:var(--white);font-weight:600">#BK<?= str_pad($b['id_booking'], 4, '0', STR_PAD_LEFT) ?></p>
                </div>
                <div>
                    <small class="text-muted">Total Harga</small>
                    <p style="color:var(--accent);font-weight:700;font-size:18px;font-family:Oswald"><?= rupiah($b['total_harga']) ?></p>
                </div>
                <div>
                    <small class="text-muted">Pembayaran</small>
                    <span class="status-dot status-<?= strtolower($b['status_pembayaran'] ?? 'menunggu') ?>"><?= $b['status_pembayaran'] ?? 'Menunggu' ?></span>
                </div>
                <div>
                    <small class="text-muted">Metode</small>
                    <p style="color:var(--white)"><?= $b['metode_pembayaran'] ?? '-' ?></p>
                </div>
            </div>

            <?php if($b['status'] == 'Pending' && $b['status_pembayaran'] == 'Menunggu' && !$b['bukti_pembayaran']): ?>
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.06)">
                <button class="btn btn-warning btn-sm" onclick="openModal('uploadModal<?= $b['id_booking'] ?>')">
                    <i class="fas fa-upload"></i> Upload Bukti Pembayaran
                </button>
                <a href="riwayat.php?batal=<?= $b['id_booking'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan booking ini?')">
                    <i class="fas fa-times"></i> Batalkan
                </a>
            </div>

            <!-- Modal Upload -->
            <div class="modal-overlay" id="uploadModal<?= $b['id_booking'] ?>">
                <div class="modal-box">
                    <button class="modal-close" onclick="closeModal('uploadModal<?= $b['id_booking'] ?>')">&times;</button>
                    <h3>Upload Bukti Pembayaran</h3>
                    <p class="text-muted mb-3">Booking #BK<?= str_pad($b['id_booking'], 4, '0', STR_PAD_LEFT) ?> - <?= rupiah($b['total_harga']) ?></p>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_pembayaran" value="<?= $b['id_pembayaran'] ?>">
                        <input type="hidden" name="id_booking" value="<?= $b['id_booking'] ?>">
                        <div class="file-upload">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Klik atau seret file ke sini</p>
                            <p><small style="color:var(--text-muted)">JPG, PNG, WebP (max 2MB)</small></p>
                            <input type="file" name="bukti" accept="image/*" required>
                            <div class="file-preview"></div>
                        </div>
                        <button type="submit" name="upload_bukti" class="btn btn-accent" style="width:100%;margin-top:16px">
                            <i class="fas fa-paper-plane"></i> Kirim Bukti
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if($b['bukti_pembayaran']): ?>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.06)">
                <small class="text-muted">Bukti Pembayaran:</small>
                <img src="assets/upload/pembayaran/<?= $b['bukti_pembayaran'] ?>" class="payment-proof-img mt-1" alt="Bukti">
            </div>
            <?php endif; ?>

            <?php if($b['status'] == 'Selesai'): ?>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.06)">
                <a href="ulasan.php?booking=<?= $b['id_booking'] ?>&lapangan=<?= $b['id_lapangan'] ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-star"></i> Beri Ulasan
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <h3>Belum ada booking</h3>
            <p>Mulai booking lapangan futsal sekarang!</p>
            <a href="lapangan.php" class="btn btn-accent mt-3"><i class="fas fa-futbol"></i> Cari Lapangan</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
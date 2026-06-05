<?php // booking.php
require_once 'config/koneksi.php';

if(!isset($_SESSION['user_id'])) {
    alert('Silakan login terlebih dahulu!', 'warning');
    header('Location: login.php');
    exit;
}

 $id_lap = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

if(!$id_lap) {
    header('Location: lapangan.php');
    exit;
}

 $lap = $pdo->prepare("SELECT * FROM lapangan WHERE id_lapangan=? AND status='Aktif'");
 $lap->execute([$id_lap]);
 $lapangan = $lap->fetch();

if(!$lapangan) {
    header('Location: lapangan.php');
    exit;
}

// Ambil jadwal tersedia
 $stmt = $pdo->prepare("SELECT j.* FROM jadwal j 
    WHERE j.id_lapangan=? 
    AND j.id_jadwal NOT IN (
        SELECT id_jadwal FROM booking WHERE id_lapangan=? AND tanggal_main=? AND status IN ('Pending','Dikonfirmasi')
    )
    ORDER BY j.jam_mulai");
 $stmt->execute([$id_lap, $id_lap, $tanggal]);
 $jadwal_tersedia = $stmt->fetchAll();

// Proses booking
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jadwal = (int)$_POST['id_jadwal'];
    $metode = $_POST['metode_pembayaran'];

    // Ambil info jadwal
    $js = $pdo->prepare("SELECT * FROM jadwal WHERE id_jadwal=? AND id_lapangan=?");
    $js->execute([$id_jadwal, $id_lap]);
    $jadwal = $js->fetch();

    if(!$jadwal) {
        $error = "Jadwal tidak valid!";
    } else {
        // Cek double booking
        $cek = $pdo->prepare("SELECT id_booking FROM booking WHERE id_jadwal=? AND tanggal_main=? AND status IN ('Pending','Dikonfirmasi')");
        $cek->execute([$id_jadwal, $tanggal]);
        if($cek->fetch()) {
            $error = "Jadwal sudah dibooking orang lain!";
        } else {
            // Hitung durasi
            $jam1 = strtotime($jadwal['jam_mulai']);
            $jam2 = strtotime($jadwal['jam_selesai']);
            $durasi = ($jam2 - $jam1) / 3600;
            $total = $durasi * $lapangan['harga_per_jam'];

            $booking_status = 'Pending';
            $pembayaran_status = 'Menunggu';
            $pesan_notif = "Booking lapangan {$lapangan['nama_lapangan']} pada $tanggal " . date('H:i', strtotime($jadwal['jam_mulai'])) . "-" . date('H:i', strtotime($jadwal['jam_selesai'])) . " berhasil. Silakan upload bukti pembayaran.";
            $alert_message = 'Booking berhasil! Silakan upload bukti pembayaran.';

            if($metode === 'Bayar di Tempat') {
                $booking_status = 'Dikonfirmasi';
                $pembayaran_status = 'Dikonfirmasi';
                $pesan_notif = "Booking lapangan {$lapangan['nama_lapangan']} pada $tanggal " . date('H:i', strtotime($jadwal['jam_mulai'])) . "-" . date('H:i', strtotime($jadwal['jam_selesai'])) . " berhasil. Silakan datang dan bayar di tempat.";
                $alert_message = 'Booking berhasil! Silakan datang dan bayar di tempat.';
            }

            $pdo->beginTransaction();
            try {
                // Insert booking
                $ins = $pdo->prepare("INSERT INTO booking (id_user, id_lapangan, id_jadwal, tanggal_main, jam_mulai, jam_selesai, total_harga, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $ins->execute([$_SESSION['user_id'], $id_lap, $id_jadwal, $tanggal, $jadwal['jam_mulai'], $jadwal['jam_selesai'], $total, $booking_status]);
                $id_booking = $pdo->lastInsertId();

                // Insert pembayaran
                $pay = $pdo->prepare("INSERT INTO pembayaran (id_booking, metode_pembayaran, jumlah_bayar, status_pembayaran) VALUES (?, ?, ?, ?)");
                $pay->execute([$id_booking, $metode, $total, $pembayaran_status]);

                if($metode === 'Bayar di Tempat') {
                    $pdo->prepare("UPDATE jadwal SET status='Dibooking' WHERE id_jadwal=?")->execute([$id_jadwal]);
                }

                // Notifikasi
                $notif = $pdo->prepare("INSERT INTO notifikasi (id_user, judul, pesan) VALUES (?, ?, ?)");
                $notif->execute([$_SESSION['user_id'], 'Booking Berhasil', $pesan_notif]);

                $pdo->commit();
                alert($alert_message, 'success');
                header("Location: riwayat.php");
                exit;
            } catch(Exception $e) {
                $pdo->rollBack();
                $error = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - FutsalZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="breadcrumb"><a href="index.php">Beranda</a> <i class="fas fa-chevron-right"></i> <a href="lapangan.php">Lapangan</a> <i class="fas fa-chevron-right"></i> Booking</div>
    <h1>Booking Lapangan</h1>
    <p>Lengkapi form berikut untuk melakukan booking</p>
</div>

<section class="section">
    <div class="container" style="max-width:700px">
        <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php showAlert(); ?>

        <!-- Info Lapangan -->
        <div class="card mb-4" style="display:flex;gap:20px;align-items:center;flex-wrap:wrap">
            <img src="<?= $lapangan['gambar'] != 'default-lapangan.jpg' ? 'assets/upload/lapangan/'.$lapangan['gambar'] : 'https://picsum.photos/seed/lap-'.$id_lap.'/300/200.jpg' ?>" style="width:200px;height:140px;object-fit:cover;border-radius:8px" alt="<?= htmlspecialchars($lapangan['nama_lapangan']) ?>">
            <div>
                <h3 style="font-size:22px;color:var(--white)"><?= htmlspecialchars($lapangan['nama_lapangan']) ?></h3>
                <span class="card-badge badge-<?= strtolower($lapangan['tipe']) ?> mb-2" style="display:inline-block"><?= $lapangan['tipe'] ?></span>
                <div class="card-price mb-0"><?= rupiah($lapangan['harga_per_jam']) ?> <span>/ jam</span></div>
            </div>
        </div>

        <form method="POST" data-validate>
            <div class="form-group">
                <label>Tanggal Main</label>
                <input type="date" class="form-control" value="<?= $tanggal ?>" readonly>
            </div>

            <div class="form-group">
                <label>Pilih Jadwal</label>
                <?php if($jadwal_tersedia): ?>
                <div style="display:flex;flex-wrap:wrap;gap:10px">
                    <?php foreach($jadwal_tersedia as $j): ?>
                    <label style="cursor:pointer">
                        <input type="radio" name="id_jadwal" value="<?= $j['id_jadwal'] ?>" required style="display:none" class="jadwal-radio" data-harga="<?= $lapangan['harga_per_jam'] ?>" data-mulai="<?= $j['jam_mulai'] ?>" data-selesai="<?= $j['jam_selesai'] ?>">
                        <div class="schedule-slot slot-tersedia" style="display:flex;align-items:center;gap:8px;transition:var(--transition)">
                            <i class="fas fa-clock"></i>
                            <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">Tidak ada jadwal tersedia untuk tanggal ini. <a href="jadwal.php?id=<?= $id_lap ?>">Lihat jadwal lain</a></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Metode Pembayaran</label>
                <select name="metode_pembayaran" class="form-control" required>
                    <option value="">Pilih metode</option>
                    <option value="Transfer BCA">Transfer BCA</option>
                    <option value="Transfer BNI">Transfer BNI</option>
                    <option value="Transfer Mandiri">Transfer Mandiri</option>
                    <option value="Transfer BRI">Transfer BRI</option>
                    <option value="E-Wallet">E-Wallet (GoPay/OVO/Dana)</option>
                    <option value="Bayar di Tempat">Bayar di Tempat</option>
                </select>
            </div>

            <!-- Ringkasan -->
            <div id="bookingSummary" class="card mb-3" style="padding:24px;display:none">
                <h3 style="font-size:18px;color:var(--accent);margin-bottom:12px"><i class="fas fa-receipt"></i> Ringkasan Booking</h3>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:var(--text-muted);font-size:14px">
                    <span>Lapangan</span><span style="color:var(--white)"><?= htmlspecialchars($lapangan['nama_lapangan']) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:var(--text-muted);font-size:14px">
                    <span>Tanggal</span><span style="color:var(--white)"><?= date('d F Y', strtotime($tanggal)) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:var(--text-muted);font-size:14px">
                    <span>Waktu</span><span style="color:var(--white)" id="summaryWaktu">-</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:var(--text-muted);font-size:14px">
                    <span>Harga/jam</span><span style="color:var(--white)"><?= rupiah($lapangan['harga_per_jam']) ?></span>
                </div>
                <hr style="border-color:rgba(255,255,255,0.1);margin:16px 0">
                <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:700">
                    <span style="color:var(--white)">Total</span><span style="color:var(--accent)" id="summaryTotal">-</span>
                </div>
            </div>

            <button type="submit" class="btn btn-accent btn-lg" style="width:100%" <?= !$jadwal_tersedia ? 'disabled' : '' ?>>
                <i class="fas fa-check-circle"></i> Konfirmasi Booking
            </button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
<script>
// Jadwal radio selection
document.querySelectorAll('.jadwal-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        // Highlight selected
        document.querySelectorAll('.jadwal-radio').forEach(r => {
            r.closest('label').querySelector('.schedule-slot').style.background = '';
            r.closest('label').querySelector('.schedule-slot').style.borderColor = '';
        });
        this.closest('label').querySelector('.schedule-slot').style.background = 'rgba(0,230,118,0.2)';
        this.closest('label').querySelector('.schedule-slot').style.borderColor = 'var(--accent)';

        // Update summary
        const harga = parseInt(this.dataset.harga);
        const mulai = this.dataset.mulai;
        const selesai = this.dataset.selesai;
        const jam1 = mulai.split(':');
        const jam2 = selesai.split(':');
        const durasi = (parseInt(jam2[0]) + parseInt(jam2[1])/60) - (parseInt(jam1[0]) + parseInt(jam1[1])/60);
        const total = durasi * harga;

        document.getElementById('summaryWaktu').textContent = mulai.substring(0,5) + ' - ' + selesai.substring(0,5) + ' (' + durasi + ' jam)';
        document.getElementById('summaryTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('bookingSummary').style.display = 'block';
    });
});
</script>
</body>
</html>
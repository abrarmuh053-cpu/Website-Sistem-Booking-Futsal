<?php // jadwal.php
require_once 'config/koneksi.php';

 $id_lap = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

 $lapangan = $pdo->query("SELECT * FROM lapangan WHERE status='Aktif' ORDER BY nama_lapangan")->fetchAll();

if(!$id_lap && $lapangan) {
    $id_lap = $lapangan[0]['id_lapangan'];
}

// Ambil jadwal
 $stmt = $pdo->prepare("SELECT j.* FROM jadwal j WHERE j.id_lapangan = ? ORDER BY FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), j.jam_mulai");
 $stmt->execute([$id_lap]);
 $jadwal = $stmt->fetchAll();

// Cek booking di tanggal tertentu
 $booked = [];
if($tanggal) {
    $b = $pdo->prepare("SELECT id_jadwal FROM booking WHERE id_lapangan=? AND tanggal_main=? AND status IN ('Pending','Dikonfirmasi')");
    $b->execute([$id_lap, $tanggal]);
    while($row = $b->fetch()) {
        $booked[] = $row['id_jadwal'];
    }
}

 $hari_list = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
 $selected_lap = null;
if($id_lap) {
    $s = $pdo->prepare("SELECT * FROM lapangan WHERE id_lapangan=?");
    $s->execute([$id_lap]);
    $selected_lap = $s->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal - FutsalZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="breadcrumb"><a href="index.php">Beranda</a> <i class="fas fa-chevron-right"></i> Jadwal</div>
    <h1>Jadwal Lapangan</h1>
    <p>Lihat ketersediaan jadwal lapangan secara real-time</p>
</div>

<section class="section">
    <div class="container">
        <div class="flex-between mb-3" style="flex-wrap:wrap;gap:16px">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
                <select class="form-control" style="width:auto;min-width:200px" onchange="window.location='jadwal.php?id='+this.value+'&tanggal=<?= $tanggal ?>'">
                    <?php foreach($lapangan as $l): ?>
                    <option value="<?= $l['id_lapangan'] ?>" <?= $l['id_lapangan']==$id_lap?'selected':'' ?>><?= htmlspecialchars($l['nama_lapangan']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="date" class="form-control" style="width:auto" value="<?= $tanggal ?>" onchange="window.location='jadwal.php?id=<?= $id_lap ?>&tanggal='+this.value">
            </div>
            <?php if($selected_lap): ?>
            <div class="card-price mb-0"><?= rupiah($selected_lap['harga_per_jam']) ?> <span>/ jam</span></div>
            <?php endif; ?>
        </div>

        <div style="display:flex;gap:16px;margin-bottom:24px">
            <div class="status-dot status-tersedia"><i class="fas fa-circle" style="font-size:8px"></i> Tersedia</div>
            <div class="status-dot status-dibooking"><i class="fas fa-circle" style="font-size:8px"></i> Dibooking</div>
        </div>

        <?php if($jadwal): ?>
        <div class="schedule-grid">
            <?php foreach($hari_list as $h):
                $slots = array_filter($jadwal, fn($j) => $j['hari'] === $h);
                if(!$slots) continue;
            ?>
            <div class="schedule-day">
                <div class="schedule-day-header"><i class="fas fa-calendar-day"></i> <?= $h ?></div>
                <div class="schedule-slots">
                    <?php foreach($slots as $s):
                        $is_booked = in_array($s['id_jadwal'], $booked);
                    ?>
                    <div class="schedule-slot <?= $is_booked ? 'slot-dibooking' : 'slot-tersedia' ?>">
                        <i class="fas fa-clock"></i>
                        <?= date('H:i', strtotime($s['jam_mulai'])) ?> - <?= date('H:i', strtotime($s['jam_selesai'])) ?>
                        <?= $is_booked ? '<i class="fas fa-lock" style="margin-left:4px"></i>' : '<i class="fas fa-check-circle" style="margin-left:4px"></i>' ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Jadwal belum tersedia</h3>
            <p>Pilih lapangan lain untuk melihat jadwal</p>
        </div>
        <?php endif; ?>

        <?php if($id_lap && isset($_SESSION['user_id'])): ?>
        <div class="text-center mt-4">
            <a href="booking.php?id=<?= $id_lap ?>&tanggal=<?= $tanggal ?>" class="btn btn-accent btn-lg"><i class="fas fa-book"></i> Booking Lapangan Ini</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
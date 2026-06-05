<?php // admin/jadwal.php
require_once '../config/koneksi.php';
if(!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Tambah jadwal
if(isset($_POST['tambah'])) {
    $id_lap = (int)$_POST['id_lapangan'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    $stmt = $pdo->prepare("INSERT INTO jadwal (id_lapangan, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_lap, $hari, $jam_mulai, $jam_selesai]);
    alert('Jadwal berhasil ditambahkan!', 'success');
    header('Location: jadwal.php');
    exit;
}

// Hapus jadwal
if(isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM jadwal WHERE id_jadwal=?")->execute([$id]);
    alert('Jadwal berhasil dihapus!', 'success');
    header('Location: jadwal.php');
    exit;
}

// Generate jadwal otomatis
if(isset($_POST['generate'])) {
    $id_lap = (int)$_POST['id_lapangan'];
    $hari_list = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
    $jam_mulai_gen = (int)$_POST['jam_mulai_gen'];
    $jam_selesai_gen = (int)$_POST['jam_selesai_gen'];
    
    $pdo->prepare("DELETE FROM jadwal WHERE id_lapangan=?")->execute([$id_lap]);
    
    foreach($hari_list as $h) {
        for($j=$jam_mulai_gen; $j<$jam_selesai_gen; $j++) {
            $pdo->prepare("INSERT INTO jadwal (id_lapangan, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?)")
                ->execute([$id_lap, $h, sprintf('%02d:00:00', $j), sprintf('%02d:00:00', $j+1)]);
        }
    }
    alert('Jadwal berhasil digenerate!', 'success');
    header('Location: jadwal.php');
    exit;
}

 $lapangan = $pdo->query("SELECT * FROM lapangan ORDER BY nama_lapangan")->fetchAll();
 $id_filter = isset($_GET['lapangan']) ? (int)$_GET['lapangan'] : ($lapangan[0]['id_lapangan'] ?? 0);

 $jadwal = [];
if($id_filter) {
    $stmt = $pdo->prepare("SELECT j.*, l.nama_lapangan FROM jadwal j JOIN lapangan l ON j.id_lapangan=l.id_lapangan WHERE j.id_lapangan=? ORDER BY FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), j.jam_mulai");
    $stmt->execute([$id_filter]);
    $jadwal = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal - FutsalZone</title>
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
            <li><a href="jadwal.php" class="active"><i class="fas fa-calendar-alt"></i> Kelola Jadwal</a></li>
            <li><a href="booking.php"><i class="fas fa-book"></i> Kelola Booking</a></li>
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
                <h3>Kelola Jadwal</h3>
            </div>
            <div style="display:flex;gap:8px">
                <button class="btn btn-primary btn-sm" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Tambah Manual</button>
                <button class="btn btn-accent btn-sm" onclick="openModal('genModal')"><i class="fas fa-magic"></i> Generate Otomatis</button>
            </div>
        </header>

        <div class="admin-content">
            <?php showAlert(); ?>

            <div class="flex-between mb-3">
                <select class="form-control" style="width:auto;min-width:200px" onchange="window.location='jadwal.php?lapangan='+this.value">
                    <?php foreach($lapangan as $l): ?>
                    <option value="<?= $l['id_lapangan'] ?>" <?= $l['id_lapangan']==$id_filter?'selected':'' ?>><?= htmlspecialchars($l['nama_lapangan']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-muted"><?= count($jadwal) ?> slot jadwal</span>
            </div>

            <div class="card" style="padding:24px">
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>#</th><th>Hari</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php foreach($jadwal as $i => $j): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td style="color:var(--white);font-weight:600"><?= $j['hari'] ?></td>
                                <td><?= date('H:i', strtotime($j['jam_mulai'])) ?></td>
                                <td><?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                                <td><span class="status-dot status-<?= strtolower($j['status']) ?>"><?= $j['status'] ?></span></td>
                                <td><a href="jadwal.php?hapus=<?= $j['id_jadwal'] ?>&lapangan=<?= $id_filter ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jadwal ini?')"><i class="fas fa-trash"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(!$jadwal): ?>
                            <tr><td colspan="6" class="text-center text-muted" style="padding:40px">Belum ada jadwal untuk lapangan ini</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Tambah Manual -->
            <div class="modal-overlay" id="addModal">
                <div class="modal-box">
                    <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
                    <h3>Tambah Jadwal Manual</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Lapangan</label>
                            <select name="id_lapangan" class="form-control" required>
                                <?php foreach($lapangan as $l): ?>
                                <option value="<?= $l['id_lapangan'] ?>" <?= $l['id_lapangan']==$id_filter?'selected':'' ?>><?= htmlspecialchars($l['nama_lapangan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Hari</label>
                            <select name="hari" class="form-control" required>
                                <?php foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h): ?>
                                <option value="<?= $h ?>"><?= $h ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" name="tambah" class="btn btn-accent" style="width:100%"><i class="fas fa-save"></i> Simpan</button>
                    </form>
                </div>
            </div>

            <!-- Modal Generate -->
            <div class="modal-overlay" id="genModal">
                <div class="modal-box">
                    <button class="modal-close" onclick="closeModal('genModal')">&times;</button>
                    <h3>Generate Jadwal Otomatis</h3>
                    <p class="text-muted mb-3" style="font-size:13px">Ini akan menghapus semua jadwal lama pada lapangan yang dipilih dan menggenerate jadwal baru per jam.</p>
                    <form method="POST">
                        <div class="form-group">
                            <label>Lapangan</label>
                            <select name="id_lapangan" class="form-control" required>
                                <?php foreach($lapangan as $l): ?>
                                <option value="<?= $l['id_lapangan'] ?>" <?= $l['id_lapangan']==$id_filter?'selected':'' ?>><?= htmlspecialchars($l['nama_lapangan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Jam Buka</label>
                                <select name="jam_mulai_gen" class="form-control">
                                    <?php for($i=6;$i<=12;$i++): ?><option value="<?= $i ?>"><?= sprintf('%02d:00', $i) ?></option><?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jam Tutup</label>
                                <select name="jam_selesai_gen" class="form-control">
                                    <?php for($i=18;$i<=24;$i++): ?><option value="<?= $i ?>" <?= $i==23?'selected':'' ?>><?= sprintf('%02d:00', $i) ?></option><?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="generate" class="btn btn-accent" style="width:100%"><i class="fas fa-magic"></i> Generate Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
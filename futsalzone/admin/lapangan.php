<?php // admin/lapangan.php
require_once '../config/koneksi.php';
if(!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Tambah / Edit
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)($_POST['id_lapangan'] ?? 0);
    $nama = trim($_POST['nama_lapangan']);
    $tipe = $_POST['tipe'];
    $harga = (int)$_POST['harga_per_jam'];
    $deskripsi = trim($_POST['deskripsi']);
    $status = $_POST['status'];
    $gambar_lama = $_POST['gambar_lama'] ?? 'default-lapangan.jpg';

    $gambar = $gambar_lama;
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','webp'];
        if(in_array(strtolower($ext), $allowed)) {
            $gambar = 'lap_' . time() . '.' . $ext;
            $dir = '../assets/upload/lapangan/';
            if(!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($_FILES['gambar']['tmp_name'], $dir . $gambar);
            if($gambar_lama != 'default-lapangan.jpg' && file_exists($dir . $gambar_lama)) {
                unlink($dir . $gambar_lama);
            }
        }
    }

    if($id > 0) {
        $stmt = $pdo->prepare("UPDATE lapangan SET nama_lapangan=?, tipe=?, harga_per_jam=?, deskripsi=?, gambar=?, status=? WHERE id_lapangan=?");
        $stmt->execute([$nama, $tipe, $harga, $deskripsi, $gambar, $status, $id]);
        alert('Lapangan berhasil diperbarui!', 'success');
    } else {
        $stmt = $pdo->prepare("INSERT INTO lapangan (nama_lapangan, tipe, harga_per_jam, deskripsi, gambar, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $tipe, $harga, $deskripsi, $gambar, $status]);
        alert('Lapangan berhasil ditambahkan!', 'success');
    }
    header('Location: lapangan.php');
    exit;
}

// Hapus
if(isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM lapangan WHERE id_lapangan=?");
    $stmt->execute([$id]);
    alert('Lapangan berhasil dihapus!', 'success');
    header('Location: lapangan.php');
    exit;
}

 $lapangan = $pdo->query("SELECT * FROM lapangan ORDER BY id_lapangan DESC")->fetchAll();
 $edit = null;
if(isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM lapangan WHERE id_lapangan=?");
    $stmt->execute([$id]);
    $edit = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lapangan - FutsalZone</title>
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
            <li><a href="lapangan.php" class="active"><i class="fas fa-futbol"></i> Kelola Lapangan</a></li>
            <li><a href="jadwal.php"><i class="fas fa-calendar-alt"></i> Kelola Jadwal</a></li>
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
                <h3>Kelola Lapangan</h3>
            </div>
            <button class="btn btn-accent btn-sm" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Tambah Lapangan</button>
        </header>

        <div class="admin-content">
            <?php showAlert(); ?>

            <div class="card" style="padding:24px">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr><th>#</th><th>Gambar</th><th>Nama</th><th>Tipe</th><th>Harga/Jam</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach($lapangan as $i => $l): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><img src="<?= $l['gambar'] != 'default-lapangan.jpg' ? '../assets/upload/lapangan/'.$l['gambar'] : 'https://picsum.photos/seed/lap-'.$l['id_lapangan'].'/80/50.jpg' ?>" style="width:80px;height:50px;object-fit:cover;border-radius:6px" alt=""></td>
                                <td style="color:var(--white);font-weight:600"><?= htmlspecialchars($l['nama_lapangan']) ?></td>
                                <td><span class="badge-<?= strtolower($l['tipe']) ?> card-badge"><?= $l['tipe'] ?></span></td>
                                <td style="color:var(--accent);font-weight:600"><?= rupiah($l['harga_per_jam']) ?></td>
                                <td><span class="status-dot status-<?= strtolower($l['status']) ?>"><?= $l['status'] ?></span></td>
                                <td>
                                    <a href="lapangan.php?edit=<?= $l['id_lapangan'] ?>" class="btn btn-warning btn-sm" onclick="openModal('addModal')"><i class="fas fa-edit"></i></a>
                                    <a href="lapangan.php?hapus=<?= $l['id_lapangan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus lapangan ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Add/Edit -->
            <div class="modal-overlay" id="addModal" <?= $edit ? 'style="display:flex"' : '' ?>>
                <div class="modal-box">
                    <button class="modal-close" onclick="closeModal('addModal');window.location='lapangan.php'">&times;</button>
                    <h3><?= $edit ? 'Edit' : 'Tambah' ?> Lapangan</h3>
                    <form method="POST" enctype="multipart/form-data" data-validate>
                        <input type="hidden" name="id_lapangan" value="<?= $edit['id_lapangan'] ?? 0 ?>">
                        <input type="hidden" name="gambar_lama" value="<?= $edit['gambar'] ?? 'default-lapangan.jpg' ?>">
                        <div class="form-group">
                            <label>Nama Lapangan</label>
                            <input type="text" name="nama_lapangan" class="form-control" value="<?= htmlspecialchars($edit['nama_lapangan'] ?? '') ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tipe</label>
                                <select name="tipe" class="form-control" required>
                                    <option value="Indoor" <?= ($edit['tipe'] ?? '') == 'Indoor' ? 'selected' : '' ?>>Indoor</option>
                                    <option value="Outdoor" <?= ($edit['tipe'] ?? '') == 'Outdoor' ? 'selected' : '' ?>>Outdoor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Harga/Jam</label>
                                <input type="number" name="harga_per_jam" class="form-control" value="<?= $edit['harga_per_jam'] ?? '' ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($edit['deskripsi'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="Aktif" <?= ($edit['status'] ?? 'Aktif') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?= ($edit['status'] ?? '') == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Gambar</label>
                            <div class="file-upload">
                                <i class="fas fa-image"></i>
                                <p>Klik untuk upload gambar</p>
                                <input type="file" name="gambar" accept="image/*">
                                <div class="file-preview">
                                    <?php if($edit && $edit['gambar'] != 'default-lapangan.jpg'): ?>
                                    <img src="../assets/upload/lapangan/<?= $edit['gambar'] ?>" alt="">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-accent" style="width:100%"><i class="fas fa-save"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
<?php if($edit): ?><script>openModal('addModal');</script><?php endif; ?>
</body>
</html>
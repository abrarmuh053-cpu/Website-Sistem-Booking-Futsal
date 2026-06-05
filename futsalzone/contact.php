<?php // contact.php
require_once 'config/koneksi.php';

$nama = '';
$email = '';
$subjek = '';
$pesan = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $subjek = trim($_POST['subjek']);
    $pesan = trim($_POST['pesan']);

    if(empty($nama) || empty($email) || empty($pesan)) {
        alert('Harap isi semua field yang wajib!', 'danger');
    } else {
        $stmt = $pdo->prepare("INSERT INTO kontak (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)");
        if($stmt->execute([$nama, $email, $subjek, $pesan])) {
            alert('Pesan berhasil dikirim! Kami akan segera menghubungi Anda.', 'success');
        } else {
            alert('Gagal mengirim pesan. Coba lagi.', 'danger');
        }
    }
    header('Location: contact.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - FutsalZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="breadcrumb"><a href="index.php">Beranda</a> <i class="fas fa-chevron-right"></i> Kontak</div>
    <h1>Hubungi Kami</h1>
    <p>Ada pertanyaan atau masukan? Silakan hubungi kami</p>
</div>

<section class="section">
    <div class="container" style="max-width:900px">
        <?php showAlert(); ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px">
            <!-- Info Kontak -->
            <div>
                <h3 style="font-size:24px;color:var(--white);margin-bottom:24px">Informasi Kontak</h3>
                <div style="display:flex;flex-direction:column;gap:20px">
                    <div style="display:flex;gap:16px;align-items:flex-start">
                        <div style="width:48px;height:48px;border-radius:12px;background:rgba(0,230,118,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-map-marker-alt" style="color:var(--accent);font-size:20px"></i>
                        </div>
                        <div>
                            <h4 style="color:var(--white);font-size:15px;font-family:Poppins;font-weight:600">Alamat</h4>
                            <p style="color:var(--text-muted);font-size:14px">Jl. Latimojong No. 13, Kelurahan Cutio, Enrekang Selatan</p>
                        </div>
                    </div>
                    <div style="display:flex;gap:16px;align-items:flex-start">
                        <div style="width:48px;height:48px;border-radius:12px;background:rgba(0,230,118,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-phone-alt" style="color:var(--accent);font-size:20px"></i>
                        </div>
                        <div>
                            <h4 style="color:var(--white);font-size:15px;font-family:Poppins;font-weight:600">Telepon</h4>
                            <p style="color:var(--text-muted);font-size:14px">+62 823-1596-9987</p>
                        </div>
                    </div>
                    <div style="display:flex;gap:16px;align-items:flex-start">
                        <div style="width:48px;height:48px;border-radius:12px;background:rgba(0,230,118,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-envelope" style="color:var(--accent);font-size:20px"></i>
                        </div>
                        <div>
                            <h4 style="color:var(--white);font-size:15px;font-family:Poppins;font-weight:600">Email</h4>
                            <p style="color:var(--text-muted);font-size:14px">info@futsalzone.com</p>
                        </div>
                    </div>
                    <div style="display:flex;gap:16px;align-items:flex-start">
                        <div style="width:48px;height:48px;border-radius:12px;background:rgba(0,230,118,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-clock" style="color:var(--accent);font-size:20px"></i>
                        </div>
                        <div>
                            <h4 style="color:var(--white);font-size:15px;font-family:Poppins;font-weight:600">Jam Operasional</h4>
                            <p style="color:var(--text-muted);font-size:14px">Senin - Minggu: 07:00 - 23:00 WITA</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="card" style="padding:28px">
                <h3 style="font-size:20px;color:var(--white);margin-bottom:20px">Kirim Pesan</h3>
                <form method="POST" data-validate>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama" value="<?= htmlspecialchars($nama) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email" value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Subjek</label>
                        <input type="text" name="subjek" class="form-control" placeholder="Subjek pesan" value="<?= htmlspecialchars($subjek) ?>">
                    </div>
                    <div class="form-group">
                        <label>Pesan</label>
                        <textarea name="pesan" class="form-control" rows="5" placeholder="Tulis pesan Anda..." required><?= htmlspecialchars($pesan) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-accent" style="width:100%"><i class="fas fa-paper-plane"></i> Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
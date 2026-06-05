<?php // register.php
require_once 'config/koneksi.php';

if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$nama = '';
$email = '';
$no_hp = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $no_hp = trim($_POST['no_hp']);

    if($password !== $password_confirm) {
        $error = 'Password dan konfirmasi password tidak sama.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if($user) {
            $error = 'Email sudah terdaftar. Silakan login atau gunakan email lain.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO user (nama, email, password, no_hp) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $email, $hash, $no_hp]);

            alert('Pendaftaran berhasil! Silakan login.', 'success');
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - FutsalZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-logo"><i class="fas fa-futbol"></i></div>
        <h2>Buat Akun Baru</h2>
        <p class="auth-sub">Daftar untuk mulai booking lapangan</p>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php showAlert(); ?>

        <form method="POST" data-validate>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama" value="<?= htmlspecialchars($nama) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" class="form-control" placeholder="Masukkan nomor HP" value="<?= htmlspecialchars($no_hp) ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirm" class="form-control" placeholder="Konfirmasi password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-accent btn-lg" style="width:100%"><i class="fas fa-user-plus"></i> Daftar</button>
        </form>
        <div class="auth-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>

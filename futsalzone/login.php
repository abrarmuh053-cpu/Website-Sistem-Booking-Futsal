<?php // login.php
require_once 'config/koneksi.php';

if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_email'] = $user['email'];
        alert('Selamat datang, ' . $user['nama'] . '!', 'success');
        header('Location: index.php');
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FutsalZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-logo"><i class="fas fa-futbol"></i></div>
        <h2>Masuk ke Akun</h2>
        <p class="auth-sub">Silakan login untuk melanjutkan booking</p>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php showAlert(); ?>

        <form method="POST" data-validate>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-accent btn-lg" style="width:100%"><i class="fas fa-sign-in-alt"></i> Masuk</button>
        </form>
        <div class="auth-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
        <div class="auth-link mt-2">
            <a href="admin/login.php" style="color:var(--text-muted);font-size:12px"><i class="fas fa-lock"></i> Login Admin</a>
        </div>
    </div>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>
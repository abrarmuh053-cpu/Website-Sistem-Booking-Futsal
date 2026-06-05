<?php // admin/login.php
require_once '../config/koneksi.php';

if(isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$email = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['admin_nama'] = $admin['nama'];
        $_SESSION['admin_email'] = $admin['email'];
        alert('Selamat datang, Admin!', 'success');
        header('Location: dashboard.php');
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
    <title>Login Admin - FutsalZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-logo"><i class="fas fa-user-shield"></i></div>
        <h2>Login Admin</h2>
        <p class="auth-sub">Panel administrasi FutsalZone</p>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php showAlert(); ?>

        <form method="POST" data-validate>
            <div class="form-group">
                <label>Email Admin</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email admin" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-accent btn-lg" style="width:100%"><i class="fas fa-sign-in-alt"></i> Masuk</button>
        </form>
        <div class="auth-link mt-2">
            <a href="../index.php" style="color:var(--text-muted);font-size:13px"><i class="fas fa-arrow-left"></i> Kembali ke Website</a>
        </div>
    </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
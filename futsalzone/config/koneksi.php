<?php
// config/koneksi.php

 $host = 'localhost';
 $dbname = 'booking_futsal';
 $username = 'root';
 $password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

session_start();

function base_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . ($path === '' || $path === '/' ? '' : $path);
}

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function alert($pesan, $tipe = 'success') {
    $_SESSION['alert'] = ['pesan' => $pesan, 'tipe' => $tipe];
}

function showAlert() {
    if (isset($_SESSION['alert'])) {
        $a = $_SESSION['alert'];
        echo "<div class='alert alert-{$a['tipe']}' onclick='this.remove()'>{$a['pesan']}</div>";
        unset($_SESSION['alert']);
    }
}
?>
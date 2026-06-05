<?php // admin/logout.php
require_once '../config/koneksi.php';
unset($_SESSION['admin_id']);
unset($_SESSION['admin_nama']);
unset($_SESSION['admin_email']);
session_destroy();
header('Location: login.php');
exit;
?>
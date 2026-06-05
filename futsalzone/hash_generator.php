<?php
// File untuk generate bcrypt hash password
$password = '#Abrarganz241105';
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Password: " . htmlspecialchars($password) . "<br>";
echo "Bcrypt Hash:<br>";
echo "<code style='background:#f0f0f0; padding:10px; display:block; word-break:break-all;'>" . htmlspecialchars($hash) . "</code>";
echo "<br><br>";
echo "Gunakan hash di atas untuk UPDATE SQL di phpMyAdmin";
?>

<?php
session_start(); // Oturumu başlat
session_unset(); // Tüm oturum değişkenlerini boşalt
session_destroy(); // Oturumu tamamen sonlandır

// Burayı login.php yapıyoruz:
header("Location: ../Pages/login.php");
exit();
?>
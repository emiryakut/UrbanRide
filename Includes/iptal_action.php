<?php
session_start();
require_once 'db.php';

if (isset($_POST['iptal_et']) && isset($_SESSION['user_id'])) {
    $talep_id = $_POST['talep_id'];
    $user_id = $_SESSION['user_id'];

    // Güvenlik: Sadece talebin sahibi olan yolcu iptal edebilir
    $sorgu = "DELETE FROM yolcu_talepleri WHERE id = '$talep_id' AND yolcu_id = '$user_id' AND durum = 'beklemede'";
    
    if (mysqli_query($conn, $sorgu)) {
        header("Location: ../Pages/ilanlarim.php?mesaj=iptal_basarili");
    } else {
        header("Location: ../Pages/ilanlarim.php?hata=iptal_hatasi");
    }
}
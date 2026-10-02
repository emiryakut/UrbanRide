<?php
session_start();
include 'db.php';

if (isset($_POST['talep_gonder'])) {
    $yolcu_id = $_SESSION['user_id'];
    $baslangic = mysqli_real_escape_string($conn, $_POST['baslangic']);
    $varis = mysqli_real_escape_string($conn, $_POST['varis']);
    $mesafe = mysqli_real_escape_string($conn, $_POST['mesafe']);
    $fiyat = mysqli_real_escape_string($conn, $_POST['fiyat']);

    $sql = "INSERT INTO yolcu_talepleri (yolcu_id, baslangic_konumu, varis_konumu, mesafe, fiyat, durum) 
            VALUES ('$yolcu_id', '$baslangic', '$varis', '$mesafe', '$fiyat', 'beklemede')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../Pages/ilanlarim.php?durum=basarili");
        exit();
    } else {
        echo "SQL Hatası: " . mysqli_error($conn);
    }
}
?>
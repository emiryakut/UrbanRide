<?php
session_start();
include 'db.php'; 

if (isset($_POST['cekme_onay']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $iban = mysqli_real_escape_string($conn, $_POST['iban_tam']);

    // 1. Mevcut bakiyeyi çekip değişkene kaydediyoruz
    $sorgu = mysqli_query($conn, "SELECT bakiye FROM kullanicilar WHERE id = '$user_id'");
    $row = mysqli_fetch_assoc($sorgu);
    $cekilen_miktar = $row['bakiye'];

    // 2. Bakiyeyi sıfırlıyoruz
    $guncelle = mysqli_query($conn, "UPDATE kullanicilar SET bakiye = 0, son_iban = '$iban' WHERE id = '$user_id'");
    
    if ($guncelle) {
        // 3. Miktarı URL parametresi olarak (miktar=...) geri gönderiyoruz
        header("Location: ../Pages/odemelerim.php?islem=basarili&miktar=" . $cekilen_miktar);
        exit();
    }
}
<?php
session_start();
include 'db.php';

if (isset($_POST['kirala']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $arac_id = mysqli_real_escape_string($conn, $_POST['arac_id']);

    // 1. Aracın fiyatını ve plakasını alalım
    $arac_sorgu = mysqli_query($conn, "SELECT fiyat, plaka FROM araclar WHERE id = '$arac_id'");
    $arac_data = mysqli_fetch_assoc($arac_sorgu);
    
    if (!$arac_data) {
        header("Location: ../Pages/arac_kirala.php?hata=arac_bulunamadi");
        exit();
    }

    $fiyat = (float)$arac_data['fiyat'];

    // 2. Kullanıcının zaten bir aracı var mı kontrol et
    $kontrol = mysqli_query($conn, "SELECT id FROM araclar WHERE surucu_id = '$user_id'");
    if (mysqli_num_rows($kontrol) > 0) {
        header("Location: ../Pages/arac_kirala.php?hata=zaten_kiralik_var");
        exit();
    }

    // 3. Veritabanı İşlemleri (Transaction başlatmak daha güvenlidir ama mevcut yapına uygun devam ediyoruz)
    
    // Aracı kullanıcıya ata ve müsaitliğini kapat
    $arac_guncelle = "UPDATE araclar SET musaitlik = 0, surucu_id = '$user_id' WHERE id = '$arac_id'";
    
    // Kullanıcının ana bakiyesinden kiralama bedelini kalıcı olarak düş
    $bakiye_guncelle = "UPDATE kullanicilar SET bakiye = bakiye - $fiyat WHERE id = '$user_id'";

    if (mysqli_query($conn, $arac_guncelle) && mysqli_query($conn, $bakiye_guncelle)) {
        // İşlem başarılıysa dashboard'a veya kiralama sayfasına yönlendir
        header("Location: ../Pages/arac_kirala.php?durum=basarili");
        exit();
    } else {
        echo "Veritabanı hatası: " . mysqli_error($conn);
    }
}
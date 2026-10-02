<?php
session_start();
include 'db.php';

ob_start();
ob_clean(); 

if (isset($_POST['id']) && isset($_SESSION['user_id'])) {
    $surucu_id = $_SESSION['user_id'];
    $talep_id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // 1. ADIM: Araç ve Talep Bilgilerini Al (Ücret ve Yolcu ID lazım)
    $talep_sorgu = mysqli_query($conn, "SELECT yolcu_id, fiyat FROM yolcu_talepleri WHERE id = '$talep_id' AND durum = 'beklemede'");
    $arac_sorgu = mysqli_query($conn, "SELECT id FROM araclar WHERE surucu_id = '$surucu_id' LIMIT 1");
    
    if (mysqli_num_rows($talep_sorgu) > 0 && mysqli_num_rows($arac_sorgu) > 0) {
        $talep_data = mysqli_fetch_assoc($talep_sorgu);
        $yolcu_id = $talep_data['yolcu_id'];
        $ucret = $talep_data['fiyat'];

        // VERİTABANI İŞLEMİ (TRANSACTION) BAŞLAT
        // Bu sayede üç sorgudan biri hata verirse hiçbiri gerçekleşmez (para kaybolmaz)
        mysqli_begin_transaction($conn);

        try {
            // A. Talebi Onayla
            mysqli_query($conn, "UPDATE yolcu_talepleri SET durum = 'onaylandi', surucu_id = '$surucu_id' WHERE id = '$talep_id'");

            // B. Yolcudan Parayı Düş (Bakiyesi eksiye düşebilir, borçlanır)
            mysqli_query($conn, "UPDATE kullanicilar SET bakiye = bakiye - $ucret WHERE id = '$yolcu_id'");

            // C. Sürücüye Parayı Ekle
            mysqli_query($conn, "UPDATE kullanicilar SET bakiye = bakiye + $ucret WHERE id = '$surucu_id'");

            // Her şey tamamsa onaylıyoruz
            mysqli_commit($conn);
            echo "success";

        } catch (Exception $e) {
            // Bir hata olursa geri al
            mysqli_rollback($conn);
            echo "error";
        }

    } else {
        echo "no_vehicle";
    }
} else {
    echo "unauthorized";
}
exit();
?>
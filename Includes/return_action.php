<?php
session_start();
include 'db.php'; 

if (isset($_POST['iade_et'])) {
    $arac_id = $_POST['arac_id'];
    $surucu_id = $_SESSION['user_id'];

    // Aracı tekrar müsait yap (1) ve sürücü bağını kopar (NULL)
    $sorgu = "UPDATE araclar SET musaitlik = 1, surucu_id = NULL WHERE id = '$arac_id' AND surucu_id = '$surucu_id'";
    
    $iade = mysqli_query($conn, $sorgu);

    if ($iade) {
        // Başarılıysa araç kiralama sayfasına yönlendir
        header("Location: ../Pages/arac_kirala.php?durum=iade_basarili");
    } else {
        // Hata varsa geri gönder
        header("Location: ../Pages/kiralanan_araclarim.php?hata=iade_edilemedi");
    }
    exit();
}
?>
<?php
session_start();
require_once 'db.php';

// AJAX talebi geldiğinde hataları sadece metin olarak döndürmek için
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =====================
// KAYIT OLMA (AJAX UYUMLU)
// =====================
if(isset($_POST['kayit_et'])) {

    // Verileri temizle
    $ad = mysqli_real_escape_string($conn, $_POST['ad_soyad'] ?? '');
    $tc = mysqli_real_escape_string($conn, $_POST['tc_no'] ?? '');
    $rol = mysqli_real_escape_string($conn, $_POST['rol'] ?? 'yolcu');
    $tel = mysqli_real_escape_string($conn, $_POST['telefon'] ?? '');
    $sifre_ham = $_POST['sifre'] ?? '';
    $dogum_tarihi = mysqli_real_escape_string($conn, $_POST['dogum_tarihi'] ?? '');
    
    // Ehliyet tarihi kontrolü
    $ehliyet_tarihi_raw = $_POST['ehliyet_tarihi'] ?? null;
    $ehliyet_tarihi = (!empty($ehliyet_tarihi_raw)) ? "'".mysqli_real_escape_string($conn, $ehliyet_tarihi_raw)."'" : "NULL";

    // Şifre hash (SHA-256)
    $sifre_guvenli = hash('sha256', $sifre_ham);

    // Kayıtlı kullanıcı kontrolü (Opsiyonel ama AJAX için çok şık olur)
    $kontrol = mysqli_query($conn, "SELECT id FROM kullanicilar WHERE tc_no = '$tc' OR telefon = '$tel'");
    if(mysqli_num_rows($kontrol) > 0) {
        echo "Bu TC numarası veya telefon zaten kayıtlı!";
        exit();
    }

    // INSERT Sorgusu
    $ekle = "INSERT INTO kullanicilar 
    (ad_soyad, tc_no, rol, telefon, sifre, dogum_tarihi, ehliyet_tarihi) 
    VALUES 
    ('$ad', '$tc', '$rol', '$tel', '$sifre_guvenli', '$dogum_tarihi', '$ehliyet_tarihi')";

    if(mysqli_query($conn, $ekle)) {
        // Yönlendirme YAPMIYORUZ, sadece "success" yazıyoruz
        echo "success";
        exit();
    } else {
        // Hata durumunda teknik mesajı döndürürsek JS bunu ekrana basar
        echo "Veritabanı hatası: " . mysqli_error($conn);
        exit();
    }
}



// =====================
// GİRİŞ YAPMA
// =====================
if(isset($_POST['giris_yap'])) {

    $tc = mysqli_real_escape_string($conn, $_POST['tc_no'] ?? '');
    $sifre_ham = $_POST['sifre'] ?? '';
    $sifre_hash = hash('sha256', $sifre_ham);

    $sorgu = "SELECT *, 
              TIMESTAMPDIFF(YEAR, ehliyet_tarihi, CURDATE()) AS deneyim_yili 
              FROM kullanicilar 
              WHERE tc_no='$tc' AND sifre='$sifre_hash'";

    $sonuc = mysqli_query($conn, $sorgu);

    if($sonuc && mysqli_num_rows($sonuc) > 0) {

        $k = mysqli_fetch_assoc($sonuc);

        $_SESSION['user_id'] = $k['id'];
        $_SESSION['ad_soyad'] = $k['ad_soyad'];
        $_SESSION['rol'] = $k['rol'];
        $_SESSION['telefon'] = $k['telefon'];
        $_SESSION['dogum_tarihi'] = $k['dogum_tarihi'];
        $_SESSION['deneyim_yili'] = $k['deneyim_yili'];

        header("Location: ../Pages/dashboard.php?ad=" . urlencode($k['ad_soyad']));
        exit();

    } else {
        header("Location: ../Pages/login.php?durum=hata");
        exit();
    }
}
?>
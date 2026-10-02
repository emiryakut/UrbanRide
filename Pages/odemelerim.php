<?php 
session_start(); 
include '../Includes/db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 1. MANTIK: BORÇ ÖDEME SIFIRLAMA (bankalar.php'den geliyorsa) ---
if (isset($_GET['odendi']) && $_GET['odendi'] == '1') {
    // Veritabanındaki bakiyeyi gerçekten 0 yapıyoruz
    mysqli_query($conn, "UPDATE kullanicilar SET bakiye = 0 WHERE id = '$user_id'");
    
    // URL'yi temizlemek ve işlemi bitirmek için yönlendiriyoruz
    header("Location: odemelerim.php?islem=borc_odendi");
    exit();
}

// --- 2. VERİLERİ ÇEK ---
$user_sorgu = mysqli_query($conn, "SELECT bakiye FROM kullanicilar WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_sorgu);
$guncel_db_bakiyesi = (float)$user_data['bakiye'];
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'yolcu'; 

$islemler = [];

// --- 3. PARA ÇEKME KAYDI (URL'den miktar geliyorsa listeye ekle) ---
if (isset($_GET['miktar']) && (float)$_GET['miktar'] > 0) {
    $islemler[] = [
        'aciklama' => "Para Çekme İşlemi (Tamamlandı)", 
        'tur' => 'Ödeme', 
        'tutar' => (float)$_GET['miktar'], 
        'isaret' => '-'
    ];
}

// 4. GİDER: Kiralanan Araçlar
if ($rol === 'surucu') {
    $arac_sorgu = mysqli_query($conn, "SELECT marka, model, fiyat FROM araclar WHERE surucu_id = '$user_id'");
    while($arac = mysqli_fetch_assoc($arac_sorgu)) {
        $islemler[] = [
            'aciklama' => $arac['marka']." ".$arac['model']." (Kiralama)", 
            'tur' => 'Gider', 
            'tutar' => (float)$arac['fiyat'], 
            'isaret' => '-'
        ];
    }
}

// 5. KAZANÇ/GİDER: Yolculuk Geçmişi
$yol_sql = ($rol === 'surucu') 
    ? "SELECT baslangic_konumu, varis_konumu, fiyat FROM yolcu_talepleri WHERE surucu_id = '$user_id' AND durum = 'onaylandi'"
    : "SELECT baslangic_konumu, varis_konumu, fiyat FROM yolcu_talepleri WHERE yolcu_id = '$user_id' AND durum = 'onaylandi'";

$yolculuk_sorgu = mysqli_query($conn, $yol_sql);
if($yolculuk_sorgu){
    while($yolculuk = mysqli_fetch_assoc($yolculuk_sorgu)) {
        $islemler[] = [
            'aciklama' => $yolculuk['baslangic_konumu']." -> ".$yolculuk['varis_konumu'],
            'tur' => ($rol === 'surucu' ? 'Kazanç' : 'Gider'),
            'tutar' => (float)$yolculuk['fiyat'],
            'isaret' => ($rol === 'surucu' ? '+' : '-')
        ];
    }
}

// Görünüm Ayarları
$bakiye_isaret = ($guncel_db_bakiyesi >= 0) ? "+" : "-";
$bakiye_renk = ($guncel_db_bakiyesi >= 0) ? "#2ecc71" : "#e74c3c";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finansal Özet - UrbanDrive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/odemelerim.css">
    
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h2><i class="fas fa-wallet"></i> Finansal Özetim</h2>
        <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Panele Dön</a>
    </div>

    <div class="grid">
        <!-- İŞLEM LİSTESİ -->
        <div class="finance-card">
            <h3>Son İşlemler</h3>
            <table>
                <?php if(empty($islemler)): ?>
                    <tr><td colspan="3" style="text-align:center; color:#999; padding:30px;">Henüz bir işlem bulunmuyor.</td></tr>
                <?php else: ?>
                    <?php foreach($islemler as $islem): ?>
                    <tr>
                        <td><?= htmlspecialchars($islem['aciklama']) ?></td>
                        <td class="<?= ($islem['tur']=='Kazanç'?'badge-kazanc':($islem['tur']=='Ödeme'?'badge-odeme':'badge-gider')) ?>">
                            <?= $islem['tur'] ?>
                        </td>
                        <td style="text-align:right; font-weight:bold;">
                            <?= $islem['isaret'] ?> ₺<?= number_format($islem['tutar'], 2, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>

        <!-- BAKİYE KARTI -->
        <div>
            <div class="finance-card" style="background: #2d3436; color: white;">
                <p style="opacity:0.7; margin-top:0;">Toplam Net Bakiye</p>
                <h2 style="color: <?= $bakiye_renk ?>; font-size: 32px; margin: 10px 0;">
                    ₺<?= number_format($guncel_db_bakiyesi, 2, ',', '.') ?>
                </h2>
                <small style="display:block; margin-bottom:15px; opacity:0.6;">* Bu rakam veritabanındaki güncel kasanızı temsil eder.</small>
                <?php if ($guncel_db_bakiyesi < 0): ?>
                    <a href="bankalar.php" style="text-decoration: none;">
                        <button type="button" class="btn-cek" style="background: #e74c3c;">
                            <i class="fas fa-credit-card"></i> Borç Öde (Bankalar)
                        </button>
                    </a>
                <?php elseif ($guncel_db_bakiyesi > 0): ?>
                    <a href="para_cekme_talebi.php" style="text-decoration: none;">
                        <button type="button" class="btn-cek" style="background: #2ecc71;">
                            <i class="fas fa-hand-holding-usd"></i> Para Çekme Talebi
                        </button>
                    </a>
                <?php else: ?>
                    <button type="button" class="btn-cek" style="background: #95a5a6; cursor: not-allowed;" onclick="alert('İşlem yapılabilecek bir bakiyeniz bulunmamaktadır.')">
                        <i class="fas fa-info-circle"></i> İşlem Yapılamaz
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
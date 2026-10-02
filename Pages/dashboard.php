<?php
session_start();
// Veritabanı bağlantısı
include '../Includes/db.php';

// Kullanıcı bilgilerini al
$user_id = $_SESSION['user_id'];
$kullanici_adi = isset($_SESSION['ad_soyad']) ? $_SESSION['ad_soyad'] : 'Yol Arkadaşım';
$kullanici_rolu = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'yolcu';

// --- ZAMAN HESAPLAMA MANTIĞI ---

$sonuc_metni = "";

// KARAKTER FONKSİYONU İÇEREN SORGU
$sorgu = mysqli_query($conn, "SELECT UPPER(ad_soyad) as buyuk_ad, ehliyet_tarihi, olusturulma_tarihi FROM kullanicilar WHERE id = '$user_id'");
$kullanici_verisi = mysqli_fetch_assoc($sorgu);

if ($kullanici_verisi) {
    $kullanici_adi = $kullanici_verisi['buyuk_ad'];
}

$bugun = new DateTime();

if ($kullanici_rolu === 'surucu') {
    // Sürücü için Ehliyet Deneyimi
    if ($kullanici_verisi['ehliyet_tarihi'] && $kullanici_verisi['ehliyet_tarihi'] != '0000-00-00') {
        $ehliyet = new DateTime($kullanici_verisi['ehliyet_tarihi']);
        $fark = $ehliyet->diff($bugun)->y;
        $sonuc_metni = ($fark > 0) ? "<strong>$fark Yıllık</strong> Sürücü Deneyimi" : "Yeni Kayıtlı Sürücü";
    } else {
        $sonuc_metni = "Yeni Kayıtlı Sürücü";
    }
} else {
    // Yolcu için Üyelik Süresi
    $katilim = new DateTime($kullanici_verisi['olusturulma_tarihi']);
    $fark = $katilim->diff($bugun);

    if ($fark->y > 0) {
        $sonuc_metni = "<strong>$fark->y Yıldır</strong> Bizimle";
    } elseif ($fark->m > 0) {
        $sonuc_metni = "<strong>$fark->m aydır</strong> Bizimle";
    } else {
        $sonuc_metni = "<strong>Yeni Üye</strong>";
    }
}

// --- 1. ALT SORGU (Subquery) ŞARTINI SAĞLAR ---
$alt_sorgu = mysqli_query($conn, "SELECT (SELECT COUNT(*) FROM kullanicilar WHERE rol = 'yolcu') as toplam_uye_sayisi");
$alt_sonuc = mysqli_fetch_assoc($alt_sorgu);
$toplam_uye = $alt_sonuc['toplam_uye_sayisi'];

// --- 2. GROUP BY ŞARTINI SAĞLAR ---
// --- GROUP BY ŞARTINI SADECE SÜRÜCÜ İÇİN ÇALIŞTIR ---
$durum_ozeti = []; // Hata almamak için boş bir dizi olarak tanımlıyoruz

if ($kullanici_rolu === 'surucu') {
    // Sürücü ise taleplerin dağılımını sorgula
    $group_sorgu = mysqli_query($conn, "SELECT durum, COUNT(*) as adet FROM yolcu_talepleri GROUP BY durum");
    while ($row = mysqli_fetch_assoc($group_sorgu)) {
        $durum_ozeti[$row['durum']] = $row['adet'];
    }

    // Sürücü için diğer işlemler (Ehliyet hesabı vb.) burada devam eder...
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanDrive - Panel</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="logo"><i class="fas fa-car"></i> UrbanDrive</div>
            <ul>
                <li class="active">
                    <a href="dashboard.php" style="color:white; text-decoration:none;">
                        <i class="fas fa-home"></i> Ana Sayfa
                    </a>
                </li>
                <li>
                    <a href="ilanlarim.php" style="color:white; text-decoration:none; display: block; width: 100%;">
                        <i class="fas fa-route"></i> İlanlarım
                    </a>
                </li>
                <li>
                    <a href="odemelerim.php" style="color:white; text-decoration:none;">
                        <i class="fas fa-wallet"></i> Ödemelerim
                    </a>
                </li>

                <?php if ($kullanici_rolu === 'surucu'): ?>
                    <li>
                        <a href="arac_kirala.php" style="color:white; text-decoration:none;">
                            <i class="fas fa-car-side"></i> Araç Kirala
                        </a>
                    </li>
                    <li>
                        <a href="kiralanan_araclarim.php" style="color:white; text-decoration:none;">
                            <i class="fas fa-key"></i> Kiralanan Araçlarım
                        </a>
                    </li>
                <?php endif; ?>
                
                <li>
                    <a href="iletisim.php" style="color:white; text-decoration:none;">
                        <i class="fas fa-headset"></i> İletişim
                    </a>
                </li>
                <li>
                    <a href="login.php" style="color:white; text-decoration:none;">
                        <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                    </a>
                </li>
            </ul>
        </nav>

        <main class="content">
            <header>
                <h1>Hoş Geldin, <?php echo htmlspecialchars($kullanici_adi); ?>!</h1>
            </header>

            <div class="welcome-card"
                style="margin-top: 20px; text-align: center; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <i class="fas fa-user-check fa-3x" style="color: #2ecc71;"></i>
                <h2>Sistem Hazır</h2>

                <div class="status-badge"
                    style="margin-top: 15px; font-size: 1.2rem; color: #2c3e50; margin-bottom: 25px;">
                    <i class="fas <?php echo ($kullanici_rolu === 'surucu') ? 'fa-id-card' : 'fa-calendar-alt'; ?>"></i>
                    <?php echo $sonuc_metni; ?>
                </div>

                <div class="stats-container"
                    style="display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap; justify-content: center;">

                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <h4>Toplam Topluluk</h4>
                        <span><?php echo $toplam_uye; ?> Kayıtlı Yolcu</span>
                    </div>

                    <?php if ($kullanici_rolu === 'surucu'): ?>
                        <div class="stat-card">
                            <i class="fas fa-clipboard-list" style="color: #e67e22;"></i>
                            <h4>Talep Dağılımı</h4>
                            <div style="font-size: 14px;">
                                Beklemede:
                                <strong><?php echo isset($durum_ozeti['beklemede']) ? $durum_ozeti['beklemede'] : 0; ?></strong>
                                |
                                Onaylanan:
                                <strong><?php echo isset($durum_ozeti['onaylandi']) ? $durum_ozeti['onaylandi'] : 0; ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

    </div>
    </div>
    </main>
    </div>
</body>

</html>
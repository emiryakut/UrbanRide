<?php 
session_start(); 
include '../Includes/db.php'; 

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Araç Kirala - UrbanDrive</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Kartlara özel hover ve genel stil düzenlemeleri */
        .arac-kart {
            background: white; 
            padding: 20px; 
            border-radius: 15px; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05); 
            transition: all 0.3s ease;
            border-top: 5px solid transparent;
        }
        .arac-kart:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .plaka-badge {
            background: #f1f2f6; 
            padding: 5px 10px; 
            border-radius: 8px; 
            font-size: 12px; 
            font-weight: bold; 
            color: #2f3542;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <main class="content" style="padding: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="margin:0;"><i class="fas fa-car"></i> Kiralanabilir Araçlar</h2>
                <a href="dashboard.php" style="text-decoration: none; color: #3498db; font-weight:bold;">
                    <i class="fas fa-arrow-left"></i> Panele Dön
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
                <?php
                // Veritabanı sorgusu
                $sorgu = mysqli_query($conn, "SELECT * FROM araclar WHERE musaitlik = 1 ORDER BY fiyat ASC");
                
                if(mysqli_num_rows($sorgu) > 0) {
                    while($arac = mysqli_fetch_assoc($sorgu)) {
                        $renk = ($arac['fiyat'] > 3000) ? '#e74c3c' : '#2ecc71';
                        ?>
                        
                        <div class="arac-kart" style="border-top-color: <?php echo $renk; ?>;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h3 style="margin: 0; color: #2c3e50;"><?php echo $arac['marka']; ?></h3>
                                    <p style="margin: 5px 0; color: #7f8c8d; font-weight: 500;"><?php echo $arac['model']; ?></p>
                                </div>
                                <span class="plaka-badge"><?php echo $arac['plaka']; ?></span>
                            </div>

                            <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">

                            <div style="margin-bottom: 20px;">
                                <span style="font-size: 24px; font-weight: 800; color: #2d3436;">
                                    ₺<?php echo number_format($arac['fiyat'], 2, ',', '.'); ?>
                                </span>
                                <span style="color: #636e72; font-size: 14px;"> / Günlük</span>
                            </div>

                            <form action="../Includes/rent_action.php" method="POST">
                                <input type="hidden" name="arac_id" value="<?php echo $arac['id']; ?>">
                                <button type="submit" name="kirala" style="background: <?php echo $renk; ?>; color: white; border: none; padding: 12px; width: 100%; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 16px;">
                                    <i class="fas fa-key"></i> Hemen Kirala
                                </button>
                            </form>
                        </div>

                    <?php }
                } else {
                    echo '<div style="grid-column: 1/-1; text-align: center; padding: 60px; background: white; border-radius: 15px; border: 2px dashed #ccc;">
                            <i class="fas fa-car-crash fa-4x" style="color: #ddd; margin-bottom: 20px;"></i>
                            <p style="font-size: 18px; color: #777;">Şu an kiralanabilir araç bulunmamaktadır.</p>
                          </div>';
                }
                ?>
            </div>
        </main>
    </div>

    <!-- JavaScript Uyarı Mekanizması -->
    <script>
        // URL'deki parametreleri kontrol et
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('hata')) {
            const hata = urlParams.get('hata');
            if (hata === 'zaten_kiralik_var') {
                alert("Uyarı: Zaten aktif bir kiralama işleminiz bulunuyor! Yeni bir araç kiralamak için mevcut aracınızı iade etmelisiniz.");
            }
        }

        if (urlParams.has('durum')) {
            const durum = urlParams.get('durum');
            if (durum === 'iade_basarili') {
                alert("İşlem Başarılı: Araç iade edildi. Yeni kiralama yapabilirsiniz.");
            } else if (durum === 'basarili') {
                alert("Tebrikler! Aracınız başarıyla kiralandı.");
            }
        }

        // Uyarıdan sonra URL'yi temizle (Yenileme yapıldığında tekrar çıkmasın)
        if (window.location.search.length > 0) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    </script>
</body>
</html>
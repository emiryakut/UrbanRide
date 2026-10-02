<?php 
session_start(); 
include '../Includes/db.php'; 

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$surucu_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kiralanan Araçlarım - UrbanDrive</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <main class="content" style="padding: 40px;">
            <h2><i class="fas fa-key"></i> Mevcut Aracım</h2>
            
            <div style="margin-top: 20px;">
                <?php
                // Sürücüye zimmetli olan aracı getir
                $sorgu = mysqli_query($conn, "SELECT * FROM araclar WHERE surucu_id = '$surucu_id' AND musaitlik = 0 LIMIT 1");
                
                // Hatayı düzelten kısım: Veriyi kontrol ederken aynı zamanda değişkene atıyoruz
                if($arac = mysqli_fetch_assoc($sorgu)) {
                    echo '
                    <div style="background: #f9f9f9; padding: 25px; border-radius: 15px; border-left: 5px solid #3498db; max-width: 500px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <h3 style="margin-top:0; color:#333;">'.$arac['marka'].' '.$arac['model'].'</h3>
                        <p style="margin: 10px 0;"><strong>Plaka:</strong> <span style="background:#eee; padding:2px 6px; border-radius:4px;">'.$arac['plaka'].'</span></p>
                        <p style="color: #27ae60; font-weight:bold;"><i class="fas fa-check-circle"></i> Bu araç şu an kullanımınızda.</p>
                        
                        <!-- Aracı İade Et Butonu -->
                        <form action="../Includes/return_action.php" method="POST" style="margin-top: 20px;">
                            <input type="hidden" name="arac_id" value="'.$arac['id'].'">
                            <button type="submit" name="iade_et" style="background: #e74c3c; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; width: 100%; font-weight:bold; transition: 0.3s;">
                                <i class="fas fa-undo"></i> Aracı İade Et (Boşa Çıkar)
                            </button>
                        </form>
                    </div>';
                } else {
                    echo '
                    <div style="background: #fff; padding: 30px; border-radius: 15px; text-align: center; border: 2px dashed #ccc;">
                        <i class="fas fa-car-side fa-3x" style="color: #ddd; margin-bottom: 15px;"></i>
                        <p style="color: #666; font-size: 18px;">Henüz kiraladığınız bir araç bulunmuyor.</p>
                        <a href="arac_kirala.php" style="display: inline-block; margin-top: 15px; background: #3498db; color: white; padding: 10px 25px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                            Araç Kiralamaya Git <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>';
                }
                ?>
            </div>
            <p style="margin-top: 30px;"><a href="dashboard.php" style="color: #666; text-decoration: none;"><i class="fas fa-chevron-left"></i> Panele Geri Dön</a></p>
        </main>
    </div>
</body>
</html>
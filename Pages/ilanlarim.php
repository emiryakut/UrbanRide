<?php
session_start();
require_once '../Includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$rol_sorgu = mysqli_query($conn, "SELECT rol FROM kullanicilar WHERE id='$user_id'");
$kullanici = mysqli_fetch_assoc($rol_sorgu);
$rol = $kullanici['rol'];

$ilceler = [
    "Adalar","Arnavutköy","Ataşehir","Avcılar","Bağcılar","Bahçelievler",
    "Bakırköy","Başakşehir","Bayrampaşa","Beşiktaş","Beykoz","Beylikdüzü",
    "Beyoğlu","Büyükçekmece","Çatalca","Çekmeköy","Esenler","Esenyurt",
    "Eyüpsultan","Fatih","Gaziosmanpaşa","Güngören","Kadıköy","Kağıthane",
    "Kartal","Küçükçekmece","Maltepe","Pendik","Sancaktepe","Sarıyer",
    "Silivri","Sultanbeyli","Sultangazi","Şile","Şişli","Tuzla",
    "Ümraniye","Üsküdar","Zeytinburnu"
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İlanlarım - UrbanDrive</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
</head>

<body>

<div class="dashboard-container">

    <nav class="sidebar">
        <div class="logo"><i class="fas fa-car"></i> UrbanDrive</div>

        <ul>
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-home"></i> Ana Sayfa
                </a>
            </li>

            <li class="active">
                <i class="fas fa-route"></i> İlanlarım
            </li>
            <li>
                <a href="odemelerim.php" style="color:white; text-decoration:none;">
                    <i class="fas fa-wallet"></i> Ödemelerim
                </a>
            </li>
            <li>
                <a href="login.php">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
            </li>
        </ul>
    </nav>

    <main class="content">

        <?php if($rol == 'yolcu'): ?>

        <?php
        $aktif_kontrol = mysqli_query($conn,
        "SELECT id FROM yolcu_talepleri 
        WHERE yolcu_id='$user_id' 
        AND durum='beklemede' LIMIT 1");

        $aktif_talep_sayisi = mysqli_num_rows($aktif_kontrol);
        ?>

        <div class="welcome-card">

            <?php if($aktif_talep_sayisi > 0): ?>

            <div class="warning-box">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Dikkat:</strong> Mevcut bir talebiniz bulunmaktadır.
            </div>

            <?php else: ?>

            <h3><i class="fas fa-map-marked-alt"></i> Yolculuk Planla</h3>

            <select id="route_from" class="custom-input" onchange="ilceFiltrele()">
                <option value="">Nereden? (İlçe Seçin)</option>

                <?php foreach($ilceler as $ilce): ?>
                    <option value="<?= $ilce ?>"><?= $ilce ?></option>
                <?php endforeach; ?>
            </select>

            <select id="route_to" class="custom-input">
                <option value="">Nereye? (İlçe Seçin)</option>

                <?php foreach($ilceler as $ilce): ?>
                    <option value="<?= $ilce ?>"><?= $ilce ?></option>
                <?php endforeach; ?>
            </select>

            <button type="button" onclick="hesaplaYolculuk()" id="btn_hesapla" class="btn-primary btn-full">
                Mesafe ve Fiyat Hesapla
            </button>

            <div id="preview_area" class="preview-box">

                <p id="dist_text"></p>

                <div id="price_options" class="price-options"></div>

                <form action="../Includes/talep_action.php" method="POST">

                    <input type="hidden" name="baslangic" id="hidden_from">
                    <input type="hidden" name="varis" id="hidden_to">
                    <input type="hidden" name="mesafe" id="hidden_dist">
                    <input type="hidden" name="fiyat" id="hidden_price">

                    <button type="submit" name="talep_gonder" class="btn-success btn-full">
                        Seçilen Fiyatı Onayla
                    </button>

                </form>
            </div>

            <?php endif; ?>

        </div>

        <?php endif; ?>

        <div class="ilan-listesi">

            <h3><?= ($rol == 'surucu') ? "Yolcu Talepleri" : "Taleplerim"; ?></h3>

            <?php

            if($rol == 'surucu') {

                $sql = "SELECT t.*,k.ad_soyad,k.telefon
                        FROM yolcu_talepleri t
                        JOIN kullanicilar k ON t.yolcu_id=k.id
                        WHERE t.durum IN ('beklemede','onaylandi')
                        ORDER BY t.id DESC";

            } else {

                $sql = "SELECT * FROM yolcu_talepleri
                        WHERE yolcu_id='$user_id'
                        ORDER BY id DESC";
            }

            $sorgu = mysqli_query($conn,$sql);

            if(mysqli_num_rows($sorgu) > 0):

            while($satir = mysqli_fetch_assoc($sorgu)):

            $onayli = $satir['durum'] == 'onaylandi';
            ?>

            <div class="stat-card <?= $onayli ? 'approved' : '' ?>">

                <p>
                    <strong><i class="fas fa-map-pin"></i> Rota:</strong>
                    <?= $satir['baslangic_konumu']; ?>
                    <i class="fas fa-arrow-right"></i>
                    <?= $satir['varis_konumu']; ?>
                </p>

                <p>
                    <strong><i class="fas fa-ruler"></i> Mesafe:</strong>
                    <?= $satir['mesafe'] ?? '0'; ?> KM
                </p>

                <p class="price-tag">
                    <i class="fas fa-lira-sign"></i>
                    <?= $satir['fiyat'] ?? '0'; ?> TL
                </p>

                <?php if($rol == 'surucu'): ?>

                <hr>

                <p>
                    <strong><i class="fas fa-user"></i> Yolcu:</strong>
                    <?= $satir['ad_soyad']; ?>
                </p>

                <?php if($satir['durum'] == 'beklemede'): ?>

                <button
                    id="btn-<?= $satir['id']; ?>"
                    onclick="kabulEt(<?= $satir['id']; ?>,'<?= $satir['telefon']; ?>')"
                    class="btn-success btn-full">

                    Yolcuyu Kabul Et

                </button>

                <?php endif; ?>

                <div id="tel-<?= $satir['id']; ?>"
                     class="tel-display"
                     style="display:<?= $onayli ? 'flex' : 'none'; ?>">

                    <i class="fas fa-phone"></i>
                    <?= $satir['telefon']; ?>

                </div>

                <?php else: ?>

                <p>
                    <strong><i class="fas fa-info-circle"></i> Durum:</strong>

                    <span class="<?= $onayli ? 'status-approved' : 'status-waiting'; ?>">
                        <?= ucfirst($satir['durum']); ?>
                    </span>
                </p>

                <?php if($satir['durum'] == 'beklemede'): ?>

                <form action="../Includes/iptal_action.php"
                      method="POST"
                      onsubmit="return confirm('İptal edilsin mi?');">

                    <input type="hidden" name="talep_id" value="<?= $satir['id']; ?>">

                    <button type="submit" name="iptal_et" class="btn-danger btn-full">
                        Talebi İptal Et
                    </button>

                </form>

                <?php endif; ?>

                <?php endif; ?>

            </div>

            <?php endwhile; ?>

            <?php else: ?>

            <p class="empty-text">Henüz ilan bulunmamaktadır.</p>

            <?php endif; ?>

        </div>

    </main>

</div>

<script>

function ilceFiltrele(){

    let nereden=document.getElementById("route_from").value;
    let nereyeSelect=document.getElementById("route_to");

    for(let i=0;i<nereyeSelect.options.length;i++){

        let opt=nereyeSelect.options[i];

        if(opt.value===nereden && nereden!==""){

            opt.disabled=true;
            opt.style.color="#ccc";

            if(nereyeSelect.value===nereden){
                nereyeSelect.value="";
            }

        } else {

            opt.disabled=false;
            opt.style.color="#000";
        }
    }
}

</script>

<script src="../JS/validation.js"></script>

</body>
</html>
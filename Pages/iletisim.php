<?php
session_start();
include '../Includes/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Şubeleri çek
$sorgu = mysqli_query($conn, "SELECT * FROM subeler ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanDrive - İletişim</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .contact-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 30px;
            justify-content: center;
        }

        .branch-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 25px;
            width: calc(45% - 20px);
            /* Yan yana iki tane durması için */
            min-width: 300px;
            transition: transform 0.3s ease;
            border-top: 5px solid #3498db;
        }

        .branch-card:hover {
            transform: translateY(-10px);
        }

        .branch-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.4rem;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
            color: #555;
            line-height: 1.5;
        }

        .info-item i {
            color: #3498db;
            margin-top: 4px;
            width: 20px;
        }

        .work-hours {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #eee;
            font-weight: 600;
            color: #27ae60;
        }

        @media (max-width: 768px) {
            .branch-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="logo"><i class="fas fa-car"></i> UrbanDrive</div>
            <ul>
                <li><a href="dashboard.php" style="color:white; text-decoration:none;"><i class="fas fa-home"></i> Ana
                        Sayfa</a></li>
                <li class="active"><a href="iletisim.php" style="color:white; text-decoration:none;"><i
                            class="fas fa-headset"></i> İletişim</a></li>
                <li><a href="login.php" style="color:white; text-decoration:none;"><i class="fas fa-sign-out-alt"></i>
                        Çıkış Yap</a></li>
            </ul>
        </nav>

        <main class="content">
            <header>
                <h1>Bize Ulaşın</h1>
                <p>Şubelerimiz üzerinden her türlü destek ve kiralama işlemi için yanınızdayız.</p>
            </header>

            <div class="contact-container">
                <?php while ($sube = mysqli_fetch_assoc($sorgu)): ?>
                    <div class="branch-card">
                        <h3><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($sube['sirket_adi']); ?></h3>

                        <div class="info-item">
                            <i class="fas fa-location-dot"></i>
                            <span><?php echo htmlspecialchars($sube['adres']); ?></span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-phone-alt"></i>
                            <span><?php echo htmlspecialchars($sube['telefon_numarasi']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-id-card"></i>
                            <span>Vergi No: <?php echo htmlspecialchars($sube['vergi_numarasi']); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>
</body>

</html>
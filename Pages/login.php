<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanDrive - Giriş Yap</title>
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <a href="../home.php" class="logo">
                    <i class="fas fa-car"></i> UrbanDrive
                </a>
                <h2>Tekrar Hoş Geldiniz!</h2>
                
                <?php if(isset($_GET['durum']) && $_GET['durum'] == "hata"): ?>
                    <p style="color: #ff4d4d; font-weight: bold;">Hata: TC No veya Şifre hatalı!</p>
                <?php elseif(isset($_GET['durum']) && $_GET['durum'] == "ok"): ?>
                    <p style="color: #2ecc71; font-weight: bold;">Kayıt başarılı! Giriş yapabilirsiniz.</p>
                <?php else: ?>
                    <p>Lütfen bilgilerinizi girerek oturum açın.</p>
                <?php endif; ?>
            </div>

            <form class="login-form" action="../Includes/auth_action.php" method="POST">
                
                <div class="input-group">
                    <label>TC Kimlik Numarası</label> <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="tc_no" placeholder="11 haneli TC numaranız" maxlength="11" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Şifre</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="sifre" placeholder="Şifrenizi girin" required>
                    </div>
                </div>

                <div class="form-options">
                    <label><input type="checkbox" name="beni_hatirla"> Beni Hatırla</label>
                    <a href="#">Şifremi Unuttum</a>
                </div>

                <button type="submit" name="giris_yap" class="btn-login-submit">Giriş Yap</button>

                <div class="login-footer">
                    Henüz bir hesabınız yok mu? <a href="signup.php">Kayıt Ol</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
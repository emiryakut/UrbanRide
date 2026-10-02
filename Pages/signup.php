<?php 
$hata = isset($_GET['hata']) ? $_GET['hata'] : null;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanDrive - Kayıt Ol</title>
    <link rel="stylesheet" href="../CSS/signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* AJAX Mesaj Kutusu için ek stil */
        #ajax-mesaj {
            display: none;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            border: 1px solid transparent;
        }
    </style>
</head>
<body>
    <div class="signup-page">
        <div class="signup-card">
            <div class="signup-header">
                <a href="../home.php" class="logo">
                    <i class="fas fa-car"></i> UrbanDrive
                </a>
                <p>Hangi rol ile kayıt olmak istersiniz?</p>
            </div>

            <!-- AJAX Mesaj Alanı -->
            <div id="ajax-mesaj"></div>

            <div class="tabs">
                <div class="tab active" id="tab-yolcu" onclick="switchTab('yolcu')">
                    <i class="fas fa-user"></i> Yolcu
                </div>
                <div class="tab" id="tab-surucu" onclick="switchTab('surucu')">
                    <i class="fas fa-id-card"></i> Sürücü
                </div>
            </div>

            <form id="form" class="signup-form" action="../Includes/auth_action.php" method="POST">
                <input type="hidden" name="rol" id="user_role" value="yolcu">

                <div id="yolcu-fields"> 
                    <div class="input-row">
                        <div class="input-group">
                            <label>Ad Soyad</label>
                            <input type="text" id="ad_soyad" name="ad_soyad" placeholder="Ad Soyad" required>
                        </div>
                        <div class="input-group">
                            <label>TC Kimlik No</label>
                            <input type="text" id="tc_no" name="tc_no" maxlength="11" placeholder="11 haneli TC" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                    </div>
                    <div class="input-row">
                        <div class="input-group">
                            <label>Telefon</label>
                            <input type="text" id="telefon" name="telefon" maxlength="11" placeholder="05XXXXXXXXX" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div class="input-group">
                            <label>Doğum Tarihi</label>
                            <input type="date" name="dogum_tarihi" required>
                        </div>
                    </div>
                </div>

                <div id="surucu-fields" style="display:none;">
                    <div class="input-row">
                        <div class="input-group">
                            <label>Ehliyet Tarihi</label>
                            <input type="date" name="ehliyet_tarihi" id="ehliyet_input">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label>Şifre</label>
                    <input type="password" id="sifre" name="sifre" placeholder="••••••••" required>
                </div>

                <button type="submit" name="kayit_et" id="submit_btn" class="btn-signup-submit">
                    Kayıt Ol
                </button>
            </form> 
        </div>
    </div>

<script>
function switchTab(role) {
    const surucuFields = document.getElementById('surucu-fields');
    const roleInput = document.getElementById('user_role');
    const tabYolcu = document.getElementById('tab-yolcu');
    const tabSurucu = document.getElementById('tab-surucu');
    const ehliyetInput = document.getElementById('ehliyet_input');

    if(role === 'surucu') {
        surucuFields.style.display = 'block';
        tabSurucu.classList.add('active');
        tabYolcu.classList.remove('active');
        roleInput.value = 'surucu';
        ehliyetInput.required = true;
    } else {
        surucuFields.style.display = 'none';
        tabYolcu.classList.add('active');
        tabSurucu.classList.remove('active');
        roleInput.value = 'yolcu';
        ehliyetInput.required = false;
    }
}

document.getElementById("form").addEventListener("submit", function(e) {
    e.preventDefault(); 

    const mesajKutusu = document.getElementById("ajax-mesaj");
    const submitBtn = document.getElementById("submit_btn");
    const rol = document.getElementById('user_role').value;
    
    let tc = document.getElementById("tc_no").value.trim();
    let telefon = document.getElementById("telefon").value.trim();

    // Temel JS Doğrulamaları
    if (tc.length !== 11) { alert("TC 11 haneli olmalı!"); return; }
    if (telefon.length !== 11) { alert("Telefon 11 haneli olmalı!"); return; }

    if (rol === 'surucu') {
        const dogumInput = document.getElementsByName('dogum_tarihi')[0].value;
        const ehliyetInput = document.getElementsByName('ehliyet_tarihi')[0].value;
        if (!dogumInput || !ehliyetInput) { alert("Lütfen tarihleri doldurun!"); return; }

        const dogum = new Date(dogumInput);
        const ehliyet = new Date(ehliyetInput);
        let kontrol = new Date(dogum);
        kontrol.setFullYear(kontrol.getFullYear() + 18);
        if (ehliyet < kontrol) { alert("Ehliyet tarihi, 18 yaşından küçük olamaz!"); return; }
    }

    // Arayüz Hazırlığı
    mesajKutusu.style.display = "none";
    submitBtn.disabled = true;
    submitBtn.innerText = "İşlem yapılıyor...";

    const formData = new FormData(this);
    formData.append('kayit_et', 'true'); 

    // ÖNEMLİ: Eğer 404 alıyorsan "../Includes/auth_action.php" yolunu klasör yapına göre kontrol et
    fetch(this.getAttribute('action'), {
    method: "POST",
    body: formData  
})
    .then(response => {
    // response.ok kontrolü ve throw Error kısmı kaldırıldı
    return response.text();
})
    .then(data => {
    mesajKutusu.style.display = "block";
    const res = data.trim();
    
    if (res === "success") {
        mesajKutusu.style.backgroundColor = "#d4edda";
        mesajKutusu.style.color = "#155724";
        mesajKutusu.style.borderColor = "#c3e6cb";
        mesajKutusu.innerText = "Kayıt Başarılı! Giriş ekranına gidiliyor...";
        
        setTimeout(() => {
            window.location.href = "../Pages/login.php"; 
        }, 2000);
    } else {
        mesajKutusu.style.backgroundColor = "#f8d7da";
        mesajKutusu.style.color = "#721c24";    
        mesajKutusu.style.borderColor = "#f5c6cb";
        mesajKutusu.innerText = "Hata: " + res;
        submitBtn.disabled = false;
        submitBtn.innerText = "Kayıt Ol";
    }
});
// .catch bloğu tamamen silindi
});
</script>
</body>
</html>
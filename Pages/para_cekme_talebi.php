<?php
session_start();
include '../Includes/db.php'; 
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$sorgu = mysqli_query($conn, "SELECT ad_soyad FROM kullanicilar WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($sorgu);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Para Çekme Talebi</title>
    <style>
        /* Paylaştığın CSS kodlarını buraya aynen yapıştır */
        .iban-container { max-width: 450px; margin: 50px auto; font-family: 'Segoe UI', sans-serif; background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border-top: 6px solid #3498db; }
        .header-text { text-align: center; color: #2c3e50; margin-bottom: 25px; }
        .info-box { background: #f1f7fe; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #3498db; }
        .input-group { margin-bottom: 20px; position: relative; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; }
        .iban-wrapper { position: relative; display: flex; align-items: center; }
        .prefix { position: absolute; left: 15px; font-weight: bold; color: #3498db; font-size: 18px; letter-spacing: 1px; }
        .iban-input { width: 100%; padding: 12px 15px 12px 45px; font-size: 18px; border: 2px solid #dfe6e9; border-radius: 10px; outline: none; transition: 0.3s; letter-spacing: 2px; color: #2c3e50; }
        .submit-btn { width: 100%; padding: 15px; background: #3498db; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .char-count { text-align: right; font-size: 12px; color: #95a5a6; margin-top: 5px; }
    </style>
</head>
<body>

<div class="iban-container">
    <div class="header-text">
        <h2>Para Çekme Talebi</h2>
        <p>Bakiyenizi güvenle hesabınıza aktarın</p>
    </div>

    <div class="info-box">
        <small style="color: #7f8c8d; display:block;">Sürücü:</small>
        <strong><?= $user['ad_soyad'] ?></strong>
    </div>

    <form action="../Includes/talep_islet.php" method="POST" onsubmit="return validateFinal()">
        <div class="input-group">
            <label>IBAN Numaranız</label>
            <div class="iban-wrapper">
                <span class="prefix">TR</span>
                <input type="text" id="ibanField" class="iban-input" placeholder="0000 0000 0000..." maxlength="24" oninput="formatInput(this)" required>
            </div>
            <div class="char-count"><span id="counter">0</span> / 24</div>
            <input type="hidden" name="iban_tam" id="fullIban">
        </div>
        <button type="submit" name="cekme_onay" class="submit-btn">Ödemeyi Onayla ve Çek</button>
    </form>
</div>

<script>
function formatInput(input) {   
    let value = input.value.replace(/[^0-9]/g, '');
    input.value = value;
    document.getElementById('counter').innerText = value.length;
    document.getElementById('fullIban').value = "TR" + value;
}

function validateFinal() {
    let val = document.getElementById('ibanField').value;
    if (val.length < 24) {
        alert("IBAN 24 hane olmalıdır!");
        return false;
    }
    return confirm("Bakiyeniz sıfırlanacaktır, onaylıyor musunuz?");
}
</script>
</body>
</html>
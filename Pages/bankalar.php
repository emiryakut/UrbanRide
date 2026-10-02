<?php
session_start(); // Oturum kontrolü için gerekli
include '../Includes/db.php'; 

// Banka bilgilerini çekiyoruz
$sorgu = mysqli_query($conn, "SELECT * FROM odeme");
?>

<div class="borc-odeme-paneli">
    <h2 style="color: #2c3e50; text-align: center;">Kurumsal Ödeme Kanalları</h2>
    <p style="text-align: center;">Ödemenizi yaparken açıklama kısmına <strong>Şoför ID</strong> numaranızı yazmayı unutmayınız.</p>

    <div class="banka-kartlari-konteynir" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; margin-top: 30px;">
        <?php while ($banka = mysqli_fetch_assoc($sorgu)): ?>
            <div class="banka-kart" style="border: 1px solid #ddd; border-radius: 12px; padding: 20px; width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background: #fff;">
                <h3 style="margin-top: 0; color: #e74c3c; border-bottom: 2px solid #f1f1f1; padding-bottom: 10px;">
                    <?= htmlspecialchars($banka['banka_adi']) ?>
                </h3>
                <div style="font-size: 14px; line-height: 1.6;">
                    <p><strong>Alıcı:</strong><br> <?= htmlspecialchars($banka['hesap_sahibi']) ?></p>
                    <p><strong>Şube Kodu:</strong> <?= htmlspecialchars($banka['sube_kodu']) ?></p>
                    <p><strong>IBAN:</strong><br> 
                        <code style="background: #f8f9fa; padding: 5px; display: block; margin-top: 5px; border-radius: 4px;" id="iban-<?= $banka['id'] ?>">
                            <?= htmlspecialchars($banka['iban']) ?>
                        </code>
                    </p>
                </div>
                <button onclick="copyAndReset('<?= $banka['iban'] ?>')" style="width: 100%; margin-top: 15px; padding: 10px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    IBAN Kopyala & Öde
                </button>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
function copyAndReset(text) {
    // 1. IBAN'ı panoya kopyala
    navigator.clipboard.writeText(text).then(() => {
        // Kullanıcıya bilgi ver
        alert("IBAN kopyalandı! Borç ödeme işleminiz kaydediliyor...");

        // 2. odemelerim.php sayfasına 'odendi=1' parametresiyle yönlendir
        // Bu parametre sayesinde sayfa açıldığında bakiye veritabanında 0 yapılacak.
        window.location.href = 'odemelerim.php?odendi=1';
        
    }).catch(err => {
        console.error('Kopyalama hatası:', err);
        alert("IBAN kopyalanamadı, lütfen manuel kopyalamayı deneyin.");
    });
}
</script>
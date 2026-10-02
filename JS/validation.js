// Sayfa yüklendiğinde çalışacak kontroller
document.addEventListener('DOMContentLoaded', function() {
    const fromInput = document.getElementById('route_from');
    const toInput = document.getElementById('route_to');
    const btnHesapla = document.getElementById('btn_hesapla');
    const previewArea = document.getElementById('preview_area');

    // Eğer bu elementler sayfada yoksa (örn: şoför paneli veya aktif talep uyarısı varsa)
    // aşağıdaki fonksiyonları bağlama, hatayı engelle.
    if (fromInput && toInput && btnHesapla) {
        
        const resetForm = () => {
            btnHesapla.disabled = false;
            btnHesapla.innerText = "Mesafe ve Fiyat Hesapla";
            btnHesapla.style.opacity = "1";
            btnHesapla.style.cursor = "pointer";
            if(previewArea) previewArea.style.display = 'none';
        };

        fromInput.oninput = resetForm;
        toInput.oninput = resetForm;
    }
});

function hesaplaYolculuk() {
    const fromInput = document.getElementById('route_from');
    const toInput = document.getElementById('route_to');
    const btnHesapla = document.getElementById('btn_hesapla');

    // Elementlerin varlığını kontrol et (Hata önleyici)
    if (!fromInput || !toInput || !btnHesapla) return;

    const from = fromInput.value;
    const to = toInput.value;

    if (!from || !to) {
        alert("Lütfen rotayı girin!");
        return;
    }

    // Buton geri bildirimi
    btnHesapla.disabled = true;
    btnHesapla.innerText = "Fiyat Hesaplandı";
    btnHesapla.style.opacity = "0.6";
    btnHesapla.style.cursor = "not-allowed";

    // Hesaplama Parametreleri
    const acilisUcreti = 50; 
    const kmBasiUcret = 40;
    const otobanFarki = 200;
    const mesafe = (Math.random() * 20 + 5).toFixed(1); 
    
    const temelFiyat = parseFloat((mesafe * kmBasiUcret + acilisUcreti).toFixed(2));
    const ucretliYolFiyat = temelFiyat + otobanFarki;

    // UI Güncelleme
    const distText = document.getElementById('dist_text');
    const priceOptions = document.getElementById('price_options');
    const previewArea = document.getElementById('preview_area');

    if(distText) distText.innerText = "Tahmini Mesafe: " + mesafe + " KM";
    
    if(priceOptions) {
        priceOptions.innerHTML = `
            <div class="option-card" onclick="secimYap('ucretsiz', ${temelFiyat})">
                <input type="radio" name="yol_tipi" id="opt_ucretsiz" checked>
                <label for="opt_ucretsiz"><strong>Ücretsiz Yol:</strong> ${temelFiyat} TL</label>
            </div>
            <div class="option-card" onclick="secimYap('ucretli', ${ucretliYolFiyat})">
                <input type="radio" name="yol_tipi" id="opt_ucretli">
                <label for="opt_ucretli"><strong>Ücretli Yol (Otoban):</strong> ${ucretliYolFiyat} TL</label>
            </div>
        `;
    }

    // Gizli inputları doldur (Varlıkları kontrol edilerek)
    const hFrom = document.getElementById('hidden_from');
    const hTo = document.getElementById('hidden_to');
    const hDist = document.getElementById('hidden_dist');
    const hPrice = document.getElementById('hidden_price');

    if(hFrom) hFrom.value = from;
    if(hTo) hTo.value = to;
    if(hDist) hDist.value = mesafe;
    if(hPrice) hPrice.value = temelFiyat;

    if(previewArea) previewArea.style.display = 'block';
}

function secimYap(tip, fiyat) {
    const hPrice = document.getElementById('hidden_price');
    if(hPrice) hPrice.value = fiyat;
    
    const radio = (tip === 'ucretsiz') ? document.getElementById('opt_ucretsiz') : document.getElementById('opt_ucretli');
    if(radio) radio.checked = true;
}

function kabulEt(talepId, telefon) {
    const telDiv = document.getElementById('tel-' + talepId);
    const btn = document.getElementById('btn-' + talepId);

    fetch('../Includes/talep_guncelle.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + talepId
    })
    .then(response => response.text())
    .then(data => {
        const res = data.trim(); // Boşlukları temizle

        if (res === "success") {
            // NUMARA SADECE BURADA GÖRÜNÜR OLABİLİR
            if(telDiv) {
                telDiv.innerText = "Yolcu Telefonu: " + telefon;
                telDiv.style.display = 'block';
            }
            if(btn) btn.style.display = 'none';
            alert("Yolculuk onaylandı!");
            location.reload();
        } 
        else if (res === "no_vehicle") {
            // ARAÇ YOKSA: Numara açılmaz, sadece alert çıkar
            alert("⚠️ Hata: Kiralık aracınız bulunamadı. Lütfen önce araç kiralayın!");
        } 
        else {
            alert("Bir hata oluştu.");
        }
    });
}
function bakiyeAksiyonu(bakiye) {
    if (bakiye < 0) {
        // Borç varsa banka hesaplarının olduğu sayfaya yönlendir
        window.location.href = 'bankalar.php'; 
    } else if (bakiye > 0) {
        // Bakiye varsa para çekme talebi sayfasına yönlendir
        window.location.href = 'para_cekme_talebi.php';
    } else {
        alert("İşlem yapılabilecek bir bakiyeniz bulunmamaktadır.");
    }
}
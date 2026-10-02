## 🚀 Projeyi Dış Dünyaya Açma (Canlıya Alma ve Kurulum)

Projenin altyapısında PHP kullanıldığı için (`login.php`, `signup.php`), sadece HTML dosyalarını açmak sistemin tam çalışması için yeterli olmayacaktır. Projeyi canlıya almak veya lokalde çalıştırmak için aşağıdaki adımları izlemelisiniz:

### 1. 🌐 Canlı Sunucuya Taşıma (Web Hosting)
Kullanıcıların dünyanın her yerinden sitenize girebilmesi için:

*   **Domain ve Hosting:** Bir Domain (Alan Adı) (örn: `urbanride.com`) ve bir Web Hosting (PHP ve MySQL destekli) satın alın.
*   **Panele Giriş:** Hosting sağlayıcınızın size sunduğu panele (cPanel, Plesk vb.) giriş yapın.
*   **Dosya Yükleme:** Dosya Yöneticisi (File Manager) üzerinden veya bir FTP programı (FileZilla gibi) aracılığıyla projenizdeki tüm dosyaları sunucunuzdaki `public_html` (veya `htdocs`) klasörünün içine yükleyin.
*   **Veritabanı Ayarları:** Eğer projenizde bir veritabanı varsa, cPanel üzerinden MySQL Veritabanları bölümünden veritabanınızı oluşturup içe aktarın ve PHP dosyalarınızdaki veritabanı bağlantı ayarlarını güncelleyin.

> 🎉 **Tebrikler!** Artık herkes web sitenizin adresini tarayıcısına yazarak UrbanRide'a erişebilir.

### 2. 💻 Geliştiriciler İçin Lokal Kurulum (Bilgisayarda Çalıştırma)
Projeyi kendi bilgisayarınızda geliştirmeye devam etmek veya test etmek isterseniz:

1.  Bilgisayarınıza **XAMPP**, **MAMP** veya **WAMP** gibi bir lokal sunucu yazılımı kurun.
2.  Bu repoyu bilgisayarınıza indirin veya klonlayın:
    ```bash
    git clone [https://github.com/emiryakut/urbanride.git](https://github.com/emiryakut/urbanride.git)
    ```
3.  İndirdiğiniz proje klasörünü XAMPP için `htdocs`, MAMP/WAMP için `www` klasörünün içine taşıyın.
4.  Lokal sunucunuzu (**Apache** ve **MySQL**) başlatın.
5.  Tarayıcınızda `http://localhost/urbanride/home.html` adresine giderek projeyi görüntüleyin.

---

## 📞 İletişim & Destek
Herhangi bir soru, öneri veya hata bildirimi için projenin **[Issues]** sekmesini kullanabilir veya proje yöneticisiyle iletişime geçebilirsiniz.

*© 2026 UrbanRide. Tüm hakları saklıdır.*

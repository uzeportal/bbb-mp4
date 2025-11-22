# PHP Mini CMS ve Tema Sistemi

Bu klasör, sayfa eklenebilen ve tema yüklenebilen basit bir PHP tabanlı CMS içerir. Yönetim paneli üzerinden sayfa ekleyebilir/silebilir, temayı değiştirebilir veya yeni bir temayı .zip olarak yükleyebilirsiniz.

## Kurulum
1. Depoyu indirin:
   - Git ile: `git clone <repo-url> && cd bbb-mp4/php-cms`
   - Ya da GitHub arayüzünden ZIP indirip açın, ardından `php-cms` dizinine girin.
2. `config.php` içindeki `admin_password` değerini değiştirin.
3. Web sunucusunu `index.php` (site) ve `admin.php` (panel) dosyalarına yönlendirin.
4. Hızlı önizleme için yerelde `php -S 0.0.0.0:8000 -t php-cms` komutunu çalıştırıp siteyi `http://localhost:8000/` adresinde açabilirsiniz.

## Varsayılan Temalar
- **base**: Minimal, beyaz ağırlıklı örnek tema.
- **law**: Zübeyir Kayahan Avukatlık Bürosu için hazırlanmış özel tema. Sticky header, yukarı çık butonu, harita iframe, tıklanabilir telefon/WhatsApp/e-posta bağlantıları ve iletişim kartı içerir.

## Yönetim Paneli
- Giriş: `admin.php` (varsayılan şifre `degistir`). Panelden şifreyi güncelleyebilirsiniz.
- Sayfa işlemleri: Başlık, slug, menüde gösterim, içerik ve meta (title/description/keywords) alanlarını ekleme/güncelleme/silme; düzenleme bağlantılarıyla mevcut sayfaları formda açabilirsiniz.
- Site & SEO ayarları: Site adı, slogan, adres, telefon, WhatsApp, e-posta, Google Maps embed URL, varsayılan meta başlık/açıklama/anahtar kelime ve temel URL değerlerini panelden kaydedin.
- Tema seçimi: Liste içinden seçip kaydedin.
- Tema yükleme: Kökünde tek bir klasör bulunan `.zip` dosyasını yükleyin; içerik `themes/` altına açılır.

## Varsayılan Sayfalar
`data/pages.json` içinde ana sayfa, hakkımızda, çalışma alanları ve iletişim sayfaları hazır gelir. İletişim sayfasında Google Maps iframe'i ve tıklanabilir telefon/WhatsApp/e-posta linkleri bulunur.

## Notlar
- `data/pages.json` dosyası yazılabilir olmalıdır.
- Tema yükleme için `ZipArchive` eklentisi gerekir.

## `hazir-cms` GitHub deposuna taşıma
Yalnızca bu PHP CMS içeriğini yeni bir GitHub deposuna (ör. `hazir-cms`) aktarmak için aşağıdaki adımları uygulayabilirsiniz:

1. Depo kökünden yeni bir klasöre sadece `php-cms` içeriğini kopyalayın:
   ```sh
   mkdir -p ../hazir-cms-export
   rsync -av --progress php-cms/ ../hazir-cms-export/
   ```
2. Yeni klasörde git deposu başlatın ve uzak bağlantıyı ekleyin:
   ```sh
   cd ../hazir-cms-export
   git init
   git add .
   git commit -m "Initial commit: PHP CMS"
   git remote add origin git@github.com:<kullanici>/hazir-cms.git
   # SSH yerine HTTPS kullanacaksanız:
   # git remote add origin https://github.com/<kullanici>/hazir-cms.git
   ```
3. İlk push ile GitHub’daki boş `hazir-cms` deposuna gönderin:
   ```sh
   git push -u origin main
   ```

`main` yerine `master` veya farklı bir varsayılan dal kullanıyorsanız push komutundaki dal adını değiştirin. Bu adımlar, mevcut projeyi temiz bir şekilde ayrı bir GitHub deposuna taşımanızı sağlar.

# Vepara Pure PHP Ödeme Sistemi

Bu proje, Vepara ödeme sisteminin saf PHP versiyonudur.

## Özellikler

- 2D ve 3D Secure ödeme desteği
- Token tabanlı kimlik doğrulama
- POS bilgisi sorgulama
- Session yönetimi
- Log sistemi
- Hata yönetimi
- Bootstrap ile responsive tasarım

## Dosya Yapısı

```
vepara_pure_php/
├── config.php                 # Konfigürasyon dosyası
├── autoload.php              # Otomatik sınıf yükleme
├── index.php                 # Ana ödeme formu
├── process_payment.php       # Ödeme işlemi
├── get_token.php            # Token alma API
├── get_pos.php              # POS bilgisi alma API
├── success.php              # 3D başarı callback
├── error.php                # 3D hata callback
├── success_page.php         # 3D başarı sayfası
├── success_2d.php           # 2D başarı sayfası
├── error_page.php           # Hata sayfası
├── error_2d.php             # 2D hata sayfası
├── helpers/                 # Yardımcı sınıflar
│   ├── CommonHelper.php
│   ├── SessionHelper.php
│   ├── LogHelper.php
│   ├── HttpHelper.php
│   └── HashGeneratorHelper.php
├── classes/                 # Ana sınıflar
│   ├── PaymentController.php
│   ├── PaymentRequest.php
│   ├── GetTokenRequest.php
│   └── GetPosRequest.php
└── logs/                    # Log dosyaları (otomatik oluşur)
```

## Kurulum

1. Dosyaları web sunucunuzun uygun dizinine kopyalayın
2. `config.php` dosyasındaki ayarları kendi bilgilerinizle güncelleyin:

   - `merchant_key`: Merchant anahtarınız
   - `app_id`: Uygulama ID'niz
   - `app_secret`: Uygulama gizli anahtarınız
   - `return_url` ve `cancel_url`: 3D callback URL'lerini güncelleyin

3. Web sunucunuzun `logs/` dizinine yazma yetkisi olduğundan emin olun

## Konfigürasyon

`config.php` dosyası güncellenmiştir:

```php
return [
    'merchant_key' => '$2y$10$s0XJ67CUdfjwruZaZuHTGO0vFqJa.9VcX8pQD6XIjYAejvxlxWPx2',
    'app_id' => '44c618310d916dbca1fcc327469b175d',
    'app_secret' => 'fa918fe712934fe81479e11085bae63b',
    'currency_code' => 'TRY',
    'return_url' => 'http://localhost/vepara_pure_php/success.php',
    'cancel_url' => 'http://localhost/vepara_pure_php/error.php',
    'base_url' => 'https://app.vepara.com.tr/ccpayment/api/', // Canlı ortam
    // ... diğer ayarlar
];
```

**Önemli:**

- `return_url` ve `cancel_url` adreslerini kendi domain'inizle güncelleyin
- Canlı ortama geçmek için `base_url`'i `https://app.vepara.com.tr/ccpayment/api/` olarak değiştirin

## Kullanım

### Ana Ödeme Formu

`index.php` sayfasını ziyaret ederek ödeme formunu kullanabilirsiniz.

### API Endpoints

#### Token Alma

```
GET/POST: get_token.php
```

#### POS Bilgisi Alma

```
POST: get_pos.php
Parametreler:
- credit_card: Kredi kartı numarası
- amount: Tutar
```

#### Ödeme İşlemi

```
POST: process_payment.php
Parametreler:
- name: Ad Soyad
- total: Tutar
- cc_holder_name: Kart sahibi adı
- cc_no: Kart numarası
- expiry_month: Son kullanma ayı
- expiry_year: Son kullanma yılı
- cvv: CVV kodu
- installments_number: Taksit sayısı
- 3d_checkbox: 3D Secure seçimi (opsiyonel)
```

## Güvenlik

- Tüm form verileri temizlenir ve doğrulanır
- XSS koruması için `htmlspecialchars()` kullanılır
- CSRF koruması için session tabanlı kontroller yapılır
- Hassas bilgiler loglanmaz

## Log Sistemi

Sistem otomatik olarak `logs/payment.log` dosyasına log yazar:

- INFO: Başarılı işlemler
- ERROR: Hata durumları
- DEBUG: Geliştirme bilgileri

## Hata Yönetimi

- Tüm hatalar yakalanır ve loglanır
- Kullanıcı dostu hata mesajları gösterilir
- Sistem hataları gizlenir, genel mesajlar gösterilir

## Gereksinimler

- PHP 7.4 veya üzeri
- cURL extension
- OpenSSL extension
- Session desteği

## Test

Test ortamında kullanım için:

- Test kartları kullanın
- `base_url`'i test ortamına ayarlayın
- Log seviyesini DEBUG'a ayarlayın

## Üretim Ortamı

Üretim ortamında:

- `display_errors`'ı kapatın
- `base_url`'i canlı ortama ayarlayın
- Log dosyalarını düzenli olarak temizleyin
- HTTPS kullanın

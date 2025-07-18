<?php
require_once 'autoload.php';

// Hata raporlamayı aç (geliştirme için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

SessionHelper::start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vepara Ödeme Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .payment-form {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .card-input {
            font-family: monospace;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-form">
            <h2 class="text-center mb-4">Ödeme Formu</h2>
            
            <?php
            // Hata mesajlarını göster
            if (SessionHelper::has('error_message')) {
                echo '<div class="alert alert-danger">' . SessionHelper::get('error_message') . '</div>';
                SessionHelper::remove('error_message');
            }
            
            // Form verilerini geri yükle
            $formData = SessionHelper::get('form_data', []);
            SessionHelper::remove('form_data');
            ?>
            
            <form id="paymentForm" method="POST" action="process_payment.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Ad Soyad *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tckn" class="form-label">TC Kimlik No</label>
                            <input type="text" class="form-control" id="tckn" name="tckn" maxlength="11">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total" class="form-label">Tutar (TL) *</label>
                            <input type="number" class="form-control" id="total" name="total" step="0.01" min="0.01" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Açıklama</label>
                    <input type="text" class="form-control" id="description" name="description" value="Ödeme açıklaması">
                </div>
                
                <hr>
                <h5>Kart Bilgileri</h5>
                
                <div class="form-group">
                    <label for="cc_holder_name" class="form-label">Kart Sahibi Adı *</label>
                    <input type="text" class="form-control" id="cc_holder_name" name="cc_holder_name" required>
                </div>
                
                <div class="form-group">
                    <label for="cc_no" class="form-label">Kart Numarası *</label>
                    <input type="text" class="form-control card-input" id="cc_no" name="cc_no" maxlength="19" placeholder="0000 0000 0000 0000" required>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="expiry_month" class="form-label">Ay *</label>
                            <select class="form-control" id="expiry_month" name="expiry_month" required>
                                <option value="">Seçiniz</option>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="expiry_year" class="form-label">Yıl *</label>
                            <select class="form-control" id="expiry_year" name="expiry_year" required>
                                <option value="">Seçiniz</option>
                                <?php for($i = date('Y'); $i <= date('Y') + 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cvv" class="form-label">CVV *</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" maxlength="4" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="installments_number" class="form-label">Taksit Sayısı</label>
                    <select class="form-control" id="installments_number" name="installments_number">
                        <option value="1">Peşin</option>
                        <option value="2">2 Taksit</option>
                        <option value="3">3 Taksit</option>
                        <option value="6">6 Taksit</option>
                        <option value="9">9 Taksit</option>
                        <option value="12">12 Taksit</option>
                    </select>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="3d_checkbox" name="3d_checkbox">
                    <label class="form-check-label" for="3d_checkbox">
                        3D Secure ile öde
                    </label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Ödemeyi Tamamla</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Kart numarası formatla
        document.getElementById('cc_no').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            if (formattedValue !== e.target.value) {
                e.target.value = formattedValue;
            }
        });
        
        // Sadece rakam girişi için
        document.getElementById('tckn').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
        
        document.getElementById('cvv').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #f3f4f6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            opacity: 0.1;
        }

        .container {
            position: relative;
            z-index: 1;
        }

        .payment-form {
            max-width: 800px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .form-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .form-header h2 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .form-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
            position: relative;
            z-index: 1;
        }

        .form-body {
            padding: 40px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-label .required {
            color: var(--danger-color);
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #fafafa;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background-color: white;
        }

        .card-input {
            font-family: 'Courier New', monospace;
            letter-spacing: 3px;
            font-size: 18px;
            font-weight: 500;
        }

        .card-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            min-height: 200px;
            box-shadow: var(--shadow-lg);
        }

        .card-preview::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .card-chip {
            width: 50px;
            height: 40px;
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .card-number-preview {
            font-size: 24px;
            letter-spacing: 4px;
            margin-bottom: 20px;
            font-family: 'Courier New', monospace;
            position: relative;
            z-index: 1;
        }

        .card-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            position: relative;
            z-index: 1;
        }

        .card-holder-preview {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-expiry-preview {
            font-size: 14px;
        }

        .installment-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .installment-option {
            position: relative;
            cursor: pointer;
        }

        .installment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .installment-option label {
            display: block;
            padding: 12px;
            text-align: center;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 500;
            background: white;
        }

        .installment-option input[type="radio"]:checked + label {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .secure-badge {
            background: var(--success-color);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-check {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .form-check:hover {
            border-color: var(--primary-color);
            background: #f3f4ff;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            margin-top: 0;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: 16px 32px;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: #fee;
            color: var(--danger-color);
        }

        .security-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }

        .security-icons img {
            height: 30px;
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }

        .security-icons img:hover {
            opacity: 1;
        }

        /* Loading animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-header {
                padding: 30px 20px;
            }
            
            .form-body {
                padding: 30px 20px;
            }
            
            .form-header h2 {
                font-size: 24px;
            }
            
            .card-preview {
                padding: 20px;
                min-height: 180px;
            }
            
            .card-number-preview {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container">
        <div class="payment-form">
            <div class="form-header">
                <h2><i class="fas fa-lock"></i> Güvenli Ödeme</h2>
                <p>256-bit SSL şifreleme ile korunmaktadır</p>
            </div>
            
            <div class="form-body">
                <?php
                // Hata mesajlarını göster
                if (SessionHelper::has('error_message')) {
                                     echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>' . SessionHelper::get('error_message') . '</div>';
                    SessionHelper::remove('error_message');
                }
                // Form verilerini geri yükle
                $formData = SessionHelper::get('form_data', []);
                SessionHelper::remove('form_data');
                ?>

                <div class="secure-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>PCI DSS Uyumlu Güvenli Ödeme</span>
                </div>

                <form id="paymentForm" method="POST" action="process_payment.php">
                    <!-- Müşteri Bilgileri -->
                    <div class="section-title">
                        <i class="fas fa-user"></i>
                        Müşteri Bilgileri
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    Ad Soyad <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       placeholder="Örn: Ahmet Yılmaz">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i> Telefon
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="5XX XXX XX XX">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tckn" class="form-label">
                                    <i class="fas fa-id-card"></i> TC Kimlik No
                                </label>
                                <input type="text" class="form-control" id="tckn" name="tckn" 
                                       maxlength="11" placeholder="Opsiyonel">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Tutar (TL) <span class="required">*</span>
                                </label>
                                <input type="number" class="form-control" id="total" name="total" 
                                       step="0.01" min="0.01" required placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">
                            <i class="fas fa-comment"></i> Açıklama
                        </label>
                        <input type="text" class="form-control" id="description" name="description" 
                               value="Ödeme açıklaması" placeholder="Ödeme ile ilgili not">
                    </div>

                    <!-- Kart Önizleme -->
                    <div class="section-title mt-5">
                        <i class="fas fa-credit-card"></i>
                        Kart Bilgileri
                    </div>

                    <div class="card-preview">
                        <div class="card-chip"></div>
                        <div class="card-number-preview" id="cardNumberPreview">•••• •••• •••• ••••</div>
                        <div class="card-info">
                            <div>
                                <div style="font-size: 10px; opacity: 0.8;">KART SAHİBİ</div>
                                <div class="card-holder-preview" id="cardHolderPreview">AD SOYAD</div>
                            </div>
                            <div>
                                <div style="font-size: 10px; opacity: 0.8;">SON KULLANMA</div>
                                <div class="card-expiry-preview" id="cardExpiryPreview">AA/YY</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cc_holder_name" class="form-label">
                            Kart Sahibi Adı <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="cc_holder_name" name="cc_holder_name" 
                               required placeholder="Kart üzerindeki isim">
                    </div>

                    <div class="form-group">
                        <label for="cc_no" class="form-label">
                            Kart Numarası <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control card-input" id="cc_no" name="cc_no" 
                               maxlength="19" placeholder="0000 0000 0000 0000" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expiry_month" class="form-label">
                                    Ay <span class="required">*</span>
                                </label>
                                <select class="form-select" id="expiry_month" name="expiry_month" required>
                                    <option value="">Seçiniz</option>
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expiry_year" class="form-label">
                                    Yıl <span class="required">*</span>
                                </label>
                                <select class="form-select" id="expiry_year" name="expiry_year" required>
                                    <option value="">Seçiniz</option>
                                    <?php for($i = date('Y'); $i <= date('Y') + 10; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cvv" class="form-label">
                                    CVV <span class="required">*</span>
                                    <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" 
                                       title="Kartınızın arkasındaki 3 haneli güvenlik kodu"></i>
                                </label>
                                <input type="text" class="form-control" id="cvv" name="cvv" 
                                       maxlength="4" required placeholder="•••">
                            </div>
                        </div>
                    </div>

                    <!-- Taksit Seçenekleri -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i> Taksit Seçenekleri
                        </label>
                        <div class="installment-options">
                            <div class="installment-option">
                                <input type="radio" id="inst1" name="installments_number" value="1" checked>
                                <label for="inst1">Peşin</label>
                            </div>
                            <div class="installment-option">
                                <input type="radio" id="inst2" name="installments_number" value="2">
                                <label for="inst2">2 Taksit</label>
                            </div>
                            <div class="installment-option">
                                <input type="radio" id="inst3" name="installments_number" value="3">
                                <label for="inst3">3 Taksit</label>
                            </div>
                            <div class="installment-option">
                                <input type="radio" id="inst6" name="installments_number" value="6">
                                <label for="inst6">6 Taksit</label>
                            </div>
                            <div class="installment-option">
                                <input type="radio" id="inst9" name="installments_number" value="9">
                                <label for="inst9">9 Taksit</label>
                            </div>
                            <div class="installment-option">
                                <input type="radio" id="inst12" name="installments_number" value="12">
                                <label for="inst12">12 Taksit</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="3d_checkbox" name="3d_checkbox" checked>
                        <label class="form-check-label" for="3d_checkbox">
                            <strong>3D Secure ile güvenli ödeme</strong>
                            <br>
                            <small class="text-muted">Bankanızın güvenlik sayfasına yönlendirileceksiniz</small>
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-lock"></i> Ödemeyi Tamamla
                        </button>
                    </div>

                    <div class="security-icons">
                        <i class="fa-brands fa-cc-visa fa-2x text-muted"></i>
                        <i class="fa-brands fa-cc-mastercard fa-2x text-muted"></i>
                        <i class="fab fa-cc-amex fa-2x text-muted"></i>
                        <i class="fas fa-shield-alt fa-2x text-muted"></i>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tooltip'leri aktif et
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Kart numarası formatla ve önizleme
        document.getElementById('cc_no').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            if (formattedValue !== e.target.value) {
                e.target.value = formattedValue;
            }
            
            // Kart önizlemesini güncelle
            let preview = value.padEnd(16, '•');
            let previewFormatted = preview.match(/.{1,4}/g)?.join(' ') || preview;
            document.getElementById('cardNumberPreview').textContent = previewFormatted;
        });

        // Kart sahibi adı önizleme
        document.getElementById('cc_holder_name').addEventListener('input', function (e) {
            let value = e.target.value.toUpperCase() || 'AD SOYAD';
            document.getElementById('cardHolderPreview').textContent = value;
        });

        // Son kullanma tarihi önizleme
        function updateExpiryPreview() {
            let month = document.getElementById('expiry_month').value || 'AA';
            let year = document.getElementById('expiry_year').value || 'YY';
            if (year !== 'YY') {
                year = year.substring(2);
            }
            document.getElementById('cardExpiryPreview').textContent = month + '/' + year;
        }

        document.getElementById('expiry_month').addEventListener('change', updateExpiryPreview);
        document.getElementById('expiry_year').addEventListener('change', updateExpiryPreview);

        // Sadece rakam girişi için
        document.getElementById('tckn').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('cvv').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // Form gönderildiğinde loading göster
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            document.getElementById('loadingOverlay').style.display = 'flex';
        });

        // Tutar alanına odaklanınca placeholder'ı temizle
        document.getElementById('total').addEventListener('focus', function() {
            if (this.value === '') {
                this.placeholder = '';
            }
        });

        document.getElementById('total').addEventListener('blur', function() {
            if (this.value === '') {
                this.placeholder = '0.00';
            }
        });
    </script>
</body>
</html>

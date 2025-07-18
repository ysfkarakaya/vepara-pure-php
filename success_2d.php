<?php
require_once 'autoload.php';

// 2D ödeme başarı sayfası
$data2d = SessionHelper::get('data2d');
if (!$data2d) {
    CommonHelper::redirect('index.php');
}

$data = $data2d['data'] ?? $data2d;

// Sayfa ziyaret kontrolü
if (SessionHelper::get('is_visit') === false) {
    SessionHelper::set('is_visit', true);
} else {
    CommonHelper::redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Başarılı - Vepara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .info-table {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                ✓
            </div>
            <h2 class="text-success mb-3">2D Ödeme Başarılı!</h2>
            <p class="lead">İşleminiz başarıyla tamamlanmıştır.</p>
            
            <?php if ($data): ?>
            <div class="info-table">
                <table class="table table-bordered">
                    <tbody>
                        <?php if (!empty($data['order_no'])): ?>
                        <tr>
                            <td><strong>Sipariş No:</strong></td>
                            <td><?= htmlspecialchars($data['order_no']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['invoice_id'])): ?>
                        <tr>
                            <td><strong>Fatura ID:</strong></td>
                            <td><?= htmlspecialchars($data['invoice_id']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['status_description'])): ?>
                        <tr>
                            <td><strong>Durum:</strong></td>
                            <td><?= htmlspecialchars($data['status_description']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['credit_card_no'])): ?>
                        <tr>
                            <td><strong>Kart No:</strong></td>
                            <td><?= htmlspecialchars($data['credit_card_no']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['auth_code'])): ?>
                        <tr>
                            <td><strong>Yetkilendirme Kodu:</strong></td>
                            <td><?= htmlspecialchars($data['auth_code']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['payment_method'])): ?>
                        <tr>
                            <td><strong>Ödeme Yöntemi:</strong></td>
                            <td>2D Secure</td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['total'])): ?>
                        <tr>
                            <td><strong>Tutar:</strong></td>
                            <td><?= number_format($data['total'], 2) ?> TL</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="index.php" class="btn btn-primary">Yeni Ödeme</a>
            </div>
            
            <!-- E-posta gönderme formu -->
            <div class="mt-4">
                <h5>Makbuz E-posta ile Gönder</h5>
                <form method="POST" action="send_email.php" class="d-flex justify-content-center">
                    <div class="input-group" style="max-width: 300px;">
                        <input type="email" class="form-control" name="email" placeholder="E-posta adresiniz" required>
                        <button type="submit" class="btn btn-outline-secondary">Gönder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

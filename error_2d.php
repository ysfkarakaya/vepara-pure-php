<?php
require_once 'autoload.php';

// 2D ödeme hata sayfası
$data = SessionHelper::get('data2d');
if (!$data) {
    CommonHelper::redirect('index.php');
}

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
    <title>2D Ödeme Hatası - Vepara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .info-table {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                ✗
            </div>
            <h2 class="text-danger mb-3">2D Ödeme Başarısız!</h2>
            <p class="lead">İşleminiz tamamlanamadı.</p>
            
            <?php if ($data): ?>
            <div class="info-table">
                <table class="table table-bordered">
                    <tbody>
                        <?php if (!empty($data['status_code'])): ?>
                        <tr>
                            <td><strong>Durum Kodu:</strong></td>
                            <td><?= htmlspecialchars($data['status_code']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['status_description'])): ?>
                        <tr>
                            <td><strong>Durum Açıklaması:</strong></td>
                            <td><?= htmlspecialchars($data['status_description']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['error_code'])): ?>
                        <tr>
                            <td><strong>Hata Kodu:</strong></td>
                            <td><?= htmlspecialchars($data['error_code']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['error_description'])): ?>
                        <tr>
                            <td><strong>Hata Açıklaması:</strong></td>
                            <td><?= htmlspecialchars($data['error_description']) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <td><strong>Ödeme Yöntemi:</strong></td>
                            <td>2D Secure</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="index.php" class="btn btn-primary">Tekrar Dene</a>
            </div>
            
            <div class="mt-3">
                <small class="text-muted">
                    Sorun devam ederse lütfen müşteri hizmetleri ile iletişime geçiniz.
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once 'autoload.php';

// Hata raporlamayı aç (geliştirme için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    CommonHelper::jsonResponse(['message' => 'Sadece POST istekleri kabul edilir.'], 405);
}

try {
    // PaymentController'ı başlat
    $paymentController = new PaymentController();
    
    // Form verilerini al
    $data = [
        'credit_card' => CommonHelper::getPost('credit_card', ''),
        'amount' => CommonHelper::getPost('amount', '')
    ];
    
    // Temel validasyon
    if (empty($data['credit_card'])) {
        CommonHelper::jsonResponse(['message' => 'Kredi kartı numarası gereklidir.'], 400);
    }
    
    if (empty($data['amount']) || (float)$data['amount'] <= 0) {
        CommonHelper::jsonResponse(['message' => 'Geçerli bir tutar giriniz.'], 400);
    }
    
    // POS bilgilerini al
    $result = $paymentController->getPos($data);
    
    CommonHelper::jsonResponse($result);
    
} catch (Exception $e) {
    LogHelper::error('Get POS işlemi sırasında beklenmeyen hata', ['Error' => $e->getMessage()]);
    CommonHelper::jsonResponse(['message' => 'Beklenmeyen bir hata oluştu.'], 500);
}

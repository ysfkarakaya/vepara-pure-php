<?php
require_once 'autoload.php';

// Hata raporlamayı aç (geliştirme için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // PaymentController'ı başlat
    $paymentController = new PaymentController();
    
    // Token al
    $result = $paymentController->getToken();
    
    CommonHelper::jsonResponse($result);
    
} catch (Exception $e) {
    LogHelper::error('Token alma işlemi sırasında beklenmeyen hata', ['Error' => $e->getMessage()]);
    CommonHelper::jsonResponse(['message' => 'Beklenmeyen bir hata oluştu.'], 500);
}

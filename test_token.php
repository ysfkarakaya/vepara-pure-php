<?php
require_once 'autoload.php';

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session'ı temizle
SessionHelper::clear();
SessionHelper::start();

echo "<h2>Token Test</h2>";

try {
    $paymentController = new PaymentController();
    
    echo "<h3>Konfigürasyon:</h3>";
    $config = include 'config.php';
    echo "<pre>";
    echo "Base URL: " . $config['base_url'] . "\n";
    echo "App ID: " . $config['app_id'] . "\n";
    echo "App Secret: " . substr($config['app_secret'], 0, 8) . "...\n";
    echo "Merchant Key: " . substr($config['merchant_key'], 0, 20) . "...\n";
    echo "</pre>";
    
    echo "<h3>Token İsteği:</h3>";
    $result = $paymentController->getToken();
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h3>Hata:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

echo "<h3>Log Dosyası:</h3>";
if (file_exists('logs/payment.log')) {
    echo "<pre>" . file_get_contents('logs/payment.log') . "</pre>";
} else {
    echo "Log dosyası bulunamadı.";
}
?>

<?php
// Konfigürasyon dosyası
return [
    'merchant_key' => 'x',
    'app_id' => 'x',
    'app_secret' => 'x',
    'currency_code' => 'TRY',
    'invoice_description' => 'INVOICE_DESCRIPTION',
    'return_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . '/vepara/vepara_callback.php',
    'cancel_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . '/vepara/vepara_error.php',
    'is_2d' => 0,
    'base_url' => 'https://app.vepara.com.tr/ccpayment/api/',
    
    // Session ayarları
    'session_name' => 'VEPARA_SESSION',
    'session_lifetime' => 3600, // 1 saat
    
    // Log ayarları
    'log_file' => __DIR__ . '/logs/payment.log',
    'enable_logging' => true
];
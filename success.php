<?php
require_once 'autoload.php';

// 3D Secure başarı callback'i
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
    $requestData = [
        'order_no' => CommonHelper::getGet('order_no', ''),
        'order_id' => CommonHelper::getGet('order_id', ''),
        'invoice_id' => CommonHelper::getGet('invoice_id', ''),
        'status_code' => CommonHelper::getGet('status_code', ''),
        'status_description' => CommonHelper::getGet('status_description', ''),
        'credit_card_no' => CommonHelper::getGet('credit_card_no', ''),
        'transaction_type' => CommonHelper::getGet('transaction_type', ''),
        'payment_status' => CommonHelper::getGet('payment_status', ''),
        'payment_method' => CommonHelper::getGet('payment_method', ''),
        'error_code' => CommonHelper::getGet('error_code', ''),
        'error' => CommonHelper::getGet('error', ''),
        'auth_code' => CommonHelper::getGet('auth_code', ''),
        'hash_key' => CommonHelper::getGet('hash_key', ''),
        'original_bank_error_code' => '',
        'original_bank_error_description' => ''
    ];
    
    SessionHelper::set('data3d', $requestData);
    LogHelper::info('3D Başarı Sayfası İstek Verileri', $requestData);
    
    // 3D Secure başarılı ödeme - randevu callback'ine yönlendir
    $_POST['status'] = 'success';
    $_POST['payment_id'] = $requestData['order_id'];
    $_POST['transaction_id'] = $requestData['order_no'];
    $_POST['auth_code'] = $requestData['auth_code'];
    include '../vepara/vepara_callback.php';
    exit();
}

// Direkt erişim engelle
CommonHelper::redirect('index.php');
<?php
require_once 'autoload.php';

// Hata raporlamayı aç (geliştirme için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug için POST verilerini logla
LogHelper::info('POST verileri alındı', $_POST);

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    LogHelper::error('POST olmayan istek geldi', ['method' => $_SERVER['REQUEST_METHOD']]);
    CommonHelper::redirect('index.php');
}

// POST verilerinin gelip gelmediğini kontrol et
if (empty($_POST)) {
    LogHelper::error('POST verileri boş');
    SessionHelper::set('error_message', 'Form verileri alınamadı. Lütfen tekrar deneyiniz.');
    CommonHelper::redirect('index.php');
}

try {
    // PaymentController'ı başlat
    $paymentController = new PaymentController();
    
    // Token al
    $tokenResponse = $paymentController->getToken();
    
    if (isset($tokenResponse['message'])) {
        // Token alınamadı
        SessionHelper::set('error_message', $tokenResponse['message']);
        CommonHelper::redirect('error_page.php');
    }
    
    // Form verilerini al ve temizle
    $data = [
        'name' => trim(CommonHelper::getPost('name', '')),
        'phone' => trim(CommonHelper::getPost('phone', '')),
        'tckn' => trim(CommonHelper::getPost('tckn', '')),
        'total' => (float)CommonHelper::getPost('total', 0),
        'description' => trim(CommonHelper::getPost('description', 'Ödeme açıklaması')),
        'cc_holder_name' => trim(CommonHelper::getPost('cc_holder_name', '')),
        'cc_no' => str_replace(' ', '', CommonHelper::getPost('cc_no', '')),
        'expiry_month' => (int)CommonHelper::getPost('expiry_month', 0),
        'expiry_year' => (int)CommonHelper::getPost('expiry_year', 0),
        'cvv' => (int)CommonHelper::getPost('cvv', 0),
        'installments_number' => (int)CommonHelper::getPost('installments_number', 1),
        '3d_checkbox' => CommonHelper::getPost('3d_checkbox') !== null
    ];
    
    // Temel validasyon
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Ad Soyad alanı zorunludur.';
    }
    
    if ($data['total'] <= 0) {
        $errors[] = 'Geçerli bir tutar giriniz.';
    }
    
    if (empty($data['cc_holder_name'])) {
        $errors[] = 'Kart sahibi adı zorunludur.';
    }
    
    if (empty($data['cc_no']) || strlen($data['cc_no']) < 13) {
        $errors[] = 'Geçerli bir kart numarası giriniz.';
    }
    
    if ($data['expiry_month'] < 1 || $data['expiry_month'] > 12) {
        $errors[] = 'Geçerli bir ay seçiniz.';
    }
    
    if ($data['expiry_year'] < date('Y')) {
        $errors[] = 'Geçerli bir yıl seçiniz.';
    }
    
    if ($data['cvv'] < 100 || $data['cvv'] > 9999) {
        $errors[] = 'Geçerli bir CVV giriniz.';
    }
    
    if (!empty($errors)) {
        SessionHelper::set('error_message', implode('<br>', $errors));
        SessionHelper::set('form_data', $data);
        CommonHelper::redirect('index.php');
    }
    
    // Ödeme işlemini başlat
    $result = $paymentController->processPayment($data);
    
    if (is_array($result)) {
        if (isset($result['success'])) {
            // 2D ödeme sonucu
            if ($result['success']) {
                // Başarılı ödeme - randevu callback'ine yönlendir
                $_POST['status'] = 'success';
                $_POST['payment_id'] = $result['payment_id'] ?? '';
                $_POST['transaction_id'] = $result['transaction_id'] ?? '';
                include '../vepara/vepara_callback.php';
                exit();
            } else {
                // Başarısız ödeme - randevu callback'ine yönlendir
                $_POST['status'] = 'failed';
                $_POST['error_message'] = $result['message'] ?? 'Ödeme başarısız';
                $_POST['error_code'] = $result['error_code'] ?? '';
                $_POST['error_description'] = $result['error_description'] ?? '';
                $_POST['bank_error_code'] = $result['bank_error_code'] ?? '';
                $_POST['bank_error_description'] = $result['bank_error_description'] ?? '';
                include '../vepara/vepara_callback.php';
                exit();
            }
        } elseif (isset($result['message'])) {
            // Hata mesajı
            $_POST['status'] = 'failed';
            $_POST['error_message'] = $result['message'];
            include '../vepara/vepara_callback.php';
            exit();
        }
    } else {
        // 3D ödeme - HTML yanıtı
        echo $result;
    }
    
} catch (Exception $e) {
    LogHelper::error('Ödeme işlemi sırasında beklenmeyen hata', ['Error' => $e->getMessage()]);
    SessionHelper::set('error_message', 'Beklenmeyen bir hata oluştu. Lütfen tekrar deneyiniz.');
    CommonHelper::redirect('error_page.php');
}
<?php

class PaymentController
{
    private $config;
    
    public function __construct()
    {
        $this->config = include __DIR__ . '/../config.php';
        SessionHelper::start();
    }
    
    /**
     * Token alır
     * @return array
     */
    public function getToken()
    {
        $apiUrl = $this->config['base_url'] . 'token';
        
        // Eğer session'da token varsa onu döndür
        if (SessionHelper::has('token')) {
            return ['token' => SessionHelper::get('token')];
        }
        
        $getTokenRequest = new GetTokenRequest();
        $getTokenRequest->setAppId($this->config['app_id']);
        $getTokenRequest->setAppSecret($this->config['app_secret']);
        
        $body = $getTokenRequest->getTokenData();
        
        // Debug için gönderilen verileri logla
        LogHelper::info('Token isteği gönderiliyor', [
            'url' => $apiUrl,
            'app_id' => $this->config['app_id'],
            'app_secret' => substr($this->config['app_secret'], 0, 8) . '...',
            'body' => $body
        ]);
        
        try {
            $response = HttpHelper::post($apiUrl, $body);
            
            // Tam yanıtı logla
            LogHelper::info('Token yanıtı alındı', [
                'status_code' => $response['status_code'],
                'response_data' => $response['data']
            ]);
            
            if ($response['status_code'] === 200 && isset($response['data']['status_code']) && $response['data']['status_code'] === 100) {
                $token = $response['data']['data']['token'];
                SessionHelper::set('token', $token);
                SessionHelper::save();
                
                LogHelper::info('Token oluşturuldu', ['Token' => $token]);
                
                return $response['data'];
            } else {
                $statusCode = $response['data']['status_code'] ?? $response['status_code'];
                $statusDescription = $response['data']['status_description'] ?? 'Bilinmeyen hata';
                LogHelper::error('Token oluşturulurken hata oluştu', [
                    'Error code' => $statusCode,
                    'Error description' => $statusDescription,
                    'Full response' => $response
                ]);
                return ['message' => 'Token oluşturulurken hata oluştu. Hata kodu: ' . $statusCode . ' - ' . $statusDescription];
            }
        } catch (Exception $e) {
            LogHelper::error('Token oluşturulurken hata oluştu', ['Error' => $e->getMessage()]);
            return ['message' => 'Token oluşturulurken hata oluştu: ' . $e->getMessage()];
        }
    }
    
    /**
     * 3D ödeme işlemi
     * @param array $data
     * @return array|string
     */
    public function processPayment3d($data)
    {
        $apiUrl = $this->config['base_url'] . 'paySmart3D';
        
        $paymentRequest = new PaymentRequest();
        $this->fillPaymentRequest($paymentRequest, $data);
        
        // 3D için ek alanlar
        $paymentRequest->setReturnUrl($this->config['return_url']);
        $paymentRequest->setCancelUrl($this->config['cancel_url']);
        
        // Items ekle
        $items = $this->getItemRequestData($data);
        $paymentRequest->setItems($items);
        
        $body = $paymentRequest->toJson();
        
        try {
            $headers = [
                'Authorization: Bearer ' . SessionHelper::get('token')
            ];
            
            $response = HttpHelper::post($apiUrl, $body, $headers);
            
            if ($response['status_code'] === 200) {
                LogHelper::info('3D ödeme başarılı');
                return $response['body'];
            } else {
                $errorMessage = 'Ödeme işlemi başarısız. HTTP Status: ' . $response['status_code'];
                if (isset($response['data'])) {
                    $errorCode = $response['data']['status_code'] ?? 'unknown';
                    $errorDescription = $response['data']['status_description'] ?? '';
                    $errorMessage .= " - Hata Kodu: {$errorCode}";
                    if ($errorDescription) {
                        $errorMessage .= " - {$errorDescription}";
                    }
                }
                
                LogHelper::error('3D ödeme başarısız', [
                    'http_status' => $response['status_code'],
                    'response_data' => $response['data'] ?? null,
                    'error_message' => $errorMessage
                ]);
                
                return ['message' => $errorMessage];
            }
        } catch (Exception $e) {
            LogHelper::error('Ödeme işlemi sırasında hata oluştu', ['Error' => $e->getMessage()]);
            return ['message' => 'Ödeme işlemi sırasında hata oluştu: ' . $e->getMessage()];
        }
    }
    
    /**
     * 2D ödeme işlemi
     * @param array $data
     * @return array
     */
    public function processPayment2d($data)
    {
        $apiUrl = $this->config['base_url'] . 'paySmart2D';
        
        $paymentRequest = new PaymentRequest();
        $this->fillPaymentRequest($paymentRequest, $data);
        
        // Items ekle
        $items = $this->getItemRequestData($data);
        $paymentRequest->setItems($items);
        
        $body = $paymentRequest->toJson();
        
        try {
            $headers = [
                'Authorization: Bearer ' . SessionHelper::get('token')
            ];
            
            $response = HttpHelper::post($apiUrl, $body, $headers);
            
            SessionHelper::set('data2d', $response['data']);
            
            if (isset($response['data']['status_code']) && $response['data']['status_code'] === 100) {
                LogHelper::info('2D ödeme başarılı', $response['data']);
                return [
                    'success' => true, 
                    'redirect' => 'success_2d.php',
                    'payment_id' => $response['data']['order_id'] ?? '',
                    'transaction_id' => $response['data']['order_no'] ?? '',
                    'data' => $response['data']
                ];
            } else {
                $errorCode = $response['data']['status_code'] ?? 'unknown';
                $errorMessage = $response['data']['status_description'] ?? 'Bilinmeyen hata';
                $bankErrorCode = $response['data']['original_bank_error_code'] ?? '';
                $bankErrorMessage = $response['data']['original_bank_error_description'] ?? '';
                
                LogHelper::error('2D ödeme başarısız', [
                    'status_code' => $errorCode,
                    'status_description' => $errorMessage,
                    'bank_error_code' => $bankErrorCode,
                    'bank_error_message' => $bankErrorMessage,
                    'full_response' => $response['data']
                ]);
                
                $detailedMessage = "Ödeme başarısız. Hata Kodu: {$errorCode}";
                if ($errorMessage) {
                    $detailedMessage .= " - {$errorMessage}";
                }
                if ($bankErrorCode && $bankErrorMessage) {
                    $detailedMessage .= " (Banka Hatası: {$bankErrorCode} - {$bankErrorMessage})";
                }
                
                return [
                    'success' => false, 
                    'redirect' => 'error_2d.php',
                    'message' => $detailedMessage,
                    'error_code' => $errorCode,
                    'error_description' => $errorMessage,
                    'bank_error_code' => $bankErrorCode,
                    'bank_error_description' => $bankErrorMessage,
                    'data' => $response['data']
                ];
            }
        } catch (Exception $e) {
            LogHelper::error('Ödeme işlemi sırasında hata oluştu', ['Error' => $e->getMessage()]);
            return ['message' => 'Ödeme işlemi sırasında hata oluştu: ' . $e->getMessage()];
        }
    }
    
    /**
     * POS bilgilerini alır
     * @param array $data
     * @return array
     */
    public function getPos($data)
    {
        $this->getToken();
        $tokenValue = SessionHelper::get('token');
        $apiUrl = $this->config['base_url'] . 'getpos';
        
        $posRequest = new GetPosRequest();
        $posRequest->setCreditCard($data['credit_card']);
        $posRequest->setAmount($data['amount']);
        $posRequest->setCurrencyCode($this->config['currency_code']);
        $posRequest->setIs2d($this->config['is_2d']);
        $posRequest->setMerchantKey($this->config['merchant_key']);
        
        $body = $posRequest->getData();
        
        try {
            $headers = [
                'Authorization: Bearer ' . $tokenValue
            ];
            
            $response = HttpHelper::post($apiUrl, $body, $headers);
            
            if (isset($response['data']['status_code']) && $response['data']['status_code'] === 100) {
                LogHelper::info('Get Pos başarılı', $response['data']);
                return $response['data'];
            } else {
                LogHelper::error('Get Pos işlemi başarısız');
                return ['message' => 'Get Pos işlemi başarısız.'];
            }
        } catch (Exception $e) {
            LogHelper::error('İşlem sırasında hata oluştu', ['Error' => $e->getMessage()]);
            return ['message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage()];
        }
    }
    
    /**
     * Ana ödeme işlemi
     * @param array $data
     * @return array|string
     */
    public function processPayment($data)
    {
        SessionHelper::set('is_visit', false);
        
        $amount = $data['amount'] ?? 0;
        $name = $data['name'] ?? '';
        $phone = $data['phone'] ?? '';
        $tckn = $data['tckn'] ?? '';
        $ccHolderName = $data['cc_holder_name'] ?? '';
        $ccNo = $data['cc_no'] ?? '';
        $expiryMonth = $data['expiry_month'] ?? '';
        $expiryYear = $data['expiry_year'] ?? '';
        $cvv = $data['cvv'] ?? '';
        $installmentNumbers = $data['installments_number'] ?? 1;
        
        LogHelper::info('Ödeme İsteği Verileri', [
            'Amount' => $amount,
            'Phone Number' => $phone,
            'Name' => $name,
            'Identity Number' => $tckn,
            'Cc Holder Name' => $ccHolderName,
            'Card Number' => $ccNo,
            'Expiry Month' => $expiryMonth,
            'Expiry Year' => $expiryYear,
            'CVV' => $cvv,
            'Installments Number' => $installmentNumbers
        ]);
        
        $is3D = isset($data['3d_checkbox']);
        SessionHelper::set('email_sent', '');
        
        if ($is3D) {
            SessionHelper::set('is_3d', true);
            LogHelper::info('3D seçildi.');
            return $this->processPayment3d($data);
        } else {
            SessionHelper::set('is_3d', false);
            LogHelper::info('2D seçildi.');
            return $this->processPayment2d($data);
        }
    }
    
    /**
     * PaymentRequest nesnesini doldurur
     * @param PaymentRequest $paymentRequest
     * @param array $data
     */
    private function fillPaymentRequest($paymentRequest, $data)
    {
        $name = $data['name'] ?? '';
        $nameParts = CommonHelper::nameSplit($name);
        
        $paymentRequest->setCcHolderName($data['cc_holder_name'] ?? '');
        $paymentRequest->setCcNo($data['cc_no'] ?? '');
        $paymentRequest->setCvv($data['cvv'] ?? 0);
        $paymentRequest->setExpiryMonth($data['expiry_month'] ?? 0);
        $paymentRequest->setExpiryYear($data['expiry_year'] ?? 0);
        $paymentRequest->setMerchantKey($this->config['merchant_key']);
        $paymentRequest->setCurrencyCode($this->config['currency_code']);
        $paymentRequest->setInvoiceDescription($this->config['invoice_description']);
        $paymentRequest->setName($nameParts['firstName']);
        $paymentRequest->setSurname($nameParts['lastName']);
        $paymentRequest->setTotal((float)($data['total'] ?? 0));
        $paymentRequest->setInstallmentsNumber($data['installments_number'] ?? 1);
        $paymentRequest->setHashKey(HashGeneratorHelper::hashGenerator((float)($data['total'] ?? 0), $data['installments_number'] ?? 1));
        $paymentRequest->setInvoiceId(SessionHelper::get('invoice_id'));
    }
    
    /**
     * Item verilerini hazırlar
     * @param array $data
     * @return array
     */
    private function getItemRequestData($data)
    {
        return [
            [
                'name' => $data['name'] ?? 'Ürün',
                'price' => (float)($data['total'] ?? 0),
                'quantity' => 1,
                'description' => $data['description'] ?? 'Ürün açıklaması'
            ]
        ];
    }
}
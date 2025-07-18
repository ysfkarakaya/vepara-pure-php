<?php

class HashGeneratorHelper
{
    /**
     * Hash anahtarı üretir
     * @param float $total
     * @param int $installmentsNumber
     * @return string
     */
    public static function hashGenerator($total, $installmentsNumber)
    {
        $config = include __DIR__ . '/../config.php';
        
        $currency_code = $config['currency_code'];
        $merchant_key = $config['merchant_key'];
        $invoice_id = CommonHelper::randomString(15);
        $app_secret = $config['app_secret'];
        
        $data = $total . '|' . $installmentsNumber . '|' . $currency_code . '|' . $merchant_key . '|' . $invoice_id;

        // Invoice ID'yi session'a kaydet
        SessionHelper::set('invoice_id', $invoice_id);

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt(
            $data, 'aes-256-cbc', $saltWithPassword, 0, $iv
        );
        
        $msg_encrypted_bundle = $iv . ':' . $salt . ':' . $encrypted;
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);
        
        return $msg_encrypted_bundle;
    }
}

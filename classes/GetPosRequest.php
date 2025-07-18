<?php

class GetPosRequest
{
    private $credit_card;
    private $amount;
    private $currency_code;
    private $is_2d;
    private $merchant_key;
    
    public function getCreditCard() { return $this->credit_card; }
    public function setCreditCard($value) { $this->credit_card = $value; }
    
    public function getAmount() { return $this->amount; }
    public function setAmount($value) { $this->amount = $value; }
    
    public function getCurrencyCode() { return $this->currency_code; }
    public function setCurrencyCode($value) { $this->currency_code = $value; }
    
    public function getIs2d() { return $this->is_2d; }
    public function setIs2d($value) { $this->is_2d = $value; }
    
    public function getMerchantKey() { return $this->merchant_key; }
    public function setMerchantKey($value) { $this->merchant_key = $value; }
    
    /**
     * Verileri JSON olarak döndürür
     * @return string
     */
    public function getData()
    {
        $data = [
            'credit_card' => $this->getCreditCard(),
            'amount' => $this->getAmount(),
            'currency_code' => $this->getCurrencyCode(),
            'merchant_key' => $this->getMerchantKey(),
            'is_2d' => $this->getIs2d(),
        ];

        return json_encode($data);
    }
}

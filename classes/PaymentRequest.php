<?php

class PaymentRequest
{
    private $cc_holder_name;
    private $cc_no;
    private $expiry_month;
    private $expiry_year;
    private $merchant_key;
    private $currency_code;
    private $invoice_description;
    private $total;
    private $installments_number;
    private $name;
    private $surname;
    private $hash_key;
    private $invoice_id;
    private $items = [];
    private $cvv;
    
    // 3D için ek alanlar
    private $return_url;
    private $cancel_url;
    
    // Getter ve Setter metodları
    public function getCcHolderName() { return $this->cc_holder_name; }
    public function setCcHolderName($value) { $this->cc_holder_name = $value; }
    
    public function getCcNo() { return $this->cc_no; }
    public function setCcNo($value) { $this->cc_no = $value; }
    
    public function getExpiryMonth() { return $this->expiry_month; }
    public function setExpiryMonth($value) { $this->expiry_month = (int)$value; }
    
    public function getExpiryYear() { return $this->expiry_year; }
    public function setExpiryYear($value) { $this->expiry_year = (int)$value; }
    
    public function getMerchantKey() { return $this->merchant_key; }
    public function setMerchantKey($value) { $this->merchant_key = $value; }
    
    public function getCurrencyCode() { return $this->currency_code; }
    public function setCurrencyCode($value) { $this->currency_code = $value; }
    
    public function getInvoiceDescription() { return $this->invoice_description; }
    public function setInvoiceDescription($value) { $this->invoice_description = $value; }
    
    public function getTotal() { return $this->total; }
    public function setTotal($value) { $this->total = (float)$value; }
    
    public function getInstallmentsNumber() { return $this->installments_number; }
    public function setInstallmentsNumber($value) { $this->installments_number = (int)$value; }
    
    public function getName() { return $this->name; }
    public function setName($value) { $this->name = $value; }
    
    public function getSurname() { return $this->surname; }
    public function setSurname($value) { $this->surname = $value; }
    
    public function getHashKey() { return $this->hash_key; }
    public function setHashKey($value) { $this->hash_key = $value; }
    
    public function getInvoiceId() { return $this->invoice_id; }
    public function setInvoiceId($value) { $this->invoice_id = $value; }
    
    public function getItems() { return $this->items; }
    public function setItems($value) { $this->items = $value; }
    
    public function getCvv() { return $this->cvv; }
    public function setCvv($value) { $this->cvv = (int)$value; }
    
    public function getReturnUrl() { return $this->return_url; }
    public function setReturnUrl($value) { $this->return_url = $value; }
    
    public function getCancelUrl() { return $this->cancel_url; }
    public function setCancelUrl($value) { $this->cancel_url = $value; }
    
    /**
     * Nesneyi array'e çevirir
     * @return array
     */
    public function toArray()
    {
        $data = [];
        
        if ($this->cc_holder_name !== null) $data['cc_holder_name'] = $this->cc_holder_name;
        if ($this->cc_no !== null) $data['cc_no'] = $this->cc_no;
        if ($this->expiry_month !== null) $data['expiry_month'] = $this->expiry_month;
        if ($this->expiry_year !== null) $data['expiry_year'] = $this->expiry_year;
        if ($this->merchant_key !== null) $data['merchant_key'] = $this->merchant_key;
        if ($this->currency_code !== null) $data['currency_code'] = $this->currency_code;
        if ($this->invoice_description !== null) $data['invoice_description'] = $this->invoice_description;
        if ($this->total !== null) $data['total'] = $this->total;
        if ($this->installments_number !== null) $data['installments_number'] = $this->installments_number;
        if ($this->name !== null) $data['name'] = $this->name;
        if ($this->surname !== null) $data['surname'] = $this->surname;
        if ($this->hash_key !== null) $data['hash_key'] = $this->hash_key;
        if ($this->invoice_id !== null) $data['invoice_id'] = $this->invoice_id;
        if (!empty($this->items)) $data['items'] = $this->items;
        if ($this->cvv !== null) $data['cvv'] = $this->cvv;
        if ($this->return_url !== null) $data['return_url'] = $this->return_url;
        if ($this->cancel_url !== null) $data['cancel_url'] = $this->cancel_url;
        
        return $data;
    }
    
    /**
     * Nesneyi JSON'a çevirir
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}

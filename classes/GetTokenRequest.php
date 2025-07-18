<?php

class GetTokenRequest
{
    private $app_secret;
    private $app_id;
    
    public function getAppSecret() { return $this->app_secret; }
    public function setAppSecret($value) { $this->app_secret = $value; }
    
    public function getAppId() { return $this->app_id; }
    public function setAppId($value) { $this->app_id = $value; }
    
    /**
     * Token verilerini JSON olarak döndürür
     * @return string
     */
    public function getTokenData()
    {
        $data = [
            'app_secret' => $this->getAppSecret(),
            'app_id' => $this->getAppId(),
        ];

        return json_encode($data);
    }
}

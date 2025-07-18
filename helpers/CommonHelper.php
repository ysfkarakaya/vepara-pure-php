<?php

class CommonHelper
{
    /**
     * İsmi ad ve soyad olarak ayırır
     * @param string $name
     * @return array
     */
    public static function nameSplit($name)
    {
        $names = explode(' ', trim($name));
        $lastName = array_pop($names);
        $firstName = implode(' ', $names);
        
        return [
            'firstName' => $firstName ?: $lastName, // Eğer tek kelime ise firstName olarak kullan
            'lastName' => $firstName ? $lastName : '' // Tek kelime ise lastName boş
        ];
    }
    
    /**
     * Güvenli POST verisi alır
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getPost($key, $default = null)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Güvenli GET verisi alır
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getGet($key, $default = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * JSON yanıt döndürür
     * @param array $data
     * @param int $statusCode
     */
    public static function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Sayfa yönlendirmesi yapar
     * @param string $url
     */
    public static function redirect($url)
    {
        header("Location: $url");
        exit;
    }
    
    /**
     * Rastgele string üretir
     * @param int $length
     * @return string
     */
    public static function randomString($length = 15)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

<?php

class SessionHelper
{
    /**
     * Session'ı başlatır
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $config = include __DIR__ . '/../config.php';
            session_name($config['session_name']);
            session_start();
        }
    }
    
    /**
     * Session değeri ayarlar
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Session değeri alır
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * Session değeri var mı kontrol eder
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Session değerini siler
     * @param string $key
     */
    public static function remove($key)
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Tüm session'ı temizler
     */
    public static function clear()
    {
        self::start();
        session_destroy();
    }
    
    /**
     * Session'ı kaydet
     */
    public static function save()
    {
        self::start();
        session_write_close();
    }
}

<?php

class LogHelper
{
    /**
     * Log mesajı yazar
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public static function log($level, $message, $context = [])
    {
        $config = include __DIR__ . '/../config.php';
        
        if (!$config['enable_logging']) {
            return;
        }
        
        $logFile = $config['log_file'];
        $logDir = dirname($logFile);
        
        // Log dizinini oluştur
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Info seviyesinde log yazar
     * @param string $message
     * @param array $context
     */
    public static function info($message, $context = [])
    {
        self::log('INFO', $message, $context);
    }
    
    /**
     * Error seviyesinde log yazar
     * @param string $message
     * @param array $context
     */
    public static function error($message, $context = [])
    {
        self::log('ERROR', $message, $context);
    }
    
    /**
     * Warning seviyesinde log yazar
     * @param string $message
     * @param array $context
     */
    public static function warning($message, $context = [])
    {
        self::log('WARNING', $message, $context);
    }
    
    /**
     * Debug seviyesinde log yazar
     * @param string $message
     * @param array $context
     */
    public static function debug($message, $context = [])
    {
        self::log('DEBUG', $message, $context);
    }
}

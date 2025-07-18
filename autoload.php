<?php
// Otomatik sınıf yükleme
spl_autoload_register(function ($class_name) {
    $directories = [
        __DIR__ . '/helpers/',
        __DIR__ . '/classes/',
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

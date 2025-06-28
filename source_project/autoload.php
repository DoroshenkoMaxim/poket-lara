<?php

// Простой автозагрузчик для замены composer autoload

spl_autoload_register(function ($class_name) {
    // Проверяем, что класс принадлежит нашему пространству имен App
    if (strpos($class_name, 'App\\') === 0) {
        // Убираем префикс App\ и заменяем \ на /
        $relative_class = substr($class_name, 4);
        $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative_class) . '.php';
        
        // Загружаем файл, если он существует
        if (file_exists($file)) {
            require_once $file;
        }
    }
}); 
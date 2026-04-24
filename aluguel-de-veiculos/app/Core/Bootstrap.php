<?php
declare(strict_types=1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $filePath = APP_PATH . '/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($filePath)) {
        require_once $filePath;
    }
});

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/lib/helpers.php';
require_once BASE_PATH . '/lib/validators.php';

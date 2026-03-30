<?php
// Simple autoloader for PhpSpreadsheet
spl_autoload_register(function ($class) {
    $prefix = 'PhpOffice\\PhpSpreadsheet\\';
    $base_dir = __DIR__ . '/phpoffice/phpspreadsheet/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load main classes
require_once __DIR__ . '/phpoffice/phpspreadsheet/Spreadsheet.php';
require_once __DIR__ . '/phpoffice/phpspreadsheet/Writer/Xlsx.php';
require_once __DIR__ . '/phpoffice/phpspreadsheet/IOFactory.php';
?>
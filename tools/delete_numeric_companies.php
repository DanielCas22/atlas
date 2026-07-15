<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/CompanyModel.php';

$cm = new CompanyModel();
$result = $cm->deleteNumericNamedCompanies();

echo "Resumen:\n";
echo "Eliminadas: " . intval($result['deleted']) . "\n";
echo "Fallidas: " . intval($result['failed']) . "\n";
if (!empty($result['names'])) {
    echo "Nombres eliminados:\n";
    foreach ($result['names'] as $n) {
        echo "- " . $n . "\n";
    }
}

echo "Listo.\n";

<?php
require 'config/Database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query('SELECT id, name FROM security_companies ORDER BY name');
$companies = $stmt->fetchAll();

// Agrupar por nombre normalizado
$grouped = [];
foreach ($companies as $c) {
    $name = trim($c['name']);
    $normalized = mb_strtolower($name, 'UTF-8');
    $normalized = str_replace(['_', ' ', '  ', '  '], '', $normalized);
    if (!isset($grouped[$normalized])) {
        $grouped[$normalized] = [];
    }
    $grouped[$normalized][] = $c;
}

// Mostrar duplicados
echo "=== EMPRESAS DUPLICADAS ===\n\n";
$duplicatesFound = false;
foreach ($grouped as $key => $list) {
    if (count($list) > 1) {
        $duplicatesFound = true;
        echo 'Grupo: ' . $list[0]['name'] . "\n";
        foreach ($list as $c) {
            echo '  - ID: ' . $c['id'] . ' | Nombre: ' . $c['name'] . "\n";
        }
        echo "\n";
    }
}

if (!$duplicatesFound) {
    echo "No se encontraron empresas duplicadas.\n";
}

echo "\n=== TOTAL EMPRESAS: " . count($companies) . " ===\n";
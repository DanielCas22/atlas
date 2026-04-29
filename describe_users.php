<?php

require_once __DIR__ . '/config/Database.php';

$db = Database::getInstance()->getConnection();

// Obtener estructura de la tabla
$stmt = $db->query('DESCRIBE users');
$columns = $stmt->fetchAll();

echo "Columnas de la tabla 'users':\n";
echo "================================\n";
foreach ($columns as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}
?>

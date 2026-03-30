<?php
require_once 'config/Database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query('SELECT id, name FROM security_companies ORDER BY name');
while ($row = $stmt->fetch()) {
    echo $row['id'] . ' - ' . $row['name'] . PHP_EOL;
}
?>
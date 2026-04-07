<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getInstance()->getConnection();
    $username = 'daniel';
    $password = 'daniel913';
    $fullname = 'Daniel Atlas';
    $email = 'daniel@atlas.local';
    $roleId = 1; // ADMIN

    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo "El usuario '$username' ya existe.\n";
        exit(0);
    }

    $stmt = $pdo->prepare('SELECT id FROM roles WHERE id = ?');
    $stmt->execute([$roleId]);
    $role = $stmt->fetch();
    if (!$role) {
        echo "No existe el rol con id $roleId. Asegúrate de que la tabla roles está inicializada.\n";
        exit(1);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $pdo->prepare('INSERT INTO users (role_id, username, password, fullname, email) VALUES (?, ?, ?, ?, ?)');
    $insert->execute([$roleId, $username, $hash, $fullname, $email]);
    echo "Usuario '$username' creado con éxito. Contraseña: $password\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(1);
}

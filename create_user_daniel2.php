<?php

require_once __DIR__ . '/config/Database.php';

$db = Database::getInstance()->getConnection();

// Hash de la contraseña
$password = password_hash('daniel', PASSWORD_BCRYPT);

// Insertar nuevo usuario con role_id = 1 (admin)
$stmt = $db->prepare('INSERT INTO users (role_id, username, password, fullname, email) VALUES (?, ?, ?, ?, ?)');
$result = $stmt->execute([1, 'daniel', $password, 'Daniel', 'daniel@atlas.local']);

if ($result) {
    echo "✅ Usuario creado exitosamente!\n";
    echo "Username: daniel\n";
    echo "Password: daniel\n";
    echo "Email: daniel@atlas.local\n";
    echo "Role: admin\n";
} else {
    echo "❌ Error al crear el usuario\n";
}
?>

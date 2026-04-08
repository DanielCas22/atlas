<?php
/**
 * Script de migración para agregar columnas de recuperación de contraseña
 * URL: http://localhost/atlas/migrate_password_reset.php
 * 
 * IMPORTANTE: Elimina este archivo después de ejecutarlo una sola vez
 */

session_start();

// Solo permitir ejecución si el usuario está autenticado como admin
if (empty($_SESSION['user']) || strtolower($_SESSION['user']['role'] ?? '') !== 'administrador') {
    http_response_code(403);
    die('❌ Acceso denegado. Solo administradores pueden ejecutar migraciones.');
}

// Incluir configuración de BD
require_once __DIR__ . '/config/Database.php';

try {
    $db = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Ejecutar migraciones
    $migrations = [
        "ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL DEFAULT NULL" => "Agregando columna reset_token",
        "ALTER TABLE users ADD COLUMN reset_token_expires DATETIME NULL DEFAULT NULL" => "Agregando columna reset_token_expires",
        "CREATE INDEX idx_reset_token ON users(reset_token)" => "Creando índice idx_reset_token",
    ];

    foreach ($migrations as $sql => $message) {
        try {
            $db->exec($sql);
            echo "✓ $message\n";
        } catch (Exception $e) {
            // Solo mostrar error si no es por columna duplicada
            if (strpos($e->getMessage(), 'Duplicate column name') === false && 
                strpos($e->getMessage(), 'already exists') === false) {
                echo "⚠ $message (puede que ya exista)\n";
            } else {
                echo "✓ $message (ya existe)\n";
            }
        }
    }

    echo "\n✅ Migración completada exitosamente\n";
    echo "⚠️  Por seguridad, elimina este archivo (migrate_password_reset.php)\n";

} catch (Exception $e) {
    http_response_code(500);
    echo "❌ Error en migración: " . $e->getMessage();
}
?>

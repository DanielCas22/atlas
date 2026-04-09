<?php

/**
 * Script de prueba para configuración SMTP
 *
 * Ejecutar desde terminal: php test_smtp.php
 */

require_once __DIR__ . '/config/SMTPConfig.php';
require_once __DIR__ . '/helpers/EmailHelper.php';

echo "=== PRUEBA DE CONFIGURACIÓN SMTP ===\n\n";

if (!SMTPConfig::isConfigured()) {
    echo "❌ SMTP no está configurado o habilitado.\n";
    echo "Revisa config/SMTPConfig.php\n";
    exit(1);
}

echo "✅ SMTP está habilitado\n";
echo "Entorno: " . (SMTPConfig::isLocalhost() ? "DESARROLLO (localhost)" : "PRODUCCIÓN (dominio)") . "\n";
echo "Proveedor: " . SMTPConfig::getProvider() . "\n";

$config = SMTPConfig::getConfig();
echo "Host: " . $config['host'] . "\n";
echo "Port: " . $config['port'] . "\n";
echo "Secure: " . ($config['secure'] ?: 'ninguno') . "\n\n";

if (SMTPConfig::getProvider() === 'gmail' && strpos(SMTPConfig::GMAIL_APP_PASSWORD, 'tu_app_password') !== false) {
    echo "⚠️  ATENCIÓN: Gmail no está configurado para producción\n";
    echo "Ejecuta: php setup_gmail.php para ver instrucciones\n\n";
}

echo "Probando envío de email...\n";

$emailHelper = new EmailHelper();

// Email de prueba - usando admin de la BD
$testEmail = 'angeldb20052@gmail.com'; // 📧 Email del usuario admin en la BD
$testUsername = 'Admin Atlas';

$result = $emailHelper->sendPasswordResetEmail($testEmail, $testUsername, 'token_de_prueba_123');

if ($result) {
    echo "✅ Email enviado exitosamente!\n";
    echo "Revisa tu bandeja de entrada en: $testEmail\n";
} else {
    echo "❌ Error al enviar email\n";
    echo "Revisa los logs de error y la configuración SMTP\n";
}

echo "\n=== FIN DE PRUEBA ===\n";
?>
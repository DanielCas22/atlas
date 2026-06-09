<?php

/**
 * Configuración de SMTP para envío de emails
 * 
 * Opciones de SMTP:
 * 
 * DESARROLLO LOCAL (localhost):
 *    - Usa Mailtrap automáticamente
 *
 * PRODUCCIÓN (dominio real):
 *    - Usa Gmail (necesitas contraseña de app)
 *    - O SendGrid (más confiable)
 *
 * Para Gmail en producción:
 * 1. Ve a: https://myaccount.google.com/security
 * 2. Habilita "Verificación en dos pasos"
 * 3. Ve a "Contraseñas de aplicación"
 * 4. Genera una contraseña para "Atlas Seguridad"
 * 5. Pon esa contraseña en GMAIL_APP_PASSWORD
 */

class SMTPConfig
{
    // Cambiar a true para habilitar envío de emails
    const ENABLED = true;

    // Cambia a 'mailtrap', 'gmail' o 'sendgrid'
    // Usa 'sendgrid' para enviar correos reales desde un proveedor SMTP externo
    const PROVIDER = 'sendgrid';

    // URL base de la aplicación, úsala cuando la app esté en un subdirectorio de htdocs
    const BASE_URL = 'http://localhost/atlas';

    // ==================== DETECCIÓN AUTOMÁTICA DE ENTORNO ====================
    public static function isLocalhost()
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return strpos($host, 'localhost') !== false ||
               strpos($host, '127.0.0.1') !== false ||
               strpos($host, '.local') !== false;
    }

    // Función para obtener el proveedor según entorno
    public static function getProvider()
    {
        if (self::PROVIDER !== 'auto') {
            return self::PROVIDER;
        }

        return self::isLocalhost() ? 'mailtrap' : 'gmail';
    }

    // ==================== MAILTRAP (DESARROLLO LOCAL) ====================
    const MAILTRAP_HOST = 'sandbox.smtp.mailtrap.io';
    const MAILTRAP_PORT = 2525;
    const MAILTRAP_USERNAME = '9e4a7d09490fa1';
    const MAILTRAP_PASSWORD = 'da1a5e31e4e34a';

    // ==================== GMAIL (PRODUCCIÓN) ====================
    const GMAIL_HOST = 'smtp.gmail.com';
    const GMAIL_PORT = 587;  // 587 para TLS, 465 para SSL
    const GMAIL_USERNAME = 'tu_email@gmail.com';  // ✏️ CAMBIAR: pon tu email de Gmail
    const GMAIL_APP_PASSWORD = 'tu_app_password_aqui';      // ✏️ CAMBIAR: genera contraseña de app

    // ==================== SENDGRID (ALTERNATIVA PRODUCCIÓN) ====================
    const SENDGRID_HOST = 'smtp.sendgrid.net';
    const SENDGRID_PORT = 587;
    const SENDGRID_USERNAME = 'apikey';
    const SENDGRID_API_KEY = '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';  // ✏️ Cambiado a tu API Key de SendGrid

    // Remitente predeterminado
    const FROM_ADDRESS = 'danielcaes07@gmail.com';
    const FROM_NAME = 'Atlas Seguridad';

    /**
     * Obtener configuración según el proveedor
     */
    public static function getConfig()
    {
        if (!self::ENABLED) {
            return null;
        }

        $configs = [
            'mailtrap' => [
                'host' => self::MAILTRAP_HOST,
                'port' => self::MAILTRAP_PORT,
                'username' => self::MAILTRAP_USERNAME,
                'password' => self::MAILTRAP_PASSWORD,
                'secure' => 'tls',
            ],
            'gmail' => [
                'host' => self::GMAIL_HOST,
                'port' => self::GMAIL_PORT,
                'username' => self::GMAIL_USERNAME,
                'password' => self::GMAIL_APP_PASSWORD,
                'secure' => 'tls',
            ],
            'sendgrid' => [
                'host' => self::SENDGRID_HOST,
                'port' => self::SENDGRID_PORT,
                'username' => self::SENDGRID_USERNAME,
                'password' => self::SENDGRID_API_KEY,
                'secure' => 'tls',
            ],
        ];

        return $configs[self::getProvider()] ?? null;
    }

    /**
     * Validar configuración
     */
    public static function isConfigured()
    {
        if (!self::ENABLED) {
            return false;
        }

        $config = self::getConfig();
        if (!$config) {
            return false;
        }

        if (empty($config['host']) || empty($config['port'])) {
            return false;
        }

        $provider = self::getProvider();
        if (in_array($provider, ['gmail', 'sendgrid'], true)) {
            return !empty($config['username']) && !empty($config['password']);
        }

        // Mailtrap y otros proveedores sin credenciales opcionales pueden funcionar con host/port únicamente
        return true;
    }
}
?>

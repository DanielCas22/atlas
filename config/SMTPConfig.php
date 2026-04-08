<?php

/**
 * Configuración de SMTP para envío de emails
 * 
 * Opciones de SMTP:
 * 
 * 1. GMAIL (recomendado para pruebas):
 *    - Host: smtp.gmail.com
 *    - Port: 587 (TLS) o 465 (SSL)
 *    - Username: tu_email@gmail.com
 *    - Password: contraseña de app (generar en Google Account)
 *    
 * 2. SERVIDOR LOCAL (XAMPP):
 *    - Host: localhost
 *    - Port: 25
 *    - Username: dejar vacío
 *    - Password: dejar vacío
 *    
 * 3. SENDGRID:
 *    - Host: smtp.sendgrid.net
 *    - Port: 587
 *    - Username: apikey
 *    - Password: tu_api_key
 */

class SMTPConfig
{
    // Cambiar a true para habilitar envío de emails
    const ENABLED = false;

    // Tipo de SMTP: 'gmail', 'local', 'sendgrid', 'custom'
    const PROVIDER = 'gmail';

    // ==================== GMAIL ====================
    const GMAIL_HOST = 'smtp.gmail.com';
    const GMAIL_PORT = 587;  // 587 para TLS, 465 para SSL
    const GMAIL_USERNAME = 'tu_email@gmail.com';  // ✏️ CAMBIAR
    const GMAIL_PASSWORD = 'tu_app_password';      // ✏️ CAMBIAR (Generar desde Google Account)

    // ==================== SERVIDOR LOCAL ====================
    const LOCAL_HOST = 'localhost';
    const LOCAL_PORT = 25;
    const LOCAL_USERNAME = '';
    const LOCAL_PASSWORD = '';

    // ==================== SENDGRID ====================
    const SENDGRID_HOST = 'smtp.sendgrid.net';
    const SENDGRID_PORT = 587;
    const SENDGRID_USERNAME = 'apikey';
    const SENDGRID_PASSWORD = 'tu_sendgrid_api_key';  // ✏️ CAMBIAR

    // ==================== CUSTOM ====================
    const CUSTOM_HOST = 'mail.tudominio.com';
    const CUSTOM_PORT = 587;
    const CUSTOM_USERNAME = 'tu_usuario';            // ✏️ CAMBIAR
    const CUSTOM_PASSWORD = 'tu_password';           // ✏️ CAMBIAR

    // Remitente predeterminado
    const FROM_ADDRESS = 'noreply@atlasseguridad.com';
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
            'gmail' => [
                'host' => self::GMAIL_HOST,
                'port' => self::GMAIL_PORT,
                'username' => self::GMAIL_USERNAME,
                'password' => self::GMAIL_PASSWORD,
                'secure' => 'tls',  // 'tls' o 'ssl'
            ],
            'local' => [
                'host' => self::LOCAL_HOST,
                'port' => self::LOCAL_PORT,
                'username' => self::LOCAL_USERNAME,
                'password' => self::LOCAL_PASSWORD,
                'secure' => '',
            ],
            'sendgrid' => [
                'host' => self::SENDGRID_HOST,
                'port' => self::SENDGRID_PORT,
                'username' => self::SENDGRID_USERNAME,
                'password' => self::SENDGRID_PASSWORD,
                'secure' => 'tls',
            ],
            'custom' => [
                'host' => self::CUSTOM_HOST,
                'port' => self::CUSTOM_PORT,
                'username' => self::CUSTOM_USERNAME,
                'password' => self::CUSTOM_PASSWORD,
                'secure' => 'tls',
            ],
        ];

        return $configs[self::PROVIDER] ?? null;
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

        // Validar que host y port no estén vacíos
        return !empty($config['host']) && !empty($config['port']);
    }
}
?>

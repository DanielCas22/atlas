<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/SMTPConfig.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    private $mailer;
    private $config;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->config = SMTPConfig::getConfig();

        if ($this->config && SMTPConfig::isConfigured()) {
            $this->configureMailer();
        }
    }

    /**
     * Configurar PHPMailer con SMTP
     */
    private function configureMailer()
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->Port = $this->config['port'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            
            if (!empty($this->config['secure'])) {
                $this->mailer->SMTPSecure = $this->config['secure'];
            }

            $this->mailer->setFrom(SMTPConfig::FROM_ADDRESS, SMTPConfig::FROM_NAME);
        } catch (Exception $e) {
            error_log("Error configurando SMTP: " . $e->getMessage());
        }
    }

    /**
     * Enviar email de recuperación de contraseña
     */
    public function sendPasswordResetEmail($userEmail, $username, $resetToken)
    {
        if (!SMTPConfig::isConfigured()) {
            return false;
        }

        try {
            $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . '/atlas/index.php?c=auth&a=reset&token=' . urlencode($resetToken);

            $this->mailer->addAddress($userEmail);
            $this->mailer->Subject = 'Recuperar contraseña - Atlas Seguridad';
            $this->mailer->isHTML(true);
            $this->mailer->Body = $this->getPasswordResetEmailTemplate($username, $resetLink);
            $this->mailer->AltBody = $this->getPasswordResetPlainText($username, $resetLink);

            $result = $this->mailer->send();

            // Limpiar destinatarios para próximo correo
            $this->mailer->clearAddresses();

            return $result;
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Template HTML del email
     */
    private function getPasswordResetEmailTemplate($username, $resetLink)
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 30px;">
        <h1 style="color: #667eea; text-align: center; margin-top: 0;">Atlas Seguridad</h1>
        
        <p style="color: #333; font-size: 16px; line-height: 1.5;">
            Hola <strong>$username</strong>,
        </p>
        
        <p style="color: #333; font-size: 16px; line-height: 1.5;">
            Hemos recibido una solicitud para recuperar tu contraseña. 
            Si no fuiste tú, puedes ignorar este email.
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="$resetLink" style="background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-size: 16px; display: inline-block;">
                Recuperar Contraseña
            </a>
        </div>
        
        <p style="color: #666; font-size: 14px; line-height: 1.5;">
            O copia este enlace en tu navegador:
            <br>
            <code style="background-color: #f5f5f5; padding: 8px; display: block; word-break: break-all; margin-top: 10px;">
                $resetLink
            </code>
        </p>
        
        <p style="color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px;">
            Este enlace expira en 30 minutos por razones de seguridad.
            <br>
            Atlas Seguridad © 2026
        </p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Versión texto plano del email
     */
    private function getPasswordResetPlainText($username, $resetLink)
    {
        return <<<TEXT
Hola $username,

Hemos recibido una solicitud para recuperar tu contraseña.

Para establecer una nueva contraseña, ingresa en el siguiente enlace:
$resetLink

Este enlace expira en 30 minutos por razones de seguridad.

Si no fuiste tú quien solicitud el cambio, puedes ignorar este email.

Atlas Seguridad
TEXT;
    }

    /**
     * Verificar que SMTP esté configurado correctamente
     */
    public static function testConnection()
    {
        $config = SMTPConfig::getConfig();
        if (!$config) {
            return ['success' => false, 'message' => 'SMTP no está habilitado'];
        }

        try {
            $mailer = new PHPMailer(true);
            $mailer->isSMTP();
            $mailer->Host = $config['host'];
            $mailer->Port = $config['port'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $config['username'];
            $mailer->Password = $config['password'];
            
            if (!empty($config['secure'])) {
                $mailer->SMTPSecure = $config['secure'];
            }

            $mailer->smtpConnect();
            return ['success' => true, 'message' => 'Conexión SMTP exitosa'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()];
        }
    }
}
?>

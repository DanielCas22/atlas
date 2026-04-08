<?php
/**
 * Panel de configuración y prueba de SMTP
 * URL: http://localhost/atlas/admin/configure_smtp.php
 * 
 * ⚠️ ELIMINAR DESPUÉS DE CONFIGURAR
 */

session_start();

// Solo permitir acceso como administrador
if (empty($_SESSION['user']) || strtolower($_SESSION['user']['role'] ?? '') !== 'administrador') {
    http_response_code(403);
    die('❌ Acceso denegado. Solo administradores pueden acceder.');
}

require_once __DIR__ . '/../config/SMTPConfig.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';

$testResult = null;
$testAttempted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'test') {
        $testAttempted = true;
        $testResult = EmailHelper::testConnection();
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar SMTP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
        }
        h1 { color: #667eea; margin-bottom: 30px; }
        h2 { color: #333; margin: 30px 0 15px; font-size: 18px; }
        .section { margin-bottom: 40px; padding-bottom: 30px; border-bottom: 1px solid #eee; }
        .section:last-child { border-bottom: none; }
        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .success { background: #d4edda; border-left-color: #28a745; color: #155724; }
        .error { background: #f8d7da; border-left-color: #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table thead {
            background: #f5f5f5;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            font-weight: 600;
            color: #333;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #d63384;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px 5px 5px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status.enabled {
            background: #d4edda;
            color: #155724;
        }
        .status.disabled {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚙️ Configuración de SMTP</h1>

        <!-- ESTADO ACTUAL -->
        <div class="section">
            <h2>Estado Actual</h2>
            <p>
                SMTP Habilitado: 
                <span class="status <?= SMTPConfig::ENABLED ? 'enabled' : 'disabled' ?>">
                    <?= SMTPConfig::ENABLED ? '✓ SÍ' : '✗ NO' ?>
                </span>
            </p>
            <?php if (SMTPConfig::ENABLED): ?>
                <p style="margin-top: 10px;">
                    Proveedor: <strong><?= ucfirst(SMTPConfig::PROVIDER) ?></strong>
                </p>
            <?php endif; ?>
        </div>

        <!-- INSTRUCCIONES POR PROVEEDOR -->
        <div class="section">
            <h2>📧 Opciones de Configuración</h2>

            <div style="margin-bottom: 30px;">
                <h3 style="color: #667eea; font-size: 16px; margin-bottom: 10px;">1. Gmail (Recomendado para pruebas)</h3>
                <ol style="margin-left: 20px; line-height: 1.8;">
                    <li>Ve a <code>config/SMTPConfig.php</code></li>
                    <li>Cambia: <code>const ENABLED = true;</code></li>
                    <li>Cambia: <code>const PROVIDER = 'gmail';</code></li>
                    <li>En Gmail:
                        <ul style="margin-left: 20px; margin-top: 5px;">
                            <li>Habilita 2FA en tu cuenta Google</li>
                            <li>Ve a <a href="https://myaccount.google.com/apppasswords" target="_blank">Contraseñas de aplicaciones</a></li>
                            <li>Crea una contraseña para "Mail" → "Windows"</li>
                            <li>Copia esa contraseña</li>
                        </ul>
                    </li>
                    <li>Actualiza en <code>SMTPConfig.php</code>:
                        <div style="background: #f5f5f5; padding: 10px; margin-top: 5px; border-radius: 4px;">
                            <code>const GMAIL_USERNAME = 'tu_email@gmail.com';</code><br>
                            <code>const GMAIL_PASSWORD = 'la_contraseña_generada';</code>
                        </div>
                    </li>
                </ol>
            </div>

            <div style="margin-bottom: 30px;">
                <h3 style="color: #667eea; font-size: 16px; margin-bottom: 10px;">2. Servidor Local (XAMPP)</h3>
                <ol style="margin-left: 20px; line-height: 1.8;">
                    <li>En <code>config/SMTPConfig.php</code>, cambia:
                        <div style="background: #f5f5f5; padding: 10px; margin-top: 5px; border-radius: 4px;">
                            <code>const ENABLED = true;</code><br>
                            <code>const PROVIDER = 'local';</code>
                        </div>
                    </li>
                    <li>Confirma que Sendmail esté habilitado en php.ini</li>
                </ol>
            </div>

            <div style="margin-bottom: 30px;">
                <h3 style="color: #667eea; font-size: 16px; margin-bottom: 10px;">3. SendGrid</h3>
                <ol style="margin-left: 20px; line-height: 1.8;">
                    <li>Regístrate en <a href="https://sendgrid.com" target="_blank">SendGrid</a></li>
                    <li>Genera una API Key</li>
                    <li>En <code>SMTPConfig.php</code>, actualiza:
                        <div style="background: #f5f5f5; padding: 10px; margin-top: 5px; border-radius: 4px;">
                            <code>const ENABLED = true;</code><br>
                            <code>const PROVIDER = 'sendgrid';</code><br>
                            <code>const SENDGRID_PASSWORD = 'tu_api_key';</code>
                        </div>
                    </li>
                </ol>
            </div>

            <div>
                <h3 style="color: #667eea; font-size: 16px; margin-bottom: 10px;">4. Servidor Personalizado</h3>
                <ol style="margin-left: 20px; line-height: 1.8;">
                    <li>En <code>SMTPConfig.php</code>, usa la sección CUSTOM:
                        <div style="background: #f5f5f5; padding: 10px; margin-top: 5px; border-radius: 4px;">
                            <code>const ENABLED = true;</code><br>
                            <code>const PROVIDER = 'custom';</code><br>
                            <code>const CUSTOM_HOST = 'tu_host';</code><br>
                            <code>const CUSTOM_PORT = 587;</code><br>
                            <code>const CUSTOM_USERNAME = 'usuario';</code><br>
                            <code>const CUSTOM_PASSWORD = 'pass';</code>
                        </div>
                    </li>
                </ol>
            </div>
        </div>

        <!-- PRUEBA DE CONEXIÓN -->
        <div class="section">
            <h2>🧪 Prueba de Conexión</h2>
            <?php if ($testAttempted): ?>
                <div class="info-box <?= $testResult['success'] ? 'success' : 'error' ?>">
                    <?= $testResult['success'] ? '✓' : '✗' ?> 
                    <?= htmlspecialchars($testResult['message']) ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="action" value="test">
                <button type="submit" class="btn btn-primary">Probar Conexión SMTP</button>
            </form>
        </div>

        <!-- RESUMEN DE CONFIGURACIÓN ACTUAL -->
        <div class="section">
            <h2>📋 Configuración Actual</h2>
            <table>
                <thead>
                    <tr>
                        <th>Parámetro</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SMTP Habilitado</td>
                        <td><?= SMTPConfig::ENABLED ? '✓ Sí' : '✗ No' ?></td>
                    </tr>
                    <tr>
                        <td>Proveedor</td>
                        <td><?= ucfirst(SMTPConfig::PROVIDER) ?></td>
                    </tr>
                    <tr>
                        <td>Host</td>
                        <td><code><?= SMTPConfig::{strtoupper(SMTPConfig::PROVIDER) . '_HOST'} ?? 'N/A' ?></code></td>
                    </tr>
                    <tr>
                        <td>Puerto</td>
                        <td><?= SMTPConfig::{strtoupper(SMTPConfig::PROVIDER) . '_PORT'} ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <td>Usuario</td>
                        <td><code><?= SMTPConfig::{strtoupper(SMTPConfig::PROVIDER) . '_USERNAME'} ?? 'N/A' ?></code></td>
                    </tr>
                    <tr>
                        <td>Remitente</td>
                        <td><code><?= SMTPConfig::FROM_ADDRESS ?></code></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PRÓXIMOS PASOS -->
        <div class="section">
            <h2>✅ Próximos Pasos</h2>
            <div class="info-box warning">
                <strong>⚠️ Importante:</strong>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Edita <code>config/SMTPConfig.php</code> con tu configuración</li>
                    <li>Preba la conexión usando el botón "Probar Conexión SMTP" ↑</li>
                    <li>Cuando funcione, prueba el flujo de recuperación de contraseña</li>
                    <li>Elimina este archivo (<code>admin/configure_smtp.php</code>) por seguridad</li>
                </ol>
            </div>
        </div>

        <p style="text-align: center; color: #999; margin-top: 30px; font-size: 12px;">
            🔒 Por seguridad, elimina este archivo después de configurar SMTP
        </p>
    </div>
</body>
</html>

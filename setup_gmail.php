<?php

/**
 * Script de configuración de Gmail para producción
 *
 * Ejecutar desde terminal: php setup_gmail.php
 *
 * Este script te ayuda a configurar Gmail SMTP para cuando subas
 * tu proyecto a un hosting web con dominio real.
 */

echo "=== CONFIGURACIÓN DE GMAIL PARA PRODUCCIÓN ===\n\n";

echo "Para usar Gmail en producción, necesitas:\n";
echo "1. Una cuenta Gmail\n";
echo "2. Verificación en dos pasos habilitada\n";
echo "3. Una contraseña de aplicación\n\n";

echo "PASOS PARA CONFIGURAR GMAIL:\n";
echo "================================\n\n";

echo "1. Ve a tu cuenta Gmail en el navegador\n";
echo "   URL: https://myaccount.google.com/security\n\n";

echo "2. Habilita 'Verificación en dos pasos' si no la tienes\n";
echo "   (Es obligatorio para contraseñas de app)\n\n";

echo "3. Ve a 'Contraseñas de aplicación'\n";
echo "   - Busca 'Contraseñas de aplicación' en la búsqueda\n";
echo "   - Selecciona 'Aplicación: Otro (nombre personalizado)'\n";
echo "   - Pon el nombre: 'Atlas Seguridad'\n";
echo "   - Copia la contraseña de 16 caracteres que te da\n\n";

echo "4. Edita el archivo config/SMTPConfig.php:\n";
echo "   - Cambia GMAIL_USERNAME por tu email real\n";
echo "   - Cambia GMAIL_APP_PASSWORD por la contraseña de app\n\n";

echo "EJEMPLO:\n";
echo "const GMAIL_USERNAME = 'miempresa@gmail.com';\n";
echo "const GMAIL_APP_PASSWORD = 'abcd-efgh-ijkl-mnop';\n\n";

echo "5. Prueba con: php test_smtp.php\n\n";

echo "ALTERNATIVA: SendGrid (más confiable para apps)\n";
echo "=================================================\n";
echo "- Ve a: https://sendgrid.com\n";
echo "- Crea cuenta gratuita\n";
echo "- Genera API Key\n";
echo "- Configura en SMTPConfig.php como 'sendgrid'\n\n";

echo "=== FIN DE INSTRUCCIONES ===\n";
?></content>
<parameter name="filePath">c:\xampp\htdocs\atlas\setup_gmail.php
# Módulo de Recuperación de Contraseña - Instrucciones

## ¿Qué se ha creado?

Se ha implementado un módulo de recuperación de contraseña completamente aislado que no afecta el resto del programa. Incluye:

- Pantalla de "¿Olvidaste tu contraseña?" desde login
- Generación de tokens seguros
- Pantalla de restablecimiento de contraseña
- Validación de tokens con expiración

## Archivos modificados

1. **controllers/AuthController.php**
   - Agregadas acciones: `forgot()` y `reset()`

2. **models/UserModel.php**
   - Agregados métodos: `findByEmail()`, `savePasswordReset()`, `findByResetToken()`, `clearResetToken()`

3. **views/auth/login.php**
   - Enlace "¿Olvidaste tu contraseña?" ahora funcional

## Archivos creados

1. **views/auth/forgot.php** - Formulario para solicitar recuperación
2. **views/auth/reset.php** - Formulario para establecer nueva contraseña
3. **db_migration_password_reset.sql** - Script SQL para preparar DB

## Próximos pasos para activar

### 1. Ejecutar migración SQL
```sql
ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN reset_token_expires DATETIME NULL DEFAULT NULL;
CREATE INDEX idx_reset_token ON users(reset_token);
```

O ejecuta desde phpMyAdmin el archivo: `db_migration_password_reset.sql`

### 2. Probar el flujo

#### Pantalla de recuperación:
```
http://localhost/atlas/index.php?c=auth&a=forgot
```

- Ingresa con un email válido registrado en la base de datos
- Se genera un token seguro y se guarda en la DB

#### Pantalla de restablecimiento:
```
http://localhost/atlas/index.php?c=auth&a=reset&token=<TOKEN_GENERADO>
```

- El token se obtiene de la tabla `users` columna `reset_token`
- Ingresa nueva contraseña y confirma
- La contraseña se actualiza y el token se limpia

## Características de seguridad

✓ Tokens de 64 caracteres con `random_bytes()`
✓ Expiración de tokens (30 minutos por defecto)
✓ Contraséñas hasheadas con `PASSWORD_BCRYPT`
✓ Validación de longitud mínima (6 caracteres)
✓ Validación de email
✓ Tokens usados una sola vez

## Notas importantes

- **Email**: El sistema busca usuarios por `email`. Asegúrate de que los usuarios tengan emails válidos en la BD.
- **Envío de emails**: Actualmente está en modo "desarrollo" - muestra el token en pantalla. Para producción, configurar un servicio de email (PHPMailer, SendGrid, etc.).
- **Tokens temporales**: Si quieres cambiar el tiempo de expiración, edita `savePasswordReset()` en UserModel.php

## Cómo desactivar (sin afectar nada)

- Se puede eliminar el enlace en `views/auth/login.php`
- Se pueden eliminar las columnas de la BD con: `ALTER TABLE users DROP COLUMN reset_token, DROP COLUMN reset_token_expires;`
- No toca ningún otro código del sistema

## Testing

1. Crea un usuario de prueba
2. Accede a: `index.php?c=auth&a=forgot`
3. Ingresa el email del usuario
4. Copia el token mostrado
5. Ve a: `index.php?c=auth&a=reset&token=<TOKEN_COPIADO>`
6. Establece nueva contraseña
7. Intenta login con la nueva contraseña

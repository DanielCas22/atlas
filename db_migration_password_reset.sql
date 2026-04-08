-- Script para agregar campos de recuperación de contraseña a la tabla users
-- Ejecutar solo si no existen las columnas

-- Agregar columnas para recuperación de contraseña
ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL DEFAULT NULL AFTER password;
ALTER TABLE users ADD COLUMN reset_token_expires DATETIME NULL DEFAULT NULL AFTER reset_token;

-- Crear índice para búsquedas rápidas
CREATE INDEX idx_reset_token ON users(reset_token);

-- Opcional: limpiar tokens expirados
-- DELETE FROM users WHERE reset_token_expires < NOW() AND reset_token_expires IS NOT NULL;

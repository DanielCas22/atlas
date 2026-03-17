-- db.sql: Esquema de base de datos único para Atlas MVC

CREATE DATABASE IF NOT EXISTS atlas CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE atlas;

-- Roles (admin, supervisor, operador)
CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

-- Usuarios
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  fullname VARCHAR(120) NOT NULL,
  email VARCHAR(120) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tipo de examen
CREATE TABLE IF NOT EXISTS exam_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

-- Empresa vinculada de seguridad
CREATE TABLE IF NOT EXISTS security_companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  contact VARCHAR(120) DEFAULT NULL
) ENGINE=InnoDB;

-- Examenes asignados
CREATE TABLE IF NOT EXISTS exams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  exam_type_id INT NOT NULL,
  candidate_name VARCHAR(120) NOT NULL,
  status ENUM('PENDIENTE','EN_CURSO','FINALIZADO','RECHAZADO') NOT NULL DEFAULT 'PENDIENTE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (company_id) REFERENCES security_companies(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (exam_type_id) REFERENCES exam_types(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Datos iniciales
INSERT IGNORE INTO roles (id,name,description) VALUES
(1,'ADMIN','Administrador completo'),
(2,'SUPERVISOR','Supervisor de operaciones'),
(3,'OPERADOR','Operador de registro');

INSERT IGNORE INTO users (role_id,username,password,fullname,email) VALUES
(1,'admin', '$2y$10$K1u/RUAi3z1JczLZG2vWbO8cM4Z4X2VorPM0z6Oe9bG0M.dmy9Fn6', 'Admin Atlas', 'admin@atlas.local');
-- password: admin123

INSERT IGNORE INTO exam_types (name,description) VALUES
('Chequeo psicométrico','Evaluación psicológica'),
('Examen médico','Chequeo médico general'),
('Investigación de antecedentes','Verificación de antecedentes');

INSERT IGNORE INTO security_companies (name,contact) VALUES
('SegurGlobal','contacto@segurglobal.com'),
('ProtecMax','info@protecmax.com');

INSERT IGNORE INTO exams (company_id,exam_type_id,candidate_name,status) VALUES
(1,1,'Juan Pérez','PENDIENTE'),
(2,2,'María López','EN_CURSO');

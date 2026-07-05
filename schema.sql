-- ============================================================
-- Esquema de base de datos para el sistema Mesa de Ayuda
-- ============================================================

-- Crear la base de datos si no existe, con soporte UTF-8
CREATE DATABASE IF NOT EXISTS mesadeayuda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mesadeayuda;

-- Crear usuario de aplicación con permisos sobre la base de datos
CREATE USER IF NOT EXISTS 'proyecto'@'localhost' IDENTIFIED BY 'proyecto';
GRANT ALL PRIVILEGES ON mesadeayuda.* TO 'proyecto'@'localhost';
FLUSH PRIVILEGES;

-- Tabla principal: almacena los tickets de soporte
CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,           -- Identificador único del ticket
  usuario VARCHAR(100) NOT NULL,               -- Nombre o correo del reportante
  asunto VARCHAR(255) NOT NULL,                 -- Título breve del problema
  mensaje TEXT NOT NULL,                        -- Descripción detallada
  estatus VARCHAR(20) NOT NULL DEFAULT 'Abierto', -- Estado: Abierto | Cerrado
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación automática
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Base de datos para el sistema de tickets (Mesa de ayuda)
CREATE DATABASE IF NOT EXISTS mesadeayuda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mesadeayuda;

CREATE USER IF NOT EXISTS 'proyecto'@'localhost' IDENTIFIED BY 'proyecto';
GRANT ALL PRIVILEGES ON mesadeayuda.* TO 'proyecto'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS incidencias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(100) NOT NULL,
  asunto VARCHAR(255) NOT NULL,
  mensaje TEXT NOT NULL,
  estatus VARCHAR(20) NOT NULL DEFAULT 'Abierto',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

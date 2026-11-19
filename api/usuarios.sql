-- ============================================================
-- Usuarios para autenticación de la aplicación Enredados Wiki
-- ============================================================
-- Ejecuta este script en phpMyAdmin o mediante el terminal:
--   mysql -u root -p < api/usuarios.sql
-- ============================================================

CREATE TABLE IF NOT EXISTS usuarios (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  email       VARCHAR(100) NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  nombre      VARCHAR(100) NOT NULL,
  creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Opcional: inserción de un usuario de ejemplo
-- INSERT INTO usuarios (email, password, nombre) VALUES ('admin@enredados.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador');

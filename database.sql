CREATE DATABASE IF NOT EXISTS clinica_odontologica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clinica_odontologica;

-- 1. Tabla de Usuarios (Para el inicio de sesión del personal / odontólogo)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabla de Pacientes (Datos personales y de contacto)
CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    direccion TEXT,
    fecha_nacimiento DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabla de Procedimientos Odontológicos (Catálogo de servicios)
CREATE TABLE IF NOT EXISTS procedimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    costo DECIMAL(10,2) NOT NULL
);

-- 4. Tabla de Historial Clínico (Relación de atenciones a pacientes)
CREATE TABLE IF NOT EXISTS historial_clinico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    procedimiento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_atencion DATETIME DEFAULT CURRENT_TIMESTAMP,
    diagnostico TEXT NOT NULL,
    observaciones TEXT,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (procedimiento_id) REFERENCES procedimientos(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT
);

-- Inserción de un usuario administrador inicial 
-- Email: admin@odontologia.com | Contraseña: 123456
-- El hash corresponde a password_hash('123456', PASSWORD_DEFAULT)
INSERT INTO usuarios (nombre, apellido, email, clave) 
VALUES ('Administrador', 'General', 'admin@odontologia.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeT9k.X9sU8V1P5rV0vV6x6k0gK8jZ5O2')
ON DUPLICATE KEY UPDATE email=email;

-- Inserción de los procedimientos odontológicos solicitados en la tarea
INSERT INTO procedimientos (nombre, descripcion, costo) VALUES 
('Limpieza Dental', 'Remoción de placa bacteriana y cálculo dental supragingival y subgingival.', 30.00),
('Blanqueamiento Dental', 'Procedimiento estético para aclarar el tono de la dentadura.', 120.00),
('Extracción de Muela / Cordal', 'Exodoncia quirúrgica o simple de piezas dentales mulares o terceros molares.', 50.00),
('Tratamiento de Conductos (Endodoncia)', 'Extracción del tejido pulpar infectado y sellado del canal radicular.', 90.00),
('Calza de Muela (Obturación)', 'Restauración de piezas dentales afectadas por caries usando composite o amalgama.', 40.00);
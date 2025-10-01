-- Crear tabla de roles
CREATE TABLE roles (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID del rol',
    nombre VARCHAR(50) NOT NULL UNIQUE COMMENT 'Nombre del rol',
    descripcion VARCHAR(255) NULL COMMENT 'Descripción del rol',
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización'
) COMMENT 'Tabla de roles del sistema';

-- Crear tabla de usuarios
CREATE TABLE usuarios (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID del usuario',
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE COMMENT 'Nombre de usuario',
    contrasena VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada',
    correo VARCHAR(100) NOT NULL UNIQUE COMMENT 'Correo electrónico',
    rol_id INT NOT NULL COMMENT 'ID del rol del usuario',
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo' COMMENT 'Estado del usuario',
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
    ultimo_ingreso DATETIME NULL COMMENT 'Último inicio de sesión',
    CONSTRAINT fk_usuarios_roles FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
) COMMENT 'Tabla de usuarios del sistema';

-- Crear índices
CREATE INDEX idx_usuarios_nombre_usuario ON usuarios(nombre_usuario);
CREATE INDEX idx_usuarios_correo ON usuarios(correo);
CREATE INDEX idx_usuarios_rol_id ON usuarios(rol_id);
CREATE INDEX idx_usuarios_estado ON usuarios(estado);

-- Insertar roles básicos
INSERT INTO roles (nombre, descripcion) VALUES 
    ('admin', 'Administrador del sistema con acceso total'),
    ('gerente', 'Gerente con acceso a reportes y gestión'),
    ('recepcion', 'Recepcionista con acceso a reservaciones');

-- Insertar usuarios semilla
INSERT INTO usuarios (
    nombre_usuario, 
    contrasena, 
    correo, 
    rol_id, 
    estado
) VALUES 
-- Contraseña: admin123
('admin', '$2y$10$hPG2HMzMPQIx6yshnCMzKO7n5eIZV8mBh9zCnM1LUxxKPlog.qHWO', 
'admin@hotel.com', 
(SELECT id FROM roles WHERE nombre = 'admin'), 
'activo'),

-- Contraseña: gerente123
('gerente', '$2y$10$jW7E.ekqTOk8EcTnvNfde./IWW6eeGbp2aLLreZ3vo2hHFTT8.cJG', 
'gerente@hotel.com', 
(SELECT id FROM roles WHERE nombre = 'gerente'), 
'activo'),

-- Contraseña: recepcion123
('recepcion', '$2y$10$EiXsh3Ut2ee2uoKoNi6DieeP4xD.qY1IYHVL5PZdQkyULHyMFFUH6', 
'recepcion@hotel.com', 
(SELECT id FROM roles WHERE nombre = 'recepcion'), 
'activo');
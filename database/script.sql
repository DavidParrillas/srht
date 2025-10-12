-- SISTEMA DE RESERVAS HOTEL TORREMOLINOS (SRHT)
-- Script de Creación de Base de Datos MySQL
-- Versión: 1.0
-- Fecha: Octubre 2025

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS hoteltorremolinos
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE hoteltorremolinos;

-- TABLA: Rol
-- Propósito: Definir los roles del personal (Ej: Recepcionista, Administrador)
CREATE TABLE Rol (
    idRol INT AUTO_INCREMENT PRIMARY KEY,
    NombreRol VARCHAR(50) NOT NULL UNIQUE,
    DescripcionRol TEXT
    -- CHECK: (Se mueve al final con ALTER TABLE para evitar Error 1064)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- InnoDB: motor de almacenamiento que soporta transacciones y claves foráneas
-- utf8mb4: permite almacenar cualquier carácter Unicode incluidos emojis


-- TABLA: Usuario
-- Propósito: Almacenar el personal del hotel que usa el sistema
-- Relaciones: 
--    - Muchos a uno con Rol
--    - Uno a muchos con Reserva (quien registra)
CREATE TABLE Usuario (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,    
    idRol INT NOT NULL,
    NombreUsuario VARCHAR(100) NOT NULL,
    ContrasenaUsuario VARCHAR(255) NOT NULL,
    CorreoUsuario VARCHAR(100) NOT NULL UNIQUE,
    
    FOREIGN KEY (idRol) REFERENCES Rol(idRol)
        ON DELETE RESTRICT 
        -- RESTRICT: no permite eliminar un rol si tiene usuarios asignados
        ON UPDATE CASCADE,
        -- CASCADE: si cambia el ID del rol, se actualiza automáticamente aquí
    INDEX idx_email (CorreoUsuario),
    -- INDEX: acelera búsquedas por email (login)
    INDEX idx_rol (idRol)
    -- INDEX: acelera consultas que filtran por rol
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Cliente
-- Propósito: Almacenar información de huéspedes del hotel
-- Relaciones: Uno a muchos con Reserva
-- =====================================================
CREATE TABLE Cliente (
    idCliente INT AUTO_INCREMENT PRIMARY KEY,

    DuiCliente VARCHAR(10) NOT NULL UNIQUE,
    CorreoCliente VARCHAR(100) NOT NULL,
    -- Email del cliente para notificaciones y confirmaciones    
    NombreCliente VARCHAR(150) NOT NULL,
    -- Nombre completo del huésped
    -- VARCHAR(150): espacio para nombres compuestos largos 
    TelefonoCliente VARCHAR(15),
    -- CHECK: (Se mueve al final con ALTER TABLE para evitar Error 1064)
    INDEX idx_dui (DuiCliente),
    -- INDEX: búsquedas rápidas por DUI (campo más usado)
    INDEX idx_nombre (NombreCliente)
    -- INDEX: búsquedas por nombre del cliente
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Paquete
-- Propósito: Almacenar paquetes vacacionales ofrecidos
-- Relaciones: Uno a muchos con Reserva
-- =====================================================
CREATE TABLE Paquete (
    idPaquete INT AUTO_INCREMENT PRIMARY KEY,
    NombrePaquete VARCHAR(100) NOT NULL,
    -- Nombre comercial del paquete
    -- Ejemplos: "Todo Incluido", "Media Pensión", "Day Pass" 
    DescripcionPaquete TEXT NOT NULL,
    TarifaPaquete DECIMAL(10,2) NOT NULL,
    CONSTRAINT chk_tarifa_positiva CHECK (TarifaPaquete > 0),
    -- CHECK: la tarifa debe ser mayor que cero
    INDEX idx_nombre_paquete (NombrePaquete)
    -- INDEX: búsquedas rápidas de paquetes por nombre
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: TipoHabitacion
-- Propósito: Categorías de habitaciones (Simple, Doble, Suite)
-- Relaciones: Uno a muchos con Habitacion
-- =====================================================
CREATE TABLE TipoHabitacion (
    idTipoHabitacion INT AUTO_INCREMENT PRIMARY KEY,
    NombreTipoHabitacion VARCHAR(50) NOT NULL UNIQUE,
    Capacidad INT NOT NULL,
    -- Número máximo de personas que permite
    PrecioTipoHabitacion DECIMAL(10,2) NOT NULL,
    -- Precio base por noche de este tipo de habitación
    CONSTRAINT chk_capacidad CHECK (Capacidad > 0 AND Capacidad <= 10),
    -- CHECK: capacidad entre 1 y 10 personas
    CONSTRAINT chk_precio_tipo CHECK (PrecioTipoHabitacion > 0)
    -- CHECK: precio debe ser positivo
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Habitacion
-- Propósito: Habitaciones físicas específicas del hotel
-- Relaciones: 
--    - Muchos a uno con TipoHabitacion
--    - Uno a muchos con Reserva
--    - Muchos a muchos con Amenidad
-- CORRECCIÓN: 'Row3' renombrado a 'DetalleHabitacion' por claridad.
-- =====================================================
CREATE TABLE Habitacion (
    idHabitacion INT AUTO_INCREMENT PRIMARY KEY,    
    idTipoHabitacion INT NOT NULL,
    -- FK: tipo de habitación (Simple, Doble, Suite, etc.)    
    NumeroHabitacion VARCHAR(10) NOT NULL UNIQUE,
    -- UNIQUE: no pueden haber dos habitaciones con el mismo número
    EstadoHabitacion ENUM('Disponible', 'Ocupada', 'Mantenimiento', 'Fuera de Servicio')  
        NOT NULL DEFAULT 'Disponible',
    DetalleHabitacion VARCHAR(50), -- Renombrado
    
    FOREIGN KEY (idTipoHabitacion) REFERENCES TipoHabitacion(idTipoHabitacion)
        ON DELETE RESTRICT 
        -- No permite eliminar un tipo si tiene habitaciones asignadas
        ON UPDATE CASCADE,
        -- Actualiza automáticamente si cambia el ID del tipo
    INDEX idx_numero (NumeroHabitacion),
    -- INDEX: búsquedas rápidas por número de habitación
    INDEX idx_estado (EstadoHabitacion),
    -- INDEX: filtros de disponibilidad
    INDEX idx_tipo (idTipoHabitacion)
    -- INDEX: consultas por tipo de habitación
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Amenidad
-- Propósito: Amenidades/servicios adicionales disponibles
-- =====================================================
CREATE TABLE Amenidad (
    idAmenidad INT AUTO_INCREMENT PRIMARY KEY,
    -- ID único para cada amenidad
    nombreAmenidad VARCHAR(100) NOT NULL,
    Descripcion VARCHAR(255)
    -- Nombre de la amenidad ("Vista al Mar", "Jacuzzi", "Balcón")
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: HabitacionAmenidad (Tabla Intermedia)
-- Propósito: Relación muchos a muchos entre Habitacion y Amenidad
-- Una habitación puede tener muchas amenidades
-- Una amenidad puede estar en muchas habitaciones
-- =====================================================
CREATE TABLE HabitacionAmenidad (
    idHabitacion INT NOT NULL,
    idAmenidad INT NOT NULL,
    -- FK: amenidad específica
    PRIMARY KEY (idHabitacion, idAmenidad),
    -- PRIMARY KEY compuesta: combinación única de habitación + amenidad
    -- Evita duplicados (no puede asignarse la misma amenidad dos veces)
    FOREIGN KEY (idHabitacion) REFERENCES Habitacion(idHabitacion)
        ON DELETE CASCADE 
        -- CASCADE: si se elimina habitación, se eliminan sus amenidades
        ON UPDATE CASCADE,
    FOREIGN KEY (idAmenidad) REFERENCES Amenidad(idAmenidad)
        ON DELETE CASCADE 
        -- CASCADE: si se elimina amenidad, se eliminan todas sus asignaciones
        ON UPDATE CASCADE,
    INDEX idx_habitacion (idHabitacion),
    INDEX idx_amenidad (idAmenidad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Reserva
-- Propósito: Reservaciones realizadas en el hotel (TABLA CENTRAL)
-- Relaciones: 
--    - Muchos a uno con Cliente (quien reserva)
--    - Muchos a uno con Paquete (paquete contratado)
--    - Muchos a uno con Habitacion (habitación asignada)
--    - Uno a muchos con Pago
-- =====================================================
CREATE TABLE Reserva (
    idReserva INT AUTO_INCREMENT PRIMARY KEY,
    idCliente INT NOT NULL,
    idPaquete INT NOT NULL,
    -- FK: paquete vacacional contratado
    -- NOT NULL: toda reserva debe incluir un paquete
    idHabitacion INT NOT NULL,
    -- FK: habitación asignada a la reserva
    -- NOT NULL: toda reserva debe tener habitación asignada
    EstadoReserva ENUM('Pendiente', 'Confirmada', 'Cancelada', 'Completada')  
        NOT NULL DEFAULT 'Pendiente',
    FechaEntrada DATE NOT NULL,
    FechaSalida DATE NOT NULL,
    Comentario TEXT,
    -- Campo para registrar incidencias, solicitudes especiales, notas
    -- Nullable: no todas las reservas tienen comentarios
    TotalReservacion DECIMAL(10,2) NOT NULL,
    -- Monto total de la reserva
    -- Se calcula: (días * precio habitación) + tarifa paquete
    
    FOREIGN KEY (idCliente) REFERENCES Cliente(idCliente)
        ON DELETE RESTRICT 
        -- No permite eliminar cliente si tiene reservas
        ON UPDATE CASCADE,
    FOREIGN KEY (idPaquete) REFERENCES Paquete(idPaquete)
        ON DELETE RESTRICT 
        -- No permite eliminar paquete si tiene reservas activas
        ON UPDATE CASCADE,
    FOREIGN KEY (idHabitacion) REFERENCES Habitacion(idHabitacion)
        ON DELETE RESTRICT 
        -- No permite eliminar habitación si tiene reservas
        ON UPDATE CASCADE,
    CONSTRAINT chk_fechas CHECK (FechaSalida > FechaEntrada),
    -- CHECK: fecha de salida debe ser posterior a fecha de entrada
    CONSTRAINT chk_total CHECK (TotalReservacion > 0),
    -- CHECK: total debe ser positivo
    INDEX idx_cliente (idCliente),
    -- INDEX: búsquedas de reservas por cliente
    INDEX idx_fechas (FechaEntrada, FechaSalida),
    -- INDEX: filtros por rango de fechas (reportes de ocupación)
    INDEX idx_estado (EstadoReserva),
    -- INDEX: filtros por estado de reserva
    INDEX idx_habitacion (idHabitacion)
    -- INDEX: consultas de reservas por habitación
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: Pago
-- Propósito: Registrar pagos realizados por reservas
-- Relaciones: Muchos a uno con Reserva
-- =====================================================
CREATE TABLE Pago (
    idPago INT AUTO_INCREMENT PRIMARY KEY,
    idReserva INT NOT NULL,
    MontoPago DECIMAL(10,2) NOT NULL,
    FormaPago ENUM('Efectivo', 'Tarjeta', 'Transferencia', 'Cheque') NOT NULL,  
    ComentarioPago TEXT,
    -- Notas sobre el pago: número de autorización, banco, observaciones
    -- Nullable: no todos los pagos requieren comentarios
    
    FOREIGN KEY (idReserva) REFERENCES Reserva(idReserva)
        ON DELETE CASCADE 
        -- CASCADE: si se elimina reserva, se eliminan sus pagos
        ON UPDATE CASCADE,
    CONSTRAINT chk_monto_pago CHECK (MontoPago > 0),
    -- CHECK: monto del pago debe ser positivo
    INDEX idx_reserva (idReserva),
    -- INDEX: búsquedas de pagos por reserva
    INDEX idx_forma_pago (FormaPago)
    -- INDEX: reportes financieros por forma de pago
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- RESTRICCIONES CHECK AGREGADAS VÍA ALTER TABLE
-- =====================================================

-- Restricción para Rol: nombre con longitud mínima
ALTER TABLE Rol 
ADD CONSTRAINT chk_nombrerol CHECK (LENGTH(NombreRol) >= 3);

-- Restricción para Cliente: valida formato de DUI
ALTER TABLE Cliente 
ADD CONSTRAINT chk_dui_format CHECK (DuiCliente REGEXP '^[0-9]{8}-[0-9]$');

-- DATOS INICIALES PRUEBA

-- Insertar roles básicos del sistema
INSERT INTO Rol (NombreRol, DescripcionRol) VALUES
('Recepción', 'Personal de recepción encargado de registro de clientes y reservas'),
('Gerencia', 'Gerente con acceso completo al sistema y reportes'),
('Subgerencia', 'Subgerente con permisos de gestión y reportes'),
('Administrador', 'Administrador del sistema con todos los permisos');

-- Insertar tipos de habitación
INSERT INTO TipoHabitacion (NombreTipoHabitacion, Capacidad, PrecioTipoHabitacion) VALUES
('Simple', 1, 50.00),
('Doble', 2, 80.00),
('Suite', 4, 150.00),
('Presidencial', 6, 300.00);

-- Insertar paquetes vacacionales
INSERT INTO Paquete (NombrePaquete, DescripcionPaquete, TarifaPaquete) VALUES
('Day Pass', 'Acceso a instalaciones por un día, incluye almuerzo', 25.00),
('Media Pensión', 'Incluye desayuno y cena', 40.00),
('Todo Incluido', 'Todas las comidas, bebidas y servicios incluidos', 75.00);

-- Insertar amenidades comunes
INSERT INTO Amenidad (NombreAmenidad, Descripcion) VALUES
('Vista al Mar', 'Habitación con vista panorámica al océano'),
('Jacuzzi', 'Jacuzzi privado en la habitación'),
('Balcón', 'Balcón privado con mobiliario'),
('Cocina Completa', 'Cocina equipada con electrodomésticos'),
('TV Smart', 'Televisor inteligente con streaming');
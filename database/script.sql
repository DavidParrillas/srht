-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: hoteltorremolinos
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.24.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Amenidad`
--

DROP TABLE IF EXISTS `Amenidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Amenidad` (
  `idAmenidad` int NOT NULL AUTO_INCREMENT,
  `nombreAmenidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idAmenidad`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Amenidad`
--

/*!40000 ALTER TABLE `Amenidad` DISABLE KEYS */;
INSERT INTO `Amenidad` VALUES (1,'Vista al Mar','Habitación con vista panorámica al océano'),(2,'Jacuzzi','Jacuzzi privado en la habitación'),(3,'Balcón','Balcón privado con mobiliario'),(4,'Cocina Completa','Cocina equipada con electrodomésticos'),(5,'TV Smart','Televisor inteligente con streaming'),(7,'Cama King Size','Habitación con cama extra grande'),(8,'Servicio de Lavandería','Acceso a servicio de lavandería express'),(9,'Mascota Bienvenida','Permitido el alojamiento con mascotas pequeñas');
/*!40000 ALTER TABLE `Amenidad` ENABLE KEYS */;

--
-- Table structure for table `Cliente`
--

DROP TABLE IF EXISTS `Cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cliente` (
  `idCliente` int NOT NULL AUTO_INCREMENT,
  `DuiCliente` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `CorreoCliente` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NombreCliente` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TelefonoCliente` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  UNIQUE KEY `DuiCliente` (`DuiCliente`),
  KEY `idx_dui` (`DuiCliente`),
  KEY `idx_nombre` (`NombreCliente`),
  CONSTRAINT `chk_dui_format` CHECK (regexp_like(`DuiCliente`,_utf8mb4'^[0-9]{8}-[0-9]$'))
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Cliente`
--

/*!40000 ALTER TABLE `Cliente` DISABLE KEYS */;
INSERT INTO `Cliente` VALUES (1,'12345678-9','juan.perez@email.com','Juan Perez Lopez','77001122'),(2,'98777732-1','ana.gomez@email.com','Ana Gomez Castillo','78993344'),(3,'11223344-5','carlos.rodriguez@email.com','Carlos Alberto Rodriguez','78934567'),(4,'55667788-0','maria.lopez@email.com','Maria Lopez Herrera','60115566'),(5,'99445656-4','josep@hotmail.com','José Armando Paredes','75459656'),(6,'46458195-5','FPortillo@outlook.com','Fatima Portillo','78494545'),(7,'79898163-3','lmartinez.014@gmail.com','Luis Martinez','79465412'),(8,'05390059-5','eportillo29@gmail.com','Erick Portillo','72001050'),(9,'34324343-3','pedro@gmial.com','Pedro','12321323'),(10,'23532453-5','marco@gmail.com','Marcos Antonio Solis','234324325');
/*!40000 ALTER TABLE `Cliente` ENABLE KEYS */;

--
-- Table structure for table `Habitacion`
--

DROP TABLE IF EXISTS `Habitacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Habitacion` (
  `idHabitacion` int NOT NULL AUTO_INCREMENT,
  `idTipoHabitacion` int NOT NULL,
  `NumeroHabitacion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `EstadoHabitacion` enum('Disponible','Ocupada','Mantenimiento','Fuera de Servicio') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `DetalleHabitacion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idHabitacion`),
  UNIQUE KEY `NumeroHabitacion` (`NumeroHabitacion`),
  KEY `idx_numero` (`NumeroHabitacion`),
  KEY `idx_estado` (`EstadoHabitacion`),
  KEY `idx_tipo` (`idTipoHabitacion`),
  CONSTRAINT `Habitacion_ibfk_1` FOREIGN KEY (`idTipoHabitacion`) REFERENCES `TipoHabitacion` (`idTipoHabitacion`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Habitacion`
--

/*!40000 ALTER TABLE `Habitacion` DISABLE KEYS */;
INSERT INTO `Habitacion` VALUES (2,4,'102','Ocupada','Habitación con vista al jardín'),(3,1,'103','Ocupada','Necesita reparación de aire acondicionado'),(4,1,'201','Disponible','Habitación con balcón pequeño'),(5,4,'202','Disponible','Vista a la piscina'),(6,4,'203','Disponible','Cama matrimonial, vista a la calle'),(7,2,'301','Disponible','Dos camas individuales'),(8,2,'302','Disponible','Problemas en el baño'),(9,3,'401','Disponible','Con sala de estar y minibar'),(10,3,'402','Disponible','Suite con vista panorámica'),(11,4,'501','Disponible','La más lujosa, con jacuzzi privado'),(12,1,'101','Ocupada',''),(13,4,'485','Ocupada',''),(14,2,'444','Ocupada',''),(15,3,'787','Mantenimiento','');
/*!40000 ALTER TABLE `Habitacion` ENABLE KEYS */;

--
-- Table structure for table `HabitacionAmenidad`
--

DROP TABLE IF EXISTS `HabitacionAmenidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `HabitacionAmenidad` (
  `idHabitacion` int NOT NULL,
  `idAmenidad` int NOT NULL,
  PRIMARY KEY (`idHabitacion`,`idAmenidad`),
  KEY `idx_habitacion` (`idHabitacion`),
  KEY `idx_amenidad` (`idAmenidad`),
  CONSTRAINT `HabitacionAmenidad_ibfk_1` FOREIGN KEY (`idHabitacion`) REFERENCES `Habitacion` (`idHabitacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `HabitacionAmenidad_ibfk_2` FOREIGN KEY (`idAmenidad`) REFERENCES `Amenidad` (`idAmenidad`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `HabitacionAmenidad`
--

/*!40000 ALTER TABLE `HabitacionAmenidad` DISABLE KEYS */;
INSERT INTO `HabitacionAmenidad` VALUES (2,1),(2,5),(4,3),(4,5),(5,1),(5,9),(6,7),(7,8),(9,3),(9,4),(9,5),(9,7),(10,1),(10,2),(10,7),(11,1),(11,2),(11,3),(11,4),(11,5),(11,7),(11,8),(11,9),(12,4),(13,7),(13,8),(14,2),(14,4),(14,9),(15,2),(15,3),(15,4),(15,7);
/*!40000 ALTER TABLE `HabitacionAmenidad` ENABLE KEYS */;

--
-- Table structure for table `Pago`
--

DROP TABLE IF EXISTS `Pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Pago` (
  `idPago` int NOT NULL AUTO_INCREMENT,
  `idReserva` int NOT NULL,
  `TipoTransaccion` enum('Depósito','Pago Final','Pago Único','Abono','Pago Transferido','Abono Diferencia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `FechaPago` datetime NOT NULL,
  `MontoPago` decimal(10,2) NOT NULL,
  `FormaPago` enum('Efectivo','Tarjeta','Transferencia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Comprobante` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ComentarioPago` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idPago`),
  KEY `idx_reserva` (`idReserva`),
  KEY `idx_forma_pago` (`FormaPago`),
  CONSTRAINT `Pago_ibfk_1` FOREIGN KEY (`idReserva`) REFERENCES `Reserva` (`idReserva`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_monto_pago` CHECK ((`MontoPago` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pago`
--

/*!40000 ALTER TABLE `Pago` DISABLE KEYS */;
INSERT INTO `Pago` VALUES (1,2,'Pago Único','2025-11-05 20:11:00',420.00,'Tarjeta','CC-TEST-201',NULL),(2,3,'Depósito','2025-11-05 20:16:00',115.00,'Transferencia','TR-TEST-202',NULL),(3,4,'Pago Único','2025-11-04 13:55:00',170.00,'Efectivo',NULL,NULL),(4,5,'Pago Único','2025-10-01 15:01:00',170.00,'Efectivo',NULL,NULL),(5,6,'Depósito','2025-11-01 08:05:00',177.50,'Transferencia',NULL,NULL),(6,7,'Pago Único','2025-11-05 10:05:00',580.00,'Tarjeta',NULL,NULL),(7,10,'Depósito','2025-11-06 20:11:56',575.00,'Transferencia','34224324',NULL),(8,1,'Abono','2025-11-06 21:08:36',44.00,'Transferencia','',NULL),(9,1,'Abono','2025-11-06 21:08:48',11.00,'Transferencia','3456234',NULL),(10,12,'Depósito','2025-11-06 21:09:53',2130.00,'Transferencia','23422432',NULL),(11,13,'Pago Único','2025-11-06 21:11:58',2900.00,'Tarjeta','45533',NULL);
/*!40000 ALTER TABLE `Pago` ENABLE KEYS */;

--
-- Table structure for table `Paquete`
--

DROP TABLE IF EXISTS `Paquete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Paquete` (
  `idPaquete` int NOT NULL AUTO_INCREMENT,
  `NombrePaquete` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `DescripcionPaquete` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TarifaPaquete` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idPaquete`),
  KEY `idx_nombre_paquete` (`NombrePaquete`),
  CONSTRAINT `chk_tarifa_positiva` CHECK ((`TarifaPaquete` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Paquete`
--

/*!40000 ALTER TABLE `Paquete` DISABLE KEYS */;
INSERT INTO `Paquete` VALUES (1,'Solo habitación','Uso de las instalaciones y servicios básicos (incluye costo de tarifa por servicios)',10.00),(3,'Media Pensión','Incluye desayuno y cena',40.00),(4,'Todo Incluido','Todas las comidas, bebidas y servicios incluidos',75.00);
/*!40000 ALTER TABLE `Paquete` ENABLE KEYS */;

--
-- Table structure for table `Reserva`
--

DROP TABLE IF EXISTS `Reserva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Reserva` (
  `idReserva` int NOT NULL AUTO_INCREMENT,
  `idCliente` int NOT NULL,
  `idPaquete` int NOT NULL,
  `idHabitacion` int NOT NULL,
  `CantidadPersonas` int NOT NULL DEFAULT '1',
  `EstadoReserva` enum('Pendiente','Confirmada','Cancelada','Completada','En Curso') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `EstadoPago` enum('Pendiente','Parcial','Completado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `FechaEntrada` date NOT NULL,
  `FechaSalida` date NOT NULL,
  `CheckIn` datetime DEFAULT NULL,
  `CheckOut` datetime DEFAULT NULL,
  `Comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `PrecioHabitacion` decimal(10,2) NOT NULL DEFAULT '0.00',
  `PrecioPaquete` decimal(10,2) NOT NULL DEFAULT '0.00',
  `TotalReservacion` decimal(10,2) NOT NULL,
  `FechaCreacion` datetime NOT NULL,
  `FechaCancelacion` datetime DEFAULT NULL,
  `RegistroCambio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idReserva`),
  KEY `idPaquete` (`idPaquete`),
  KEY `idx_cliente` (`idCliente`),
  KEY `idx_fechas` (`FechaEntrada`,`FechaSalida`),
  KEY `idx_estado` (`EstadoReserva`),
  KEY `idx_habitacion` (`idHabitacion`),
  CONSTRAINT `Reserva_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `Cliente` (`idCliente`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `Reserva_ibfk_2` FOREIGN KEY (`idPaquete`) REFERENCES `Paquete` (`idPaquete`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `Reserva_ibfk_3` FOREIGN KEY (`idHabitacion`) REFERENCES `Habitacion` (`idHabitacion`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_cantidad_personas` CHECK (((`CantidadPersonas` > 0) and (`CantidadPersonas` <= 10))),
  CONSTRAINT `chk_fechas` CHECK ((`FechaSalida` > `FechaEntrada`)),
  CONSTRAINT `chk_total` CHECK ((`TotalReservacion` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reserva`
--

/*!40000 ALTER TABLE `Reserva` DISABLE KEYS */;
INSERT INTO `Reserva` VALUES (1,1,1,3,1,'Confirmada','Completado','2025-11-10','2025-11-11',NULL,NULL,'Reserva creada sin pago inicial. Pendiente de pago.',45.00,10.00,55.00,'2025-11-05 20:00:00',NULL,NULL),(2,2,4,9,2,'Confirmada','Completado','2025-11-20','2025-11-22',NULL,NULL,'Pagado en su totalidad con tarjeta.',135.00,75.00,420.00,'2025-11-05 20:10:00',NULL,NULL),(3,3,3,5,2,'En Curso','Parcial','2025-11-12','2025-11-14','2025-11-06 19:55:35',NULL,'Abonó 50%. Pagará el resto al llegar.',75.00,40.00,230.00,'2025-11-05 20:15:00',NULL,NULL),(4,4,1,6,2,'En Curso','Completado','2025-11-04','2025-11-06','2025-11-04 14:00:00',NULL,'Cliente pagó el total en efectivo al llegar.',75.00,10.00,170.00,'2025-11-04 13:50:00',NULL,NULL),(5,5,1,7,2,'Completada','Completado','2025-10-01','2025-10-03','2025-10-01 15:00:00','2025-10-03 11:00:00','Reserva histórica.',75.00,10.00,170.00,'2025-09-30 09:00:00',NULL,NULL),(6,6,4,2,4,'Cancelada','Parcial','2025-11-15','2025-11-16',NULL,NULL,'--- CANCELACIÓN (04-11-2025 10:00 por Sistema) ---\nCliente canceló por motivos de salud.',280.00,75.00,355.00,'2025-11-01 08:00:00','2025-11-04 10:00:00',NULL),(7,7,1,11,1,'En Curso','Completado','2025-11-05','2025-11-07','2025-11-06 18:57:10',NULL,'Cliente llega hoy. Ya pagó todo.',280.00,10.00,580.00,'2025-11-05 10:00:00',NULL,NULL),(8,9,1,6,1,'Pendiente','Pendiente','2025-11-07','2025-11-20',NULL,NULL,'',75.00,10.00,1105.00,'2025-11-06 19:13:43',NULL,NULL),(9,9,3,8,4,'Pendiente','Pendiente','2025-11-13','2025-11-24',NULL,NULL,'',75.00,40.00,1265.00,'2025-11-06 20:07:46',NULL,NULL),(10,1,3,5,3,'Confirmada','Parcial','2025-11-18','2025-11-28',NULL,NULL,'',75.00,40.00,1150.00,'2025-11-06 20:11:56',NULL,NULL),(11,1,3,4,1,'Pendiente','Pendiente','2025-11-08','2025-11-27',NULL,NULL,'',45.00,40.00,1615.00,'2025-11-06 21:07:54',NULL,NULL),(12,2,4,2,4,'Confirmada','Parcial','2025-11-15','2025-11-27',NULL,NULL,'',280.00,75.00,4260.00,'2025-11-06 21:09:53',NULL,NULL),(13,10,1,15,3,'Confirmada','Completado','2025-11-07','2025-11-27',NULL,NULL,'',135.00,10.00,2900.00,'2025-11-06 21:11:58',NULL,NULL);
/*!40000 ALTER TABLE `Reserva` ENABLE KEYS */;

--
-- Table structure for table `Rol`
--

DROP TABLE IF EXISTS `Rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Rol` (
  `idRol` int NOT NULL AUTO_INCREMENT,
  `NombreRol` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `DescripcionRol` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idRol`),
  UNIQUE KEY `NombreRol` (`NombreRol`),
  CONSTRAINT `chk_nombrerol` CHECK ((length(`NombreRol`) >= 3))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Rol`
--

/*!40000 ALTER TABLE `Rol` DISABLE KEYS */;
INSERT INTO `Rol` VALUES (1,'Recepción','Personal de recepción encargado de registro de clientes y reservas'),(2,'Gerencia','Gerente con acceso completo al sistema y reportes'),(3,'Subgerencia','Subgerente con permisos de gestión y reportes'),(4,'Administrador','Administrador del sistema con todos los permisos');
/*!40000 ALTER TABLE `Rol` ENABLE KEYS */;

--
-- Table structure for table `TipoHabitacion`
--

DROP TABLE IF EXISTS `TipoHabitacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoHabitacion` (
  `idTipoHabitacion` int NOT NULL AUTO_INCREMENT,
  `NombreTipoHabitacion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Capacidad` int NOT NULL,
  `PrecioTipoHabitacion` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idTipoHabitacion`),
  UNIQUE KEY `NombreTipoHabitacion` (`NombreTipoHabitacion`),
  CONSTRAINT `chk_capacidad` CHECK (((`Capacidad` > 0) and (`Capacidad` <= 10))),
  CONSTRAINT `chk_precio_tipo` CHECK ((`PrecioTipoHabitacion` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoHabitacion`
--

/*!40000 ALTER TABLE `TipoHabitacion` DISABLE KEYS */;
INSERT INTO `TipoHabitacion` VALUES (1,'Simple',1,45.00),(2,'Doble',2,75.00),(3,'Suite',4,135.00),(4,'Presidencial',6,280.00);
/*!40000 ALTER TABLE `TipoHabitacion` ENABLE KEYS */;

--
-- Table structure for table `Usuario`
--

DROP TABLE IF EXISTS `Usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Usuario` (
  `idUsuario` int NOT NULL AUTO_INCREMENT,
  `idRol` int NOT NULL,
  `NombreUsuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ContrasenaUsuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `CorreoUsuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `CorreoUsuario` (`CorreoUsuario`),
  KEY `idx_email` (`CorreoUsuario`),
  KEY `idx_rol` (`idRol`),
  CONSTRAINT `Usuario_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `Rol` (`idRol`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Usuario`
--

/*!40000 ALTER TABLE `Usuario` DISABLE KEYS */;
INSERT INTO `Usuario` VALUES (1,1,'recepcion','$2y$10$9Gv/t/gX/gX/gX/gX/gX/gX/gX/gX/gX/gX/gX/gX/gX/gX/gX/gX/gX','recepcion@example.com'),(3,4,'Administrador','$2y$10$qpIadezHoaOYg/3pVThHw.LXyxhTlGrDykma.AHdn2.8WRrTXi6OK','admin@torremolinos.com'),(4,4,'Erick','$2a$12$K.VgMP/Ahg.d5Eclo0EHBuwNFL8kwPKuA1uOkKO4ESN0hh9m5ueL6','erick@hotelterremolinos.com'),(5,4,'adminTorremolinos','$2y$10$P66vcqFflKoCTksy1dQNGu9jpgg6CcQ6l/oH.ATY/Akf9z0PEoZn6','admin@gmail.com'),(6,2,'Gerencia','$2y$10$xlV60DImAoBIhCosOc5UDewBjp/LSPHuUd9ZOEky1wXmcxDwmFIJ2','gerente@gmial.com'),(7,1,'Recepción','$2y$10$uU0QFzmDrSnwJ5zEMWx8Qe/In.XLt0NhFEout.pNbQI.IKRjyATji','recepcion@gmail.com'),(8,4,'Eladmin','$2y$10$eblW/I4seqv4jD6OZDMxtem7cp8auOHJHw9wJwP8g5Cx6pkGpBCb2','eladmin@gmial.com'),(10,1,'Recepción Torremolinos','$2y$10$kxdu2MtDIAv54GFeG8aCy.6E.LccL1gneZn5HptWSxyOirq6Nybri','recepcionTorremolinos@gmail.com'),(11,2,'SubGerencia','$2y$10$vXDUPsoT1M6HoRsXrOXO3.b82C2./wgfIkSbY8PcHUjuVnTVNm2P2','subgerencia@gmail.com'),(13,2,'Gerente','$2y$10$41esdWFSJQjeH8sYlLWsQOreWiD0nowpJzdNRmyBhnb6ZE9cIMAfK','gerente@torremolinos.com'),(14,1,'Recepción','$2y$10$V7SeA7ZMYf9tCtXJB4aTOOHsnirnn1ENzRcdcDHv1lLObYVVU3QNu','recepcion@torremolinos.com');
/*!40000 ALTER TABLE `Usuario` ENABLE KEYS */;

--
-- Dumping routines for database 'hoteltorremolinos'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-07 22:12:18

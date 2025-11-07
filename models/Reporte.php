<?php
/**
 * Modelo Reporte
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona las consultas complejas para la generación de reportes gerenciales.
 */

class Reporte {
    
    private $conexion;
    
    /**
     * Constructor
     * @param PDO $db Conexión a la base de datos
     */
    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Genera un reporte de ocupación por tipo de habitación en un rango de fechas.
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array|false
     */
    public function getReporteOcupacion($fechaInicio, $fechaFin) {
        try {
            // CORRECCIÓN: Se cambió el filtro de fecha para que considere la superposición de rangos,
            // no solo la fecha de inicio. Se añadió LEAST y GREATEST para contar solo las noches
            // que caen DENTRO del período del reporte. Se aseguró que DATEDIFF no sea cero.
            $query = "
                SELECT 
                    th.NombreTipoHabitacion,
                    COUNT(r.idReserva) AS NumeroReservas,
                    SUM(DATEDIFF(LEAST(r.FechaSalida, :fechaFin), GREATEST(r.FechaEntrada, :fechaInicio))) AS NochesVendidas,
                    AVG(r.TotalReservacion / GREATEST(DATEDIFF(r.FechaSalida, r.FechaEntrada), 1)) AS TarifaPromedio
                FROM Reserva r
                JOIN Habitacion h ON r.idHabitacion = h.idHabitacion
                JOIN TipoHabitacion th ON h.idTipoHabitacion = th.idTipoHabitacion
                WHERE r.FechaEntrada <= :fechaFin_dup 
                  AND r.FechaSalida >= :fechaInicio_dup
                  AND r.EstadoReserva IN ('Confirmada', 'En Curso', 'Completada')
                GROUP BY th.NombreTipoHabitacion
                ORDER BY NochesVendidas DESC;
            ";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':fechaInicio', $fechaInicio);
            $stmt->bindParam(':fechaFin', $fechaFin);
            // Se usan duplicados porque no se puede usar el mismo marcador de posición dos veces en algunas lógicas de PDO.
            $stmt->bindParam(':fechaInicio_dup', $fechaInicio);
            $stmt->bindParam(':fechaFin_dup', $fechaFin);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en reporte de ocupación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Genera un reporte de ingresos por paquete y forma de pago en un rango de fechas.
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array|false
     */
    public function getReporteIngresos($fechaInicio, $fechaFin) {
        try {
            // CORRECCIÓN: Se cambió a LEFT JOIN para incluir pagos de reservas sin paquete.
            // Se usa COALESCE para mostrar 'Sin Paquete' si idPaquete es NULL.
            // El filtro de fecha ahora se basa en la fecha del PAGO (pa.FechaPago), que es más relevante para ingresos.
            $query = "
                SELECT 
                    COALESCE(p.NombrePaquete, 'Sin Paquete') AS NombrePaquete,
                    pa.FormaPago,
                    COUNT(pa.idPago) AS NumeroPagos,
                    SUM(pa.MontoPago) AS TotalIngresos
                FROM Pago pa
                JOIN Reserva r ON pa.idReserva = r.idReserva -- Corregido aquí
                LEFT JOIN Paquete p ON r.idPaquete = p.idPaquete
                WHERE pa.FechaPago >= :fechaInicio 
                  AND pa.FechaPago < DATE_ADD(:fechaFin, INTERVAL 1 DAY)
                GROUP BY p.NombrePaquete, pa.FormaPago
                ORDER BY TotalIngresos DESC;
            ";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':fechaInicio', $fechaInicio);
            $stmt->bindParam(':fechaFin', $fechaFin);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en reporte de ingresos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un historial de reservaciones en un rango de fechas.
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array|false
     */
    public function getHistorialReservaciones($fechaInicio, $fechaFin) {
        try {
            // CORRECCIÓN: Se cambió a LEFT JOIN para incluir reservas sin paquete.
            // Se usa COALESCE para mostrar 'Sin Paquete'.
            // Se corrigió el filtro de fecha para usar superposición de rangos.
            $query = "
                SELECT r.*, c.NombreCliente, h.NumeroHabitacion, p.NombrePaquete
                FROM Reserva r
                JOIN Cliente c ON r.idCliente = c.idCliente
                JOIN Habitacion h ON r.idHabitacion = h.idHabitacion
                LEFT JOIN Paquete p ON r.idPaquete = p.idPaquete
                WHERE r.FechaEntrada <= :fechaFin 
                  AND r.FechaSalida >= :fechaInicio
                ORDER BY r.FechaEntrada DESC;
            ";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':fechaInicio', $fechaInicio);
            $stmt->bindParam(':fechaFin', $fechaFin);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en historial de reservaciones: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene las estadísticas clave para el Dashboard.
     * @return array|false
     */
    public function getDashboardStats() {
        try {
            $stats = [
                'total_clientes' => 0,
                'total_usuarios' => 0,
                'total_habitaciones' => 0,
                'total_paquetes' => 0,
                'total_reservaciones' => 0,
            ];

            // 1. Total de Clientes
            $stmt = $this->conexion->query("SELECT COUNT(idCliente) FROM Cliente");
            $stats['total_clientes'] = $stmt->fetchColumn();

            // 2. Total de Usuarios
            $stmt = $this->conexion->query("SELECT COUNT(idUsuario) FROM Usuario");
            $stats['total_usuarios'] = $stmt->fetchColumn();

            // 3. Total de Habitaciones
            $stmt = $this->conexion->query("SELECT COUNT(idHabitacion) FROM Habitacion");
            $stats['total_habitaciones'] = $stmt->fetchColumn();

            // 4. Total de Paquetes
            $stmt = $this->conexion->query("SELECT COUNT(idPaquete) FROM Paquete");
            $stats['total_paquetes'] = $stmt->fetchColumn();

            // 5. Total de Reservaciones
            $stmt = $this->conexion->query("SELECT COUNT(idReserva) FROM Reserva");
            $stats['total_reservaciones'] = $stmt->fetchColumn();

            return $stats;

        } catch (PDOException $e) {
            error_log("Error en getDashboardStats: " . $e->getMessage());
            return false;
        }
    }
}
?>

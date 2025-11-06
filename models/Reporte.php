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
            $query = "
                SELECT 
                    th.NombreTipoHabitacion,
                    COUNT(r.idReserva) AS NumeroReservas,
                    SUM(DATEDIFF(r.FechaSalida, r.FechaEntrada)) AS NochesVendidas,
                    AVG(r.TotalReservacion / DATEDIFF(r.FechaSalida, r.FechaEntrada)) AS TarifaPromedio
                FROM Reserva r
                JOIN Habitacion h ON r.idHabitacion = h.idHabitacion
                JOIN TipoHabitacion th ON h.idTipoHabitacion = th.idTipoHabitacion
                WHERE r.FechaEntrada BETWEEN :fechaInicio AND :fechaFin
                AND r.EstadoReserva IN ('Confirmada', 'Completada')
                GROUP BY th.NombreTipoHabitacion
                ORDER BY NochesVendidas DESC;
            ";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':fechaInicio', $fechaInicio);
            $stmt->bindParam(':fechaFin', $fechaFin);
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
            $query = "
                SELECT 
                    p.NombrePaquete,
                    pa.FormaPago,
                    COUNT(pa.idPago) AS NumeroPagos,
                    SUM(pa.MontoPago) AS TotalIngresos
                FROM Pago pa
                JOIN Reserva r ON pa.idReserva = r.idReserva
                JOIN Paquete p ON r.idPaquete = p.idPaquete
                WHERE r.FechaEntrada BETWEEN :fechaInicio AND :fechaFin
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
            $query = "
                SELECT r.*, c.NombreCliente, h.NumeroHabitacion, p.NombrePaquete
                FROM Reserva r
                JOIN Cliente c ON r.idCliente = c.idCliente
                JOIN Habitacion h ON r.idHabitacion = h.idHabitacion
                JOIN Paquete p ON r.idPaquete = p.idPaquete
                WHERE r.FechaEntrada BETWEEN :fechaInicio AND :fechaFin
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
}
?>

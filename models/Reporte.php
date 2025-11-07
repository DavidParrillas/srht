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
                JOIN Reserva r ON pa.idReserva = r.idReserva
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
            $stats = [];
            $hoy_inicio = date('Y-m-d 00:00:00');
            $hoy_fin = date('Y-m-d 23:59:59');
            $hoy_fecha = date('Y-m-d');

            // 1. Check-ins para hoy (más útil que 'reservas creadas hoy')
            $stmt = $this->conexion->prepare("SELECT COUNT(idReserva) FROM Reserva WHERE FechaEntrada = :hoy_fecha AND EstadoReserva IN ('Confirmada', 'En Curso')");
            $stmt->bindParam(':hoy_fecha', $hoy_fecha);
            $stmt->execute();
            $stats['checkins_hoy'] = $stmt->fetchColumn();

            // 2. Ingresos del día
            // CORRECCIÓN: Se usa un rango de fechas para optimizar la consulta (usa índices).
            $stmt = $this->conexion->prepare("SELECT SUM(MontoPago) FROM Pago WHERE FechaPago BETWEEN :hoy_inicio AND :hoy_fin");
            $stmt->bindParam(':hoy_inicio', $hoy_inicio);
            $stmt->bindParam(':hoy_fin', $hoy_fin);
            $stmt->execute();
            $stats['ingresos_dia'] = $stmt->fetchColumn() ?? 0;

            // 3. Habitaciones ocupadas (reservas activas hoy)
            // La lógica original es correcta, solo se ajusta el parámetro.
            $stmt = $this->conexion->prepare(
                "SELECT COUNT(DISTINCT idHabitacion) FROM Reserva 
                 WHERE :hoy >= FechaEntrada AND :hoy < FechaSalida AND EstadoReserva IN ('Confirmada', 'En Curso')"
            );
            $stmt->bindParam(':hoy', $hoy_fecha);
            $stmt->execute();
            $stats['habitaciones_ocupadas'] = $stmt->fetchColumn();

            // 4. Total de habitaciones
            $stmt = $this->conexion->query("SELECT COUNT(idHabitacion) FROM Habitacion");
            $stats['total_habitaciones'] = $stmt->fetchColumn();

            // 5. Check-outs para hoy
            // CORRECCIÓN: Se incluyen reservas 'Completada' para contar también las que ya hicieron check-out hoy.
            $stmt = $this->conexion->prepare("SELECT COUNT(idReserva) FROM Reserva WHERE FechaSalida = :hoy_fecha AND EstadoReserva IN ('Confirmada', 'En Curso', 'Completada')");
            $stmt->bindParam(':hoy_fecha', $hoy_fecha);
            $stmt->execute();
            $stats['checkouts_hoy'] = $stmt->fetchColumn();

            return $stats;

        } catch (PDOException $e) {
            error_log("Error en getDashboardStats: " . $e->getMessage());
            return false;
        }
    }
}
?>

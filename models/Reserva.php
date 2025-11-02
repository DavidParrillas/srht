<?php
class Reserva {
    
    private $table = 'Reserva';
    private $conexion;
    
    // Propiedades de la reserva
    public $idReserva;
    public $idCliente;
    public $idPaquete;
    public $idHabitacion;
    public $EstadoReserva;
    public $FechaEntrada;
    public $FechaSalida;
    public $Comentario;
    public $TotalReservacion;
    
    /**
     * Constructor
     * @param PDO $db Conexión a la base de datos
     */
    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Verifica que las fechas sean válidas
     * @return bool True si son válidas, False si no
     */
    public function validarFechas($fechaEntrada, $fechaSalida) {
        return (strtotime($fechaSalida) > strtotime($fechaEntrada));
    }

   /**
     * Obtener habitaciones disponibles en un rango de fechas.
     */

public function obtenerHabitacionesDisponibles($fechaEntrada, $fechaSalida, $idReservaAExcluir = null) { 
    try {
        $idExcluir = $idReservaAExcluir ? intval($idReservaAExcluir) : null; 
        
        $query = "
            SELECT 
                h.idHabitacion, 
                h.NumeroHabitacion, 
                th.NombreTipoHabitacion, 
                th.PrecioTipoHabitacion, 
                h.EstadoHabitacion
            FROM 
                Habitacion AS h
            JOIN 
                tipohabitacion AS th ON h.idTipoHabitacion = th.idTipoHabitacion
            WHERE 
                h.idHabitacion NOT IN (
                    SELECT r.idHabitacion 
                    FROM Reserva r
                    WHERE 
                        (r.FechaEntrada <= :fechaSalida AND r.FechaSalida >= :fechaEntrada)
                        AND r.EstadoReserva IN ('Pendiente', 'Confirmada', 'En Curso')
                        " . ($idExcluir ? " AND r.idReserva != :idExcluir" : "") . "
                )
            ORDER BY 
                th.NombreTipoHabitacion ASC, h.NumeroHabitacion ASC
        ";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':fechaEntrada', $fechaEntrada);
        $stmt->bindParam(':fechaSalida', $fechaSalida);
        
        // vinculamos el parámetro si existe una ID de exclusión
        if ($idExcluir) {
            $stmt->bindParam(':idExcluir', $idExcluir, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt;

    } catch (PDOException $e) {
        error_log("Error al obtener habitaciones disponibles: " . $e->getMessage());
        return false;
    }
}

   /**
     * Buscar cliente por nombre, DUI o correo
     *
     * @param string $termino
     * @return PDOStatement | false
     */
    public function buscarCliente($termino) {
        try {
            $query = "SELECT idCliente, NombreCliente, CorreoCliente, DuiCliente
                      FROM Cliente
                      WHERE NombreCliente LIKE :t_nombre
                        OR CorreoCliente LIKE :t_correo
                        OR DuiCliente LIKE :t_dui
                      ORDER BY NombreCliente ASC";

            $stmt = $this->conexion->prepare($query);
            
            // Creamos el parámetro de búsqueda
            $param = "%$termino%";
            
            // Vinculamos el mismo valor a los tres parámetros únicos
            $stmt->bindParam(':t_nombre', $param);
            $stmt->bindParam(':t_correo', $param);
            $stmt->bindParam(':t_dui', $param);
            
            $stmt->execute();
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Error al buscar cliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcular total de la reserva
     * (Versión corregida con la lógica de negocio del paquete por noche)
     */
    public function calcularTotal($idHabitacion, $idPaquete, $fechaEntrada, $fechaSalida) {
        try {
            
            $query = "SELECT th.PrecioTipoHabitacion, 
                             COALESCE(p.TarifaPaquete, 0) AS precioPaquete
                      FROM Habitacion h
                      JOIN tipohabitacion th ON h.idTipoHabitacion = th.idTipoHabitacion
                      LEFT JOIN Paquete p ON p.idPaquete = :idPaquete
                      WHERE h.idHabitacion = :idHabitacion";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idHabitacion', $idHabitacion);
            
            if ($idPaquete === null) {
                $stmt->bindValue(':idPaquete', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':idPaquete', $idPaquete, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$datos) {
                error_log("calcularTotal: No se encontraron datos para idHabitacion " . $idHabitacion);
                return 0; 
            }

            $dias = (strtotime($fechaSalida) - strtotime($fechaEntrada)) / 86400;
            if ($dias < 1) $dias = 1;
            
            $tarifaTotalDiaria = $datos['PrecioTipoHabitacion'] + $datos['precioPaquete'];
            
            return $tarifaTotalDiaria * $dias;

        } catch (PDOException $e) {
            error_log("Error al calcular total de reserva: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Crear una nueva reserva
     * @return bool True si se creó, False si falla
     */
    public function crear() {
    try {
        $query = "INSERT INTO " . $this->table . " 
                     (idCliente, idPaquete, idHabitacion, EstadoReserva, FechaEntrada, FechaSalida, Comentario, TotalReservacion)
                     VALUES (:idCliente, :idPaquete, :idHabitacion, :EstadoReserva, :FechaEntrada, :FechaSalida, :Comentario, :TotalReservacion)";
        
        $stmt = $this->conexion->prepare($query);

        $this->Comentario = htmlspecialchars(strip_tags($this->Comentario));
        $this->EstadoReserva = $this->EstadoReserva ?? 'Pendiente';

        $stmt->bindParam(':idCliente', $this->idCliente, PDO::PARAM_INT);
        $stmt->bindParam(':idPaquete', $this->idPaquete, PDO::PARAM_INT);
        $stmt->bindParam(':idHabitacion', $this->idHabitacion, PDO::PARAM_INT);
        $stmt->bindParam(':EstadoReserva', $this->EstadoReserva);
        $stmt->bindParam(':FechaEntrada', $this->FechaEntrada);
        $stmt->bindParam(':FechaSalida', $this->FechaSalida);
        $stmt->bindParam(':Comentario', $this->Comentario);
        
        // Se asume que TotalReservacion es decimal/float, se usa PARAM_STR por defecto o PARAM_STR con el valor ya formateado.
        $stmt->bindParam(':TotalReservacion', $this->TotalReservacion);

        if ($stmt->execute()) {
            $this->idReserva = $this->conexion->lastInsertId();
            return true;
        }

        return false;
    } catch (PDOException $e) {
        error_log("Error al crear reserva: " . $e->getMessage());
        return false;
    }
}

    /**
     * Actualizar fechas y recalcular total
     */
    public function actualizarFechas($idReserva, $fechaEntrada, $fechaSalida) {
        try {
            $total = $this->calcularTotal($this->idHabitacion, $this->idPaquete, $fechaEntrada, $fechaSalida);

            $query = "UPDATE " . $this->table . "
                      SET FechaEntrada = :fechaEntrada,
                          FechaSalida = :fechaSalida,
                          TotalReservacion = :total
                      WHERE idReserva = :idReserva";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':fechaEntrada', $fechaEntrada);
            $stmt->bindParam(':fechaSalida', $fechaSalida);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':idReserva', $idReserva);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar fechas: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado (Check-In, Check-Out, Cancelada)
     */
    public function cambiarEstado($idReserva, $nuevoEstado) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET EstadoReserva = :estado
                      WHERE idReserva = :idReserva";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':estado', $nuevoEstado);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de reserva: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las reservas con detalles del cliente y habitación.
     * @return PDOStatement|false Resultado de la consulta o false en caso de error.
    */
    public function obtenerTodas() {
    try {
        // Query que une las tablas reserva(r), cliente(c), habitacion(h) y tipohabitacion(th)
        $query = "
            SELECT 
                r.idReserva,
                c.NombreCliente,
                th.NombreTipoHabitacion,
                h.NumeroHabitacion,
                r.EstadoReserva,
                r.FechaEntrada,
                r.FechaSalida
            FROM 
                reserva AS r
            JOIN 
                cliente AS c ON r.idCliente = c.idCliente
            JOIN 
                habitacion AS h ON r.idHabitacion = h.idHabitacion
            JOIN 
                tipohabitacion AS th ON h.idTipoHabitacion = th.idTipoHabitacion
            ORDER BY 
                r.FechaEntrada DESC
        ";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        
        // Retorna el statement para ser procesado en la vista
        return $stmt;
        
    } catch (PDOException $e) {
        // Registrar el error en logs
        error_log("Error al obtener todas las reservas: " . $e->getMessage());
        return false;
    }
    }

    /**
     * Obtener los datos de una Habitación por su ID.
     * @param int $idHabitacion
     * @return array | false
     */
    public function obtenerDatosHabitacion($idHabitacion) {
        try {
            $query = "SELECT h.*, th.PrecioTipoHabitacion, th.NombreTipoHabitacion 
                      FROM Habitacion h
                      JOIN tipohabitacion th ON h.idTipoHabitacion = th.idTipoHabitacion
                      WHERE h.idHabitacion = :idHabitacion";
                      
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idHabitacion', $idHabitacion);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener datos de habitación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener los datos de un Paquete por su ID.
     *
     * @param int $idPaquete
     * @return array | false
     */
    public function obtenerDatosPaquete($idPaquete) {
        try {
            $query = "SELECT * FROM Paquete WHERE idPaquete = :idPaquete";
                      
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idPaquete', $idPaquete, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener datos de paquete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener los datos de un Cliente por su ID.
     *
     * @param int $idCliente
     * @return array | false
     */
    public function obtenerDatosCliente($idCliente) {
        try {
            $query = "SELECT * FROM Cliente WHERE idCliente = :idCliente";
                      
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idCliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener datos de cliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener una reserva por su ID, incluyendo detalles de habitación y cliente
     */
    public function obtenerReservaPorId($idReserva) {
        try {
            $query = "
                SELECT 
                    r.*, 
                    c.NombreCliente, 
                    th.NombreTipoHabitacion,
                    h.NumeroHabitacion
                FROM 
                    reserva AS r
                JOIN 
                    cliente AS c ON r.idCliente = c.idCliente
                JOIN 
                    habitacion AS h ON r.idHabitacion = h.idHabitacion
                JOIN 
                    tipohabitacion AS th ON h.idTipoHabitacion = th.idTipoHabitacion
                WHERE 
                    r.idReserva = :idReserva";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener reserva por ID: " . $e->getMessage());
            return false;
        }
    }

}

?>

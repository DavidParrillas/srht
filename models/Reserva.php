<?php
class Reserva
{

    private $table = 'Reserva';
    private $conexion;

    // Propiedades de la reserva
    public $idReserva;
    public $idCliente;
    public $idPaquete;
    public $idHabitacion;
    public $EstadoReserva;
    public $EstadoPago;
    public $FechaEntrada;
    public $FechaSalida;
    public $Comentario;
    public $TotalReservacion;
    public $CantidadPersonas;
    public $PrecioHabitacion;
    public $PrecioPaquete;
    public $RegistroCambio;

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    public function validarFechas($fechaEntrada, $fechaSalida)
    {

        return (strtotime($fechaSalida) > strtotime($fechaEntrada));
    }

    public function obtenerHabitacionesDisponibles($fechaEntrada, $fechaSalida, $idReservaAExcluir = null, $cantidadPersonas = 1)
    {
        try {
            $idExcluir = $idReservaAExcluir ? intval($idReservaAExcluir) : null;

            // Convertimos cantidadPersonas a entero
            $capacidad = intval($cantidadPersonas);
            if ($capacidad < 1) {
                $capacidad = 1;
            }

            $query = "
            SELECT 
                h.idHabitacion, 
                h.NumeroHabitacion, 
                th.NombreTipoHabitacion, 
                th.PrecioTipoHabitacion,
                th.Capacidad,  -- (Opcional, para depuración)
                h.EstadoHabitacion
            FROM 
                Habitacion AS h
            JOIN 
                TipoHabitacion AS th ON h.idTipoHabitacion = th.idTipoHabitacion
            WHERE 
                th.Capacidad >= :cantidadPersonas
            AND 
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
            $stmt->bindParam(':cantidadPersonas', $capacidad, PDO::PARAM_INT);

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

    public function buscarCliente($termino)
    {
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

    public function calcularTotal($idHabitacion, $idPaquete, $fechaEntrada, $fechaSalida)
    {
        try {

            $query = "SELECT th.PrecioTipoHabitacion, 
                                COALESCE(p.TarifaPaquete, 0) AS precioPaquete
                        FROM Habitacion h
                        JOIN TipoHabitacion th ON h.idTipoHabitacion = th.idTipoHabitacion
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
            if ($dias < 1)
                $dias = 1;

            $tarifaTotalDiaria = $datos['PrecioTipoHabitacion'] + $datos['precioPaquete'];

            return $tarifaTotalDiaria * $dias;

        } catch (PDOException $e) {
            error_log("Error al calcular total de reserva: " . $e->getMessage());
            return 0;
        }
    }

    public function crear()
    {
        try {

            $query = "INSERT INTO " . $this->table . " 
                        (idCliente, idPaquete, idHabitacion, EstadoReserva, EstadoPago, FechaEntrada, FechaSalida, 
                         Comentario, TotalReservacion, CantidadPersonas, PrecioHabitacion, PrecioPaquete, 
                         RegistroCambio, FechaCreacion)
                      VALUES 
                        (:idCliente, :idPaquete, :idHabitacion, :EstadoReserva, :EstadoPago, :FechaEntrada, :FechaSalida, 
                         :Comentario, :TotalReservacion, :CantidadPersonas, :PrecioHabitacion, :PrecioPaquete, 
                         :RegistroCambio, :FechaCreacion)";

            $stmt = $this->conexion->prepare($query);

            $this->Comentario = htmlspecialchars(strip_tags($this->Comentario));

            $this->EstadoReserva = $this->EstadoReserva ?? 'Pendiente';
            $this->EstadoPago = $this->EstadoPago ?? 'Pendiente';


            $fechaCreacionPHP = date('Y-m-d H:i:s');

            $stmt->bindParam(':idCliente', $this->idCliente, PDO::PARAM_INT);
            $stmt->bindParam(':idPaquete', $this->idPaquete, PDO::PARAM_INT);
            $stmt->bindParam(':idHabitacion', $this->idHabitacion, PDO::PARAM_INT);
            $stmt->bindParam(':EstadoReserva', $this->EstadoReserva);
            $stmt->bindParam(':EstadoPago', $this->EstadoPago);
            $stmt->bindParam(':FechaEntrada', $this->FechaEntrada);
            $stmt->bindParam(':FechaSalida', $this->FechaSalida);
            $stmt->bindParam(':Comentario', $this->Comentario);
            $stmt->bindParam(':TotalReservacion', $this->TotalReservacion);

            $stmt->bindParam(':CantidadPersonas', $this->CantidadPersonas, PDO::PARAM_INT);
            $stmt->bindParam(':PrecioHabitacion', $this->PrecioHabitacion);
            $stmt->bindParam(':PrecioPaquete', $this->PrecioPaquete);

            $stmt->bindParam(':RegistroCambio', $this->RegistroCambio);
            $stmt->bindParam(':FechaCreacion', $fechaCreacionPHP);

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

    public function actualizarFechas($idReserva, $fechaEntrada, $fechaSalida)
    {
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

    public function cambiarEstado($idReserva, $nuevoEstado, $comentarioCancelacion = null)
    {
        try {

            $querySetAdicional = "";
            $fechaCancelacionPHP = null;

            if ($nuevoEstado == 'Cancelada') {

                $fechaCancelacionPHP = date('Y-m-d H:i:s');
                $querySetAdicional = ", FechaCancelacion = :fechaCancelacion";


                if (!empty($comentarioCancelacion)) {

                    $querySetAdicional .= ", Comentario = CONCAT_WS('\n\n', COALESCE(Comentario, ''), :comentarioCancelacion)";
                }

            } else {
                $querySetAdicional = ", FechaCancelacion = NULL";
            }


            $query = "UPDATE " . $this->table . " 
                      SET 
                          EstadoReserva = :estado
                          " . $querySetAdicional . "
                      WHERE 
                          idReserva = :idReserva";

            $stmt = $this->conexion->prepare($query);


            $stmt->bindParam(':estado', $nuevoEstado);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);

            if ($fechaCancelacionPHP) {
                $stmt->bindParam(':fechaCancelacion', $fechaCancelacionPHP);
            }
            if (!empty($comentarioCancelacion)) {
                $stmt->bindParam(':comentarioCancelacion', $comentarioCancelacion);
            }

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al cambiar estado de reserva: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodas($filtro_texto = null, $filtro_fecha = null)
    {
        try {
            date_default_timezone_set('America/El_Salvador');

            $query = "
            SELECT 
                r.idReserva,
                c.NombreCliente,
                th.NombreTipoHabitacion,
                h.NumeroHabitacion,
                r.EstadoReserva,
                r.FechaEntrada,
                r.FechaSalida,
                r.EstadoPago,
                r.CheckIn,
                r.CheckOut,
                r.CantidadPersonas,
                r.TotalReservacion,
                r.PrecioHabitacion,
                r.PrecioPaquete

            FROM 
                Reserva AS r
            LEFT JOIN 
                Cliente AS c ON r.idCliente = c.idCliente
            LEFT JOIN 
                Habitacion AS h ON r.idHabitacion = h.idHabitacion
            LEFT JOIN
                TipoHabitacion AS th ON h.idTipoHabitacion = th.idTipoHabitacion";

            $where_clauses = [];
            $params = [];

            if (!empty($filtro_texto)) {
                if (is_numeric($filtro_texto)) {
                    $where_clauses[] = "(c.NombreCliente LIKE :texto OR r.idReserva = :id_reserva)";
                    $params[':id_reserva'] = intval($filtro_texto);
                } else {
                    $where_clauses[] = "c.NombreCliente LIKE :texto";
                }
                $params[':texto'] = "%$filtro_texto%";
            }

            if (!empty($filtro_fecha)) {
                $where_clauses[] = "r.FechaEntrada = :fecha";
                $params[':fecha'] = $filtro_fecha;
            }

            if (!empty($where_clauses)) {
                $query .= " WHERE " . implode(" AND ", $where_clauses);
            }

            $query .= "
            ORDER BY 
                CASE
                    WHEN r.EstadoReserva IN ('Confirmada', 'Pendiente') AND r.FechaEntrada >= CURDATE() THEN 1
                    WHEN r.EstadoReserva = 'Cancelada' THEN 3
                    ELSE 2
                END ASC,
                
                CASE
                    WHEN r.EstadoReserva IN ('Confirmada', 'Pendiente') AND r.FechaEntrada >= CURDATE() THEN r.FechaEntrada
                END ASC,
                
                CASE
                    WHEN r.EstadoReserva NOT IN ('Confirmada', 'Pendiente') OR r.FechaEntrada < CURDATE() THEN r.FechaEntrada
                END DESC";

            $stmt = $this->conexion->prepare($query);
            $stmt->execute($params);

            return $stmt;

        } catch (PDOException $e) {
            error_log("Error al obtener todas las reservas: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerDatosHabitacion($idHabitacion)
    {
        try {
            $query = "SELECT h.*, th.PrecioTipoHabitacion, th.NombreTipoHabitacion 
                        FROM Habitacion h 
                        JOIN TipoHabitacion th ON h.idTipoHabitacion = th.idTipoHabitacion
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

    public function obtenerDatosPaquete($idPaquete)
    {
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

    public function obtenerDatosCliente($idCliente)
    {
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

    public function obtenerReservaPorId($idReserva)
    {
        try {
            $query = "
                SELECT 
                    r.*, 
                    c.NombreCliente, 
                    th.NombreTipoHabitacion,
                    h.NumeroHabitacion,
                    p.NombrePaquete  -- <-- ¡CAMBIO AÑADIDO!
                FROM 
                    Reserva AS r
                JOIN 
                    Cliente AS c ON r.idCliente = c.idCliente
                JOIN 
                    Habitacion AS h ON r.idHabitacion = h.idHabitacion
                JOIN 
                    TipoHabitacion AS th ON h.idTipoHabitacion = th.idTipoHabitacion
                LEFT JOIN 
                    Paquete AS p ON r.idPaquete = p.idPaquete
                WHERE 
                    r.idReserva = :idReserva";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener reserva por ID: " + $e->getMessage());
            return false;
        }
    }

    public function obtenerPagosPorReserva($idReserva)
    {
        try {
            $query = "SELECT * FROM Pago 
                      WHERE idReserva = :idReserva 
                      ORDER BY FechaPago ASC";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener pagos por reserva: " . $e->getMessage());
            return false;
        }
    }

    public function agregarPago($idReserva, $monto, $tipoTransaccion, $formaPago, $comprobante = null, $comentarioPago = null)
    {
        try {
            $query = "INSERT INTO Pago 
                        (idReserva, TipoTransaccion, FechaPago, MontoPago, FormaPago, Comprobante, ComentarioPago)
                      VALUES 
                        (:idReserva, :tipoTransaccion, NOW(), :monto, :formaPago, :comprobante, :comentarioPago)";

            $stmt = $this->conexion->prepare($query);

            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);
            $stmt->bindParam(':tipoTransaccion', $tipoTransaccion);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':formaPago', $formaPago);
            $stmt->bindParam(':comprobante', $comprobante);
            $stmt->bindParam(':comentarioPago', $comentarioPago);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al agregar pago: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstadoPago($idReserva)
    {
        try {

            $query = "SELECT 
                        r.TotalReservacion, 
                        COALESCE(SUM(p.MontoPago), 0) AS TotalPagado
                      FROM 
                        Reserva r
                      LEFT JOIN 
                        Pago p ON r.idReserva = p.idReserva
                      WHERE 
                        r.idReserva = :idReserva
                      GROUP BY 
                        r.idReserva";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$datos) {
                return false;
            }


            $totalReserva = (float) $datos['TotalReservacion'];
            $totalPagado = (float) $datos['TotalPagado'];


            $nuevoEstadoPago = 'Pendiente';


            $epsilon = 0.001;

            if (abs($totalPagado - $totalReserva) < $epsilon || $totalPagado > $totalReserva) {
                $nuevoEstadoPago = 'Completado';
            } elseif ($totalPagado > 0) {
                $nuevoEstadoPago = 'Parcial';
            }


            $queryUpdate = "UPDATE " . $this->table . " 
                            SET EstadoPago = :estadoPago 
                            WHERE idReserva = :idReserva";

            $stmtUpdate = $this->conexion->prepare($queryUpdate);
            $stmtUpdate->bindParam(':estadoPago', $nuevoEstadoPago);
            $stmtUpdate->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);

            return $stmtUpdate->execute();

        } catch (PDOException $e) {
            error_log("Error al actualizar estado de pago: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarRegistroCambio($idReserva, $nuevoTexto)
    {
        try {

            $query = "UPDATE " . $this->table . " 
                      SET RegistroCambio = 
                        CASE
                            WHEN COALESCE(RegistroCambio, '') = '' THEN :nuevoTexto
                            ELSE CONCAT_WS('\n', RegistroCambio, :nuevoTexto)
                        END
                      WHERE idReserva = :idReserva";

            $stmt = $this->conexion->prepare($query);

            $stmt->bindParam(':nuevoTexto', $nuevoTexto);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al actualizar registro de cambio: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarComentario($idReserva, $comentario)
    {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET Comentario = :comentario 
                      WHERE idReserva = :idReserva";

            $stmt = $this->conexion->prepare($query);

            $stmt->bindParam(':comentario', $comentario);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al actualizar comentario: " . $e->getMessage());
            return false;
        }
    }

    public function registrarCheckIn($idReserva)
    {
        try {
            $fechaCheckInPHP = date('Y-m-d H:i:s');

            $query = "UPDATE " . $this->table . " 
                      SET 
                          EstadoReserva = 'En Curso',
                          CheckIn = :fechaCheckIn
                      WHERE 
                          idReserva = :idReserva 
                          AND EstadoReserva = 'Confirmada'";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':fechaCheckIn', $fechaCheckInPHP);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al registrar Check-In: " . $e->getMessage());
            return false;
        }
    }

    public function registrarCheckOut($idReserva)
    {
        try {
            $fechaCheckOutPHP = date('Y-m-d H:i:s');

            $query = "UPDATE " . $this->table . " 
                      SET 
                          EstadoReserva = 'Completada',
                          CheckOut = :fechaCheckOut
                      WHERE 
                          idReserva = :idReserva
                          AND EstadoReserva = 'En Curso'";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':fechaCheckOut', $fechaCheckOutPHP);
            $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al registrar Check-Out: " . $e->getMessage());
            return false;
        }
    }

}
?>
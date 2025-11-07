<?php
require_once 'config/database.php';
require_once 'models/Habitacion.php';
require_once 'models/TipoHabitacion.php';

class HabitacionService
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listarHabitaciones()
    {
        $sql = "SELECT 
            h.idHabitacion AS idHabitacion,
            h.numeroHabitacion AS numeroHabitacion,
            h.estadoHabitacion AS estadoHabitacion,
            h.detalleHabitacion AS detalleHabitacion,
            t.idTipoHabitacion AS idTipoHabitacion,
            t.nombreTipoHabitacion AS nombreTipoHabitacion,
            t.capacidad AS capacidad,
            t.precioTipoHabitacion AS precioTipoHabitacion
        FROM Habitacion h
        INNER JOIN TipoHabitacion t ON h.idTipoHabitacion = t.idTipoHabitacion";

        $stmt = $this->conn->query($sql);
        $habitaciones = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tipo = new TipoHabitacion(
                $fila['idTipoHabitacion'],
                $fila['nombreTipoHabitacion'],
                $fila['capacidad'],
                $fila['precioTipoHabitacion']
            );

            $habitaciones[] = new Habitacion(
                $fila['idHabitacion'],
                $tipo,
                $fila['numeroHabitacion'],
                $fila['estadoHabitacion'],
                $fila['detalleHabitacion']
            );
        }

        return $habitaciones;
    }

    public function listarTiposHabitacion()
    {
        $sql = "SELECT idTipoHabitacion, nombreTipoHabitacion, capacidad, precioTipoHabitacion 
            FROM TipoHabitacion
            ORDER BY nombreTipoHabitacion ASC";

        $stmt = $this->conn->query($sql);
        $tipos = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tipos[] = new TipoHabitacion(
                $fila['idTipoHabitacion'],
                $fila['nombreTipoHabitacion'],
                $fila['capacidad'],
                $fila['precioTipoHabitacion']
            );
        }

        return $tipos;
    }


    public function crearHabitacion(Habitacion $habitacion)
    {
        $sql = "INSERT INTO Habitacion (numeroHabitacion, estadoHabitacion, detalleHabitacion, idTipoHabitacion)
                VALUES (:numero, :estado, :detalle, :idTipo)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':numero' => $habitacion->getNumeroHabitacion(),
            ':estado' => $habitacion->getEstadoHabitacion(),
            ':detalle' => $habitacion->getDetalleHabitacion(),
            ':idTipo' => $habitacion->getTipoHabitacion()->getId()
        ]);

        return $this->conn->lastInsertId();
    }

    public function listarEstadosHabitacion()
    {
        $sql = "SELECT DISTINCT estadoHabitacion 
            FROM Habitacion 
            WHERE estadoHabitacion IS NOT NULL 
            ORDER BY estadoHabitacion ASC";

        $stmt = $this->conn->query($sql);
        $estados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estados[] = $fila['estadoHabitacion'];
        }

        return $estados;
    }

    public function obtenerHabitacionPorId($id)
    {
        try {
            $sql = "SELECT 
                    h.idHabitacion,
                    h.numeroHabitacion AS numeroHabitacion,
                    h.estadoHabitacion AS estadoHabitacion,
                    h.detalleHabitacion AS detalleHabitacion,
                    t.idTipoHabitacion,
                    t.nombreTipoHabitacion AS nombreTipoHabitacion,
                    t.precioTipoHabitacion AS precioTipoHabitacion,
                    t.capacidad
                FROM Habitacion h
                JOIN TipoHabitacion t ON h.idTipoHabitacion = t.idTipoHabitacion
                WHERE h.idHabitacion = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) {
                error_log("No se encontró habitación con idHabitacion=$id");
                return null;
            }

            error_log("Datos habitación obtenidos: " . print_r($data, true));

            $tipo = new TipoHabitacion(
                $data['idTipoHabitacion'],
                $data['nombreTipoHabitacion'],
                $data['capacidad'],
                $data['precioTipoHabitacion']
            );

            $habitacion = new Habitacion();
            $habitacion->setIdHabitacion($data['idHabitacion']);
            $habitacion->setNumeroHabitacion($data['numeroHabitacion']);
            $habitacion->setEstadoHabitacion($data['estadoHabitacion']);
            $habitacion->setTipoHabitacion($tipo);
            $habitacion->setDetalleHabitacion($data['detalleHabitacion']);
            $habitacion->setAmenidadesIds($this->obtenerAmenidadesPorHabitacion($data['idHabitacion']) ?? []);

            return $habitacion;
        } catch (PDOException $e) {
            error_log("Error al obtener habitación por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene una habitación por su ID, incluyendo sus amenidades asociadas.
     *
     * @param int $id El ID de la habitación.
     * @return Habitacion|null El objeto Habitacion con sus amenidades o null si no se encuentra.
     */
    public function obtenerHabitacionConAmenidadesPorId($id)
    {
        // Primero, obtenemos la habitación base
        $habitacion = $this->obtenerHabitacionPorId($id);

        if (!$habitacion) {
            return null; // Si no se encuentra la habitación, no hay nada más que hacer.
        }

        // Ahora, obtenemos las amenidades asociadas a esa habitación
        $sqlAmenidades = "SELECT idAmenidad FROM HabitacionAmenidad WHERE idHabitacion = :idHabitacion";
        $stmtAmenidades = $this->conn->prepare($sqlAmenidades);
        $stmtAmenidades->execute([':idHabitacion' => $id]);
        
        // fetchAll con PDO::FETCH_COLUMN nos da un array plano de IDs, ej: [1, 3, 5]
        $amenidadesIds = $stmtAmenidades->fetchAll(PDO::FETCH_COLUMN);

        // Asignamos el array de IDs de amenidades al objeto habitación
        $habitacion->setAmenidadesIds($amenidadesIds);

        return $habitacion;
    }

    private function obtenerAmenidadesPorHabitacion($idHabitacion)
    {
        try {
            $sql = "SELECT idAmenidad FROM habitacionamenidad WHERE idHabitacion = :idHabitacion";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':idHabitacion', $idHabitacion, PDO::PARAM_INT);
            $stmt->execute();

            $amenidades = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'idAmenidad');
            error_log("Amenidades de la habitación $idHabitacion: " . json_encode($amenidades));
            return $amenidades;
        } catch (PDOException $e) {
            error_log('' . $e->getMessage());
            return [];
        }
    }

    public function listarAmenidades()
    {
        try {
            $sql = "SELECT idAmenidad, nombreAmenidad FROM amenidad ORDER BY nombreAmenidad ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar amenidades: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarHabitacion($id, $idTipoHabitacion, $estado, $detalle, array $amenidades)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE habitacion 
                SET EstadoHabitacion = :estado,
                    idTipoHabitacion = :tipo,
                    DetalleHabitacion = :detalle
                WHERE idHabitacion = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':tipo', $idTipoHabitacion, PDO::PARAM_INT);
            $stmt->bindParam(':detalle', $detalle);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $sqlDelete = "DELETE FROM habitacionamenidad WHERE idHabitacion = :id";
            $stmt = $this->conn->prepare($sqlDelete);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if (!empty($amenidades)) {
                $sqlInsert = "INSERT INTO habitacionamenidad (idHabitacion, idAmenidad) VALUES (:idHabitacion, :idAmenidad)";
                $stmt = $this->conn->prepare($sqlInsert);
                foreach ($amenidades as $idAmenidad) {
                    $stmt->execute([
                        ':idHabitacion' => $id,
                        ':idAmenidad' => $idAmenidad
                    ]);
                }
            }

            $this->conn->commit();
            error_log("✅ Habitación ID=$id actualizada correctamente con sus amenidades.");
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("❌ Error al actualizar habitación ID=$id: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarHabitacion($id)
    {
        try {
            $this->conn->beginTransaction();

            $sqlAmenidades = "DELETE FROM habitacionamenidad WHERE idHabitacion = :id";
            $stmt = $this->conn->prepare($sqlAmenidades);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $sqlHabitacion = "DELETE FROM habitacion WHERE idHabitacion = :id";
            $stmt = $this->conn->prepare($sqlHabitacion);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();

            error_log("✅ Habitación ID=$id eliminada correctamente.");
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("❌ Error al eliminar habitación ID=$id: " . $e->getMessage());
            return false;
        }
    }



}
?>
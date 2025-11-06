<?php
class Paquetes
{
    private $table = 'Paquete';

    // Campos
    public $idPaquete;
    public $NombrePaquete;
    public $DescripcionPaquete;
    public $TarifaPaquete;

    // Conexión PDO
    private $conexion;

    /** @param PDO $db */
    public function __construct($db)
    {
        $this->conexion = $db;
    }

    /** Crear */
    public function crear()
    {
        try {
            $query = "INSERT INTO {$this->table}
                      (NombrePaquete, DescripcionPaquete, TarifaPaquete)
                      VALUES (:nombre, :descripcion, :tarifa)";
            $stmt = $this->conexion->prepare($query);

            // Sanitizar
            $this->NombrePaquete       = htmlspecialchars(strip_tags($this->NombrePaquete));
            $this->DescripcionPaquete  = htmlspecialchars(strip_tags($this->DescripcionPaquete));
            $this->TarifaPaquete       = (float)$this->TarifaPaquete;

            // Bind
            $stmt->bindParam(':nombre', $this->NombrePaquete);
            $stmt->bindParam(':descripcion', $this->DescripcionPaquete);
            $stmt->bindParam(':tarifa', $this->TarifaPaquete);

            if ($stmt->execute()) {
                $this->idPaquete = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear paquete: " . $e->getMessage());
            return false;
        }
    }

    /** Editar */
    public function editar()
    {
        try {
            $query = "UPDATE {$this->table}
                        SET NombrePaquete = :nombre,
                            DescripcionPaquete = :descripcion,
                            TarifaPaquete = :tarifa
                      WHERE idPaquete = :id";
            $stmt = $this->conexion->prepare($query);

            $this->NombrePaquete       = htmlspecialchars(strip_tags($this->NombrePaquete));
            $this->DescripcionPaquete  = htmlspecialchars(strip_tags($this->DescripcionPaquete));
            $this->TarifaPaquete       = (float)$this->TarifaPaquete;
            $this->idPaquete           = (int)$this->idPaquete;

            $stmt->bindParam(':nombre', $this->NombrePaquete);
            $stmt->bindParam(':descripcion', $this->DescripcionPaquete);
            $stmt->bindParam(':tarifa', $this->TarifaPaquete);
            $stmt->bindParam(':id', $this->idPaquete);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al editar paquete: " . $e->getMessage());
            return false;
        }
    }

    /** Eliminar */
    public function eliminar()
    {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM {$this->table} WHERE idPaquete = :id");
            $this->idPaquete = (int)$this->idPaquete;
            $stmt->bindParam(':id', $this->idPaquete);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar paquete: " . $e->getMessage());
            return false;
        }
    }

    /** Listado */
    public function obtenerTodas()
    {
        try {
            $stmt = $this->conexion->prepare("SELECT idPaquete, NombrePaquete, DescripcionPaquete, TarifaPaquete
                                              FROM {$this->table}
                                              ORDER BY NombrePaquete ASC");
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error al obtener paquetes: " . $e->getMessage());
            return false;
        }
    }

    /** Cargar por ID (carga propiedades del modelo + retorna true/false) */
    public function obtenerPorId()
    {
        try {
            $stmt = $this->conexion->prepare("SELECT idPaquete, NombrePaquete, DescripcionPaquete, TarifaPaquete
                                              FROM {$this->table}
                                              WHERE idPaquete = :id
                                              LIMIT 1");
            $stmt->bindParam(':id', $this->idPaquete, PDO::PARAM_INT);
            $stmt->execute();
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->NombrePaquete      = $row['NombrePaquete'];
                $this->DescripcionPaquete = $row['DescripcionPaquete'];
                $this->TarifaPaquete      = $row['TarifaPaquete'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al obtener paquete por ID: " . $e->getMessage());
            return false;
        }
    }

    /** ¿Existe por nombre? */
    public function existePorNombre($nombre)
    {
        try {
            $stmt = $this->conexion->prepare("SELECT idPaquete FROM {$this->table} WHERE NombrePaquete = :nombre LIMIT 1");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar existencia de paquete: " . $e->getMessage());
            return false;
        }
    }
}
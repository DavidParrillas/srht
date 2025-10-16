<?php
class Amenidades {
    
    private $table = 'Amenidad';
    
    // Propiedades de la amenidad
    public $idAmenidad;
    public $nombreAmenidad;
    public $Descripcion;
    
    // Conexión a la base de datos
    private $conexion;
    
    /**
     * Constructor
     * @param PDO $db Conexión a la base de datos
     */
    public function __construct($db) {
        $this->conexion = $db;
    }
    
    
    /**
     * Crear una nueva amenidad
     * @return bool True si se creó exitosamente, False en caso contrario
     */
    public function crear() {
        try {
            // Preparar la consulta SQL
            $query = "INSERT INTO " . $this->table . " 
                    (nombreAmenidad, Descripcion) 
                    VALUES (:nombreAmenidad, :Descripcion)";
            
            // Preparar la declaración
            $stmt = $this->conexion->prepare($query);
            
            // Limpiar y validar datos
            $this->nombreAmenidad = htmlspecialchars(strip_tags($this->nombreAmenidad));
            $this->Descripcion = htmlspecialchars(strip_tags($this->Descripcion));
            
            // Vincular parámetros
            $stmt->bindParam(':nombreAmenidad', $this->nombreAmenidad);
            $stmt->bindParam(':Descripcion', $this->Descripcion);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                $this->idAmenidad = $this->conexion->lastInsertId();
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al crear amenidad: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Editar una amenidad existente
     * @return bool True si se actualizó exitosamente, False en caso contrario
     */
    public function editar() {
        try {
            // Preparar la consulta SQL
            $query = "UPDATE " . $this->table . " 
                    SET nombreAmenidad = :nombreAmenidad,
                        Descripcion = :Descripcion
                    WHERE idAmenidad = :idAmenidad";
            
            // Preparar la declaración
            $stmt = $this->conexion->prepare($query);
            
            // Limpiar y validar datos
            $this->nombreAmenidad = htmlspecialchars(strip_tags($this->nombreAmenidad));
            $this->Descripcion = htmlspecialchars(strip_tags($this->Descripcion));
            $this->idAmenidad = htmlspecialchars(strip_tags($this->idAmenidad));
            
            // Vincular parámetros
            $stmt->bindParam(':nombreAmenidad', $this->nombreAmenidad);
            $stmt->bindParam(':Descripcion', $this->Descripcion);
            $stmt->bindParam(':idAmenidad', $this->idAmenidad);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al editar amenidad: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar una amenidad
     * @return bool True si se eliminó exitosamente, False en caso contrario
     */
    public function eliminar() {
        try {
            // Preparar la consulta SQL
            $query = "DELETE FROM " . $this->table . " 
                    WHERE idAmenidad = :idAmenidad";
            
            // Preparar la declaración
            $stmt = $this->conexion->prepare($query);
            
            // Limpiar datos
            $this->idAmenidad = htmlspecialchars(strip_tags($this->idAmenidad));
            
            // Vincular parámetro
            $stmt->bindParam(':idAmenidad', $this->idAmenidad);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al eliminar amenidad: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todas las amenidades
     * @return PDOStatement Resultado de la consulta
     */
    public function obtenerTodas() {
        try {
            $query = "SELECT idAmenidad, nombreAmenidad, Descripcion 
                    FROM " . $this->table . " 
                    ORDER BY nombreAmenidad ASC";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Error al obtener amenidades: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener una amenidad por su ID
     * @return bool True si se encontró, False en caso contrario
     */
    public function obtenerPorId() {
        try {
            $query = "SELECT idAmenidad, nombreAmenidad, Descripcion 
                    FROM " . $this->table . " 
                    WHERE idAmenidad = :idAmenidad 
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idAmenidad', $this->idAmenidad);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $this->nombreAmenidad = $row['nombreAmenidad'];
                $this->Descripcion = $row['Descripcion'];
                
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al obtener amenidad por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si una amenidad existe por su nombre
     * @param string $nombre Nombre de la amenidad a verificar
     * @return bool True si existe, False en caso contrario
     */
    public function existePorNombre($nombre) {
        try {
            $query = "SELECT idAmenidad 
                    FROM " . $this->table . " 
                    WHERE nombreAmenidad = :nombre 
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error al verificar existencia de amenidad: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener amenidades asociadas a una habitación
     * @param int $idHabitacion ID de la habitación
     * @return PDOStatement Resultado de la consulta
     */
    public function obtenerPorHabitacion($idHabitacion) {
        try {
            $query = "SELECT a.idAmenidad, a.nombreAmenidad, a.Descripcion 
                    FROM " . $this->table . " a
                    INNER JOIN HabitacionAmenidad ha ON a.idAmenidad = ha.idAmenidad
                    WHERE ha.idHabitacion = :idHabitacion
                    ORDER BY a.nombreAmenidad ASC";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idHabitacion', $idHabitacion);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Error al obtener amenidades por habitación: " . $e->getMessage());
            return false;
        }
    }
}
?>
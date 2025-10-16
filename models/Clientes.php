<?php
/**
 * Modelo: Clientes
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * Propósito: Gestionar los clientes/huéspedes del hotel
 * Versión: 1.0
 * Fecha: Octubre 2025
 */

class Clientes {
    
    private $conn;
    private $table = 'Cliente';
    
    // Propiedades del cliente
    public $idCliente;
    public $DuiCliente;
    public $CorreoCliente;
    public $NombreCliente;
    public $TelefonoCliente;
    
    /**
     * Constructor
     * @param PDO $db Conexión a la base de datos
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crear un nuevo cliente
     * @return bool True si se creó exitosamente, False en caso contrario
     */
    public function crear() {
        try {
            // Preparar la consulta SQL
            $query = "INSERT INTO " . $this->table . " 
                    (DuiCliente, CorreoCliente, NombreCliente, TelefonoCliente) 
                    VALUES (:DuiCliente, :CorreoCliente, :NombreCliente, :TelefonoCliente)";
            
            // Preparar la declaración
            $stmt = $this->conn->prepare($query);
            
            // Limpiar y validar datos
            $this->DuiCliente = htmlspecialchars(strip_tags($this->DuiCliente));
            $this->CorreoCliente = filter_var($this->CorreoCliente, FILTER_SANITIZE_EMAIL);
            $this->NombreCliente = htmlspecialchars(strip_tags($this->NombreCliente));
            $this->TelefonoCliente = htmlspecialchars(strip_tags($this->TelefonoCliente));
            
            // Vincular parámetros
            $stmt->bindParam(':DuiCliente', $this->DuiCliente);
            $stmt->bindParam(':CorreoCliente', $this->CorreoCliente);
            $stmt->bindParam(':NombreCliente', $this->NombreCliente);
            $stmt->bindParam(':TelefonoCliente', $this->TelefonoCliente);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                $this->idCliente = $this->conn->lastInsertId();
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al crear cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Editar un cliente existente
     * @return bool True si se actualizó exitosamente, False en caso contrario
     */
    public function editar() {
        try {
            // Preparar la consulta SQL
            $query = "UPDATE " . $this->table . " 
                    SET DuiCliente = :DuiCliente,
                        CorreoCliente = :CorreoCliente,
                        NombreCliente = :NombreCliente,
                        TelefonoCliente = :TelefonoCliente
                    WHERE idCliente = :idCliente";
        
            // Preparar la declaración
            $stmt = $this->conn->prepare($query);
            
            // Limpiar y validar datos
            $this->DuiCliente = htmlspecialchars(strip_tags($this->DuiCliente));
            $this->CorreoCliente = filter_var($this->CorreoCliente, FILTER_SANITIZE_EMAIL);
            $this->NombreCliente = htmlspecialchars(strip_tags($this->NombreCliente));
            $this->TelefonoCliente = htmlspecialchars(strip_tags($this->TelefonoCliente));
            $this->idCliente = htmlspecialchars(strip_tags($this->idCliente));
            
            // Vincular parámetros
            $stmt->bindParam(':DuiCliente', $this->DuiCliente);
            $stmt->bindParam(':CorreoCliente', $this->CorreoCliente);
            $stmt->bindParam(':NombreCliente', $this->NombreCliente);
            $stmt->bindParam(':TelefonoCliente', $this->TelefonoCliente);
            $stmt->bindParam(':idCliente', $this->idCliente);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al editar cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar un cliente
     * IMPORTANTE: No se puede eliminar si tiene reservas (RESTRICT en BD)
     * @return bool True si se eliminó exitosamente, False en caso contrario
     * @throws PDOException Si el cliente tiene reservas asociadas
     */
    public function eliminar() {
        try {
            // Preparar la consulta SQL
            $query = "DELETE FROM " . $this->table . " 
                    WHERE idCliente = :idCliente";
            
            // Preparar la declaración
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $this->idCliente = htmlspecialchars(strip_tags($this->idCliente));
            
            // Vincular parámetro
            $stmt->bindParam(':idCliente', $this->idCliente);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            // Si es error de constraint (tiene reservas), lanzar excepción
            if ($e->getCode() == '23000') {
                throw new PDOException('No se puede eliminar el cliente porque tiene reservas asociadas', 23000);
            }
            error_log("Error al eliminar cliente: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener todos los clientes
     * @return PDOStatement Resultado de la consulta
     */
    public function obtenerTodos() {
        try {
            $query = "SELECT idCliente, DuiCliente, CorreoCliente, NombreCliente, TelefonoCliente 
                    FROM " . $this->table . " 
                    ORDER BY NombreCliente ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener un cliente por su ID
     * @return bool True si se encontró, False en caso contrario
     */
    public function obtenerPorId() {
        try {
            $query = "SELECT idCliente, DuiCliente, CorreoCliente, NombreCliente, TelefonoCliente 
                    FROM " . $this->table . " 
                    WHERE idCliente = :idCliente 
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCliente', $this->idCliente);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $this->DuiCliente = $row['DuiCliente'];
                $this->CorreoCliente = $row['CorreoCliente'];
                $this->NombreCliente = $row['NombreCliente'];
                $this->TelefonoCliente = $row['TelefonoCliente'];
                
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al obtener cliente por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un DUI ya existe en la base de datos
     * @param string $dui DUI a verificar
     * @param int|null $excluirId ID del cliente a excluir en la búsqueda (para edición)
     * @return bool True si existe, False en caso contrario
     */
    public function duiExiste($dui, $excluirId = null) {
        try {
            if ($excluirId === null) {
                $query = "SELECT idCliente 
                        FROM " . $this->table . " 
                        WHERE DuiCliente = :dui 
                        LIMIT 1";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':dui', $dui);
            } else {
                $query = "SELECT idCliente 
                        FROM " . $this->table . " 
                        WHERE DuiCliente = :dui 
                        AND idCliente != :excluirId 
                        LIMIT 1";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':dui', $dui);
                $stmt->bindParam(':excluirId', $excluirId);
            }
            
            $stmt->execute();
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error al verificar existencia de DUI: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un correo ya existe en la base de datos
     * @param string $correo Correo a verificar
     * @param int|null $excluirId ID del cliente a excluir en la búsqueda (para edición)
     * @return bool True si existe, False en caso contrario
     */
    public function correoExiste($correo, $excluirId = null) {
        try {
            if ($excluirId === null) {
                $query = "SELECT idCliente 
                        FROM " . $this->table . " 
                        WHERE CorreoCliente = :correo 
                        LIMIT 1";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':correo', $correo);
            } else {
                $query = "SELECT idCliente 
                        FROM " . $this->table . " 
                        WHERE CorreoCliente = :correo 
                        AND idCliente != :excluirId 
                        LIMIT 1";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':excluirId', $excluirId);
            }
            
            $stmt->execute();
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error al verificar existencia de correo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar clientes por nombre
     * @param string $termino Término de búsqueda
     * @return PDOStatement Resultado de la consulta
     */
    public function buscarPorNombre($termino) {
        try {
            $query = "SELECT idCliente, DuiCliente, CorreoCliente, NombreCliente, TelefonoCliente 
                    FROM " . $this->table . " 
                    WHERE NombreCliente LIKE :termino 
                    ORDER BY NombreCliente ASC";
            
            $stmt = $this->conn->prepare($query);
            $terminoBusqueda = "%{$termino}%";
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Error al buscar clientes por nombre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar cliente por DUI
     * @param string $dui DUI a buscar
     * @return PDOStatement Resultado de la consulta
     */
    public function buscarPorDui($dui) {
        try {
            $query = "SELECT idCliente, DuiCliente, CorreoCliente, NombreCliente, TelefonoCliente 
                    FROM " . $this->table . " 
                    WHERE DuiCliente LIKE :dui 
                    ORDER BY NombreCliente ASC";
            
            $stmt = $this->conn->prepare($query);
            $duiBusqueda = "%{$dui}%";
            $stmt->bindParam(':dui', $duiBusqueda);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Error al buscar cliente por DUI: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar cliente por correo
     * @param string $correo Correo a buscar
     * @return PDOStatement Resultado de la consulta
     */
    public function buscarPorCorreo($correo) {
        try {
            $query = "SELECT idCliente, DuiCliente, CorreoCliente, NombreCliente, TelefonoCliente 
                    FROM " . $this->table . " 
                    WHERE CorreoCliente LIKE :correo 
                    ORDER BY NombreCliente ASC";
            
            $stmt = $this->conn->prepare($query);
            $correoBusqueda = "%{$correo}%";
            $stmt->bindParam(':correo', $correoBusqueda);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Error al buscar cliente por correo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener reservas de un cliente
     * @param int $idCliente ID del cliente
     * @return PDOStatement|bool Resultado de la consulta o false en caso de error
     */
    public function obtenerReservas($idCliente) {
        try {
            $query = "SELECT r.*, 
                            h.NumeroHabitacion, 
                            th.NombreTipoHabitacion,
                            p.NombrePaquete,
                            p.TarifaPaquete
                    FROM Reserva r
                    INNER JOIN Habitacion h ON r.idHabitacion = h.idHabitacion
                    INNER JOIN TipoHabitacion th ON h.idTipoHabitacion = th.idTipoHabitacion
                    INNER JOIN Paquete p ON r.idPaquete = p.idPaquete
                    WHERE r.idCliente = :idCliente
                    ORDER BY r.FechaEntrada DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idCliente', $idCliente);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Error al obtener reservas del cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validar formato de DUI salvadoreño
     * @param string $dui DUI a validar
     * @return bool True si el formato es válido, False en caso contrario
     */
    public function validarFormatoDui($dui) {
        // Formato: 12345678-9 (8 dígitos, guion, 1 dígito)
        return preg_match('/^[0-9]{8}-[0-9]$/', $dui) === 1;
    }
    
    /**
     * Contar total de clientes
     * @return int Número total de clientes
     */
    public function contarClientes() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return intval($row['total']);
            
        } catch (PDOException $e) {
            error_log("Error al contar clientes: " . $e->getMessage());
            return 0;
        }
    }
}
?>
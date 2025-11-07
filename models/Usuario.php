<?php
/**
 * Modelo Usuario
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona las operaciones CRUD y consultas relacionadas con usuarios del sistema
 * Tabla: Usuario
 */

class Usuario {
    
    // Propiedades de la clase que mapean los campos de la tabla
    private $idUsuario;
    private $idRol;
    private $NombreUsuario;
    private $ContrasenaUsuario;
    private $CorreoUsuario;
    
    // Conexión a la base de datos
    private $conexion;
    
    /**
     * Constructor
     * @param PDO $db Conexión a la base de datos
     */
    public function __construct($db) {
        $this->conexion = $db;
    }
    
    // ==================== GETTERS Y SETTERS ====================
    
    public function getIdUsuario() {
        return $this->idUsuario;
    }
    
    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }
    
    public function getIdRol() {
        return $this->idRol;
    }
    
    public function setIdRol($idRol) {
        $this->idRol = $idRol;
    }
    
    public function getNombreUsuario() {
        return $this->NombreUsuario;
    }
    
    public function setNombreUsuario($NombreUsuario) {
        $this->NombreUsuario = $NombreUsuario;
    }
    
    public function getContrasenaUsuario() {
        return $this->ContrasenaUsuario;
    }
    
    public function setContrasenaUsuario($ContrasenaUsuario) {
        // Encriptar contraseña antes de guardar
        $this->ContrasenaUsuario = password_hash($ContrasenaUsuario, PASSWORD_DEFAULT);
    }
    
    public function getCorreoUsuario() {
        return $this->CorreoUsuario;
    }
    
    public function setCorreoUsuario($CorreoUsuario) {
        $this->CorreoUsuario = $CorreoUsuario;
    }
    
    // ==================== MÉTODOS CRUD ====================
    
    /**
     * Crear un nuevo usuario
     * @return bool True si se creó exitosamente, false en caso contrario
     */
    public function crear() {
        try {
            $query = "INSERT INTO Usuario (idRol, NombreUsuario, ContrasenaUsuario, CorreoUsuario) 
                    VALUES (:idRol, :nombreUsuario, :contrasena, :correo)";
            
            $stmt = $this->conexion->prepare($query);
            
            // Bind de parámetros
            $stmt->bindParam(':idRol', $this->idRol);
            $stmt->bindParam(':nombreUsuario', $this->NombreUsuario);
            $stmt->bindParam(':contrasena', $this->ContrasenaUsuario);
            $stmt->bindParam(':correo', $this->CorreoUsuario);
            
            if ($stmt->execute()) {
                $this->idUsuario = $this->conexion->lastInsertId();
                return true;
            }
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Leer todos los usuarios con información de su rol
     * @param array $filtros Array asociativo con los filtros a aplicar (nombre, rol)
     * @return array|false Array de usuarios o false si hay error
     */
    public function leerTodos($filtros = []) {
        try {
            $query = "SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, 
                            u.idRol, r.NombreRol
                    FROM Usuario u
                    INNER JOIN Rol r ON u.idRol = r.idRol
                    WHERE 1=1";

            $params = [];
            
            // Aplicar filtro por nombre si existe
            if (!empty($filtros['nombre'])) {
                $query .= " AND u.NombreUsuario LIKE :nombre";
                $params[':nombre'] = "%" . $filtros['nombre'] . "%";
            }
            
            // Aplicar filtro por rol si existe
            if (!empty($filtros['rol'])) {
                $query .= " AND u.idRol = :rol";
                $params[':rol'] = $filtros['rol'];
            }
            
            $query .= " ORDER BY u.NombreUsuario ASC";
            
            $stmt = $this->conexion->prepare($query);
            
            // Bind de parámetros
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al leer usuarios: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Leer un usuario específico por ID
     * @return array|false Datos del usuario o false si no existe
     */
    public function leerPorId() {
        try {
            $query = "SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, 
                            u.idRol, r.NombreRol, r.DescripcionRol
                    FROM Usuario u
                    INNER JOIN Rol r ON u.idRol = r.idRol
                    WHERE u.idUsuario = :idUsuario";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idUsuario', $this->idUsuario);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->NombreUsuario = $row['NombreUsuario'];
                $this->CorreoUsuario = $row['CorreoUsuario'];
                $this->idRol = $row['idRol'];
                return $row;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al leer usuario por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar datos del usuario
     * @return bool True si se actualizó exitosamente, false en caso contrario
     */
    public function actualizar() {
        try {
            $query = "UPDATE Usuario 
                    SET idRol = :idRol, 
                        NombreUsuario = :nombreUsuario, 
                        CorreoUsuario = :correo
                    WHERE idUsuario = :idUsuario";
            
            $stmt = $this->conexion->prepare($query);
            
            // Bind de parámetros
            $stmt->bindParam(':idRol', $this->idRol);
            $stmt->bindParam(':nombreUsuario', $this->NombreUsuario);
            $stmt->bindParam(':correo', $this->CorreoUsuario);
            $stmt->bindParam(':idUsuario', $this->idUsuario);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar contraseña del usuario
     * @param string $nuevaContrasena Nueva contraseña sin encriptar
     * @return bool True si se actualizó exitosamente, false en caso contrario
     */
    public function actualizarContrasena($nuevaContrasena) {
        try {
            $query = "UPDATE Usuario 
                    SET ContrasenaUsuario = :contrasena
                    WHERE idUsuario = :idUsuario";
            
            $stmt = $this->conexion->prepare($query);
            
            $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
            
            $stmt->bindParam(':contrasena', $contrasenaHash);
            $stmt->bindParam(':idUsuario', $this->idUsuario);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error al actualizar contraseña: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar usuario
     * @return bool True si se eliminó exitosamente, false en caso contrario
     */
    public function eliminar() {
        try {
            $query = "DELETE FROM Usuario WHERE idUsuario = :idUsuario";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idUsuario', $this->idUsuario);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // ==================== MÉTODOS DE AUTENTICACIÓN ====================
    
    /**
     * Login de usuario
     * @param string $correo Email del usuario
     * @param string $contrasena Contraseña sin encriptar
     * @return array|false Datos del usuario si login exitoso, false en caso contrario
     */
    public function login($correo, $contrasena) {
        try {
            $query = "SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, 
                            u.ContrasenaUsuario, u.idRol, r.NombreRol
                    FROM Usuario u
                    INNER JOIN Rol r ON u.idRol = r.idRol
                    WHERE u.CorreoUsuario = :correo";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && password_verify($contrasena, $row['ContrasenaUsuario'])) {
                // Remover contraseña del array de retorno por seguridad
                unset($row['ContrasenaUsuario']);
                return $row;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un correo ya existe en la base de datos
     * @param string $correo Email a verificar
     * @param int|null $idUsuarioExcluir ID de usuario a excluir de la búsqueda (para actualizaciones)
     * @return bool True si el correo existe, false en caso contrario
     */
    public function correoExiste($correo, $idUsuarioExcluir = null) {
        try {
            $query = "SELECT idUsuario FROM Usuario WHERE CorreoUsuario = :correo";
            
            if ($idUsuarioExcluir !== null) {
                $query .= " AND idUsuario != :idUsuarioExcluir";
            }
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':correo', $correo);
            
            if ($idUsuarioExcluir !== null) {
                $stmt->bindParam(':idUsuarioExcluir', $idUsuarioExcluir);
            }
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error al verificar correo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener usuarios por rol
     * @param int $idRol ID del rol a filtrar
     * @return array|false Array de usuarios o false si hay error
     */
    public function leerPorRol($idRol) {
        try {
            $query = "SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, 
                            u.idRol, r.NombreRol
                    FROM Usuario u
                    INNER JOIN Rol r ON u.idRol = r.idRol
                    WHERE u.idRol = :idRol
                    ORDER BY u.NombreUsuario ASC";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':idRol', $idRol);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al leer usuarios por rol: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar usuarios por nombre
     * @param string $termino Término de búsqueda
     * @return array|false Array de usuarios o false si hay error
     */
    public function buscarPorNombre($termino) {
        try {
            $query = "SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, 
                            u.idRol, r.NombreRol
                    FROM Usuario u
                    INNER JOIN Rol r ON u.idRol = r.idRol
                    WHERE u.NombreUsuario LIKE :termino
                    ORDER BY u.NombreUsuario ASC";
            
            $stmt = $this->conexion->prepare($query);
            $terminoBusqueda = "%{$termino}%";
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al buscar usuarios: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Contar total de usuarios
     * @return int Número total de usuarios
     */
    public function contarUsuarios() {
        try {
            $query = "SELECT COUNT(*) as total FROM Usuario";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return intval($row['total']);

        } catch (PDOException $e) {
            error_log("Error al contar usuarios: " . $e->getMessage());
            return 0;
        }
    }
}
?>
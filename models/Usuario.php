<?php
/**
 * Usuario - Modelo de Usuario
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona operaciones CRUD y autenticación de usuarios del sistema
 * Trabaja con las tablas Usuario y Rol
 */

class Usuario {
    private $pdo;

    /**
     * Constructor - Establece conexión con la base de datos
     */
    public function __construct() {
        $dbConfig = require __DIR__ . '/../config/database.php';
        try {
            $this->pdo = new PDO(
                "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4",
                $dbConfig['user'],
                $dbConfig['password']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
            die('No se pudo conectar a la base de datos.');
        }
    }

    /**
     * Autenticar usuario con nombre de usuario y contraseña
     * 
     * @param string $nombreUsuario Nombre de usuario
     * @param string $contrasena Contraseña en texto plano
     * @return array|false Datos del usuario si autenticación exitosa, false si falla
     */
    public function autenticar($nombreUsuario, $contrasena) {
        // Buscar usuario por nombre
        $usuario = $this->findByUsername($nombreUsuario);
        
        if (!$usuario) {
            return false;
        }
        
        // Verificar contraseña
        // NOTA: Asumiendo que las contraseñas están hasheadas con password_hash()
        // Si las contraseñas están en texto plano (NO RECOMENDADO), usar: $contrasena === $usuario['ContrasenaUsuario']
        if (password_verify($contrasena, $usuario['ContrasenaUsuario'])) {
            // Actualizar fecha de último login
            $this->updateLastLogin($usuario['idUsuario']);
            
            // Retornar datos del usuario (sin la contraseña por seguridad)
            unset($usuario['ContrasenaUsuario']);
            return $usuario;
        }
        
        return false;
    }

    /**
     * Buscar usuario por nombre de usuario
     * 
     * @param string $username Nombre de usuario
     * @return array|false Datos del usuario o false si no existe
     */
    public function findByUsername($username) {
        $query = "SELECT u.idUsuario, u.NombreUsuario, u.ContrasenaUsuario, 
                        u.CorreoUsuario, u.idRol, r.NombreRol as role_name 
                FROM Usuario u 
                INNER JOIN Rol r ON u.idRol = r.idRol 
                WHERE u.NombreUsuario = :username";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    /**
     * Buscar usuario por ID
     * 
     * @param int $id ID del usuario
     * @return array|false Datos del usuario o false si no existe
     */
    public function findById($id) {
        $query = "SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, 
                        u.idRol, r.NombreRol, r.DescripcionRol
                FROM Usuario u 
                INNER JOIN Rol r ON u.idRol = r.idRol 
                WHERE u.idUsuario = :id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Buscar usuario por email
     * 
     * @param string $email Correo electrónico
     * @return array|false Datos del usuario o false si no existe
     */
    public function findByEmail($email) {
        $query = "SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, 
                        u.idRol, r.NombreRol
                FROM Usuario u 
                INNER JOIN Rol r ON u.idRol = r.idRol 
                WHERE u.CorreoUsuario = :email";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos los usuarios del sistema
     * 
     * @return array Lista de todos los usuarios
     */
    public function getAll() {
                $query = "SELECT u.idUsuario, u.NombreUsuario as nombre_usuario, u.CorreoUsuario as correo, 
                                 u.idRol, r.NombreRol as role_name, r.DescripcionRol
                          FROM Usuario u 
                          INNER JOIN Rol r ON u.idRol = r.idRol
                          ORDER BY u.NombreUsuario ASC";        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener usuarios por rol
     * 
     * @param int $idRol ID del rol
     * @return array Lista de usuarios con ese rol
     */
    public function getByRol($idRol) {
        $query = "SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, 
                        u.idRol, r.NombreRol
                FROM Usuario u 
                INNER JOIN Rol r ON u.idRol = r.idRol 
                WHERE u.idRol = :idRol
                ORDER BY u.NombreUsuario ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['idRol' => $idRol]);
        return $stmt->fetchAll();
    }

    /**
     * Crear un nuevo usuario
     * 
     * @param array $datos Datos del usuario (NombreUsuario, ContrasenaUsuario, CorreoUsuario, idRol)
     * @return int|false ID del usuario creado o false si falla
     */
    public function crear($datos) {
        try {
            // Verificar que el nombre de usuario no exista
            if ($this->findByUsername($datos['NombreUsuario'])) {
                throw new Exception('El nombre de usuario ya existe');
            }

            // Verificar que el email no exista
            if ($this->findByEmail($datos['CorreoUsuario'])) {
                throw new Exception('El correo electrónico ya está registrado');
            }

            // Hashear la contraseña
            $contrasenaHash = password_hash($datos['ContrasenaUsuario'], PASSWORD_DEFAULT);

            $query = "INSERT INTO Usuario (idRol, NombreUsuario, ContrasenaUsuario, CorreoUsuario) 
                    VALUES (:idRol, :nombreUsuario, :contrasena, :email)";
            
            $stmt = $this->pdo->prepare($query);
            $resultado = $stmt->execute([
                'idRol' => $datos['idRol'],
                'nombreUsuario' => $datos['NombreUsuario'],
                'contrasena' => $contrasenaHash,
                'email' => $datos['CorreoUsuario']
            ]);

            return $resultado ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log('Error al crear usuario: ' . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar datos de un usuario
     * 
     * @param int $id ID del usuario
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó, false si falla
     */
    public function actualizar($id, $datos) {
        try {
            // Construir query dinámicamente según campos a actualizar
            $campos = [];
            $valores = ['id' => $id];

            if (isset($datos['NombreUsuario'])) {
                $campos[] = "NombreUsuario = :nombreUsuario";
                $valores['nombreUsuario'] = $datos['NombreUsuario'];
            }

            if (isset($datos['CorreoUsuario'])) {
                $campos[] = "CorreoUsuario = :email";
                $valores['email'] = $datos['CorreoUsuario'];
            }

            if (isset($datos['idRol'])) {
                $campos[] = "idRol = :idRol";
                $valores['idRol'] = $datos['idRol'];
            }

            // Si se proporciona nueva contraseña, hashearla
            if (isset($datos['ContrasenaUsuario']) && !empty($datos['ContrasenaUsuario'])) {
                $campos[] = "ContrasenaUsuario = :contrasena";
                $valores['contrasena'] = password_hash($datos['ContrasenaUsuario'], PASSWORD_DEFAULT);
            }

            if (empty($campos)) {
                return false; // No hay nada que actualizar
            }

            $query = "UPDATE Usuario SET " . implode(', ', $campos) . " WHERE idUsuario = :id";
            
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute($valores);

        } catch (PDOException $e) {
            error_log('Error al actualizar usuario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar contraseña de un usuario
     * 
     * @param int $id ID del usuario
     * @param string $nuevaContrasena Nueva contraseña en texto plano
     * @return bool True si se actualizó, false si falla
     */
    public function cambiarContrasena($id, $nuevaContrasena) {
        try {
            $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

            $query = "UPDATE Usuario SET ContrasenaUsuario = :contrasena WHERE idUsuario = :id";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                'contrasena' => $contrasenaHash,
                'id' => $id
            ]);

        } catch (PDOException $e) {
            error_log('Error al cambiar contraseña: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar un usuario
     * 
     * @param int $id ID del usuario
     * @return bool True si se eliminó, false si falla
     */
    public function eliminar($id) {
        try {
            $query = "DELETE FROM Usuario WHERE idUsuario = :id";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute(['id' => $id]);

        } catch (PDOException $e) {
            error_log('Error al eliminar usuario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Contar total de usuarios
     * 
     * @return int Número total de usuarios
     */
    public function contarTotal() {
        $query = "SELECT COUNT(*) as total FROM Usuario";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return (int) $resultado['total'];
    }

    /**
     * Contar usuarios por rol
     * 
     * @return array Array asociativo con idRol => cantidad
     */
    public function contarPorRol() {
        $query = "SELECT r.NombreRol, COUNT(u.idUsuario) as cantidad 
                FROM Rol r 
                LEFT JOIN Usuario u ON r.idRol = u.idRol 
                GROUP BY r.idRol, r.NombreRol";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Validar si un email ya está registrado
     * 
     * @param string $email Email a validar
     * @param int|null $exceptoId ID del usuario a excluir de la búsqueda (para actualizaciones)
     * @return bool True si el email existe, false si no
     */
    public function emailExiste($email, $exceptoId = null) {
        if ($exceptoId) {
            $query = "SELECT COUNT(*) as total FROM Usuario 
                    WHERE CorreoUsuario = :email AND idUsuario != :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email, 'id' => $exceptoId]);
        } else {
            $query = "SELECT COUNT(*) as total FROM Usuario WHERE CorreoUsuario = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email]);
        }
        
        $resultado = $stmt->fetch();
        return $resultado['total'] > 0;
    }

    /**
     * Validar si un nombre de usuario ya existe
     * 
     * @param string $nombreUsuario Nombre de usuario a validar
     * @param int|null $exceptoId ID del usuario a excluir de la búsqueda
     * @return bool True si el nombre existe, false si no
     */
    public function nombreUsuarioExiste($nombreUsuario, $exceptoId = null) {
        if ($exceptoId) {
            $query = "SELECT COUNT(*) as total FROM Usuario 
                    WHERE NombreUsuario = :nombre AND idUsuario != :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['nombre' => $nombreUsuario, 'id' => $exceptoId]);
        } else {
            $query = "SELECT COUNT(*) as total FROM Usuario WHERE NombreUsuario = :nombre";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['nombre' => $nombreUsuario]);
        }
        
        $resultado = $stmt->fetch();
        return $resultado['total'] > 0;
    }
}
?>
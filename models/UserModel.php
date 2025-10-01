<?php
class UserModel {
    private $pdo;

    public function __construct() {
        $dbConfig = require __DIR__ . '/../config/database.php';
        try {
            $this->pdo = new PDO(
                "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4",
                $dbConfig['user'],
                $dbConfig['password']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
            die('No se pudo conectar a la base de datos.');
        }
    }

    public function findByUsername($username) {
        $query = "SELECT u.*, r.nombre as role_name 
                FROM usuarios u 
                JOIN roles r ON u.rol_id = r.id 
                WHERE u.nombre_usuario = ? AND u.estado = 'activo'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateLastLogin($userId) {
        try {
            $query = "UPDATE usuarios SET ultimo_ingreso = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$userId]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar último login: " . $e->getMessage());
            return false;
        }
    }
}
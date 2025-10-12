<?php
/**
 * Clase Database - Gestión de Conexión (Singleton)
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 */

class Database {
    private static $instance = null;
    private $conn;
    
    /**
     * Constructor privado (Singleton)
     */
    private function __construct() {
        // Cargar configuración desde config/database.php
        $configPath = __DIR__ . '/../config/database.php';
        
        if (!file_exists($configPath)) {
            die("Error: Archivo de configuración no encontrado");
        }
        
        $config = require $configPath;
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $config['user'], $config['password'], $options);
            
        } catch(PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor contacte al administrador.");
        }
    }
    
    /**
     * Obtener instancia única (Singleton)
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtener conexión PDO
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Prevenir clonación del objeto
     */
    private function __clone() {}
    
    /**
     * Prevenir deserialización del objeto
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton");
    }
}
?>
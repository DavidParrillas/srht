<?php
/**
 * Controlador de Paquetes
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona el CRUD de los paquetes turísticos
 */

require_once 'controllers/AuthController.php';

class PaquetesController {
    
    private $db;
    
    /**
     * Constructor
     */
    public function __construct($conexion) {
        $this->db = $conexion;
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Listar todos los paquetes
     * Accesible para roles de administración.
     */
    public function index() {
        // Requerir autenticación y rol específico
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        $page_title = "Gestión de Paquetes";
        $active_page = "paquetes";
        $child_view = 'views/paquetes/index.php';
        
        require_once 'views/layouts/main.php';
    }
}
?>
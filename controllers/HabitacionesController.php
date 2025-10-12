<?php
/**
 * Controlador de Habitaciones
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona el CRUD de las habitaciones del hotel
 */

require_once 'controllers/AuthController.php';

class HabitacionesController {
    
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
     * Listar todas las habitaciones
     * Accesible para roles con permisos de gestión.
     */
    public function index() {
        // Requerir autenticación y rol específico
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        
        $page_title = "Gestión de Habitaciones";
        $active_page = "habitaciones";
        $child_view = 'views/habitaciones/index.php';
        
        require_once 'views/layouts/main.php';
    }
}
?>
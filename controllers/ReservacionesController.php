<?php
/**
 * Controlador de Reservaciones
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona el CRUD de reservaciones del hotel
 */

require_once 'controllers/AuthController.php';

class ReservacionesController {
    
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
     * Listar todas las reservaciones
     * Accesible para roles con permisos de gestión.
     */
    public function index() {
        // Requerir autenticación y rol específico
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        
        $page_title = "Gestión de Reservaciones";
        $active_page = "reservaciones";
        $child_view = 'views/reservaciones/index.php';
        
        require_once 'views/layouts/main.php';
    }
}
?>
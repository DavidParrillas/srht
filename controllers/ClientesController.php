<?php
/**
 * Controlador de Clientes
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona el CRUD de clientes del hotel
 */

require_once 'controllers/AuthController.php';

class ClientesController {
    
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
     * Listar todos los clientes
     * Accesible para roles con permisos de gestión.
     */
    public function index() {
        // Requerir autenticación y rol específico
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        
        $page_title = "Gestión de Clientes";
        $active_page = "clientes";
        $child_view = 'views/clientes/index.php';
        
        require_once 'views/layouts/main.php';
    }
}
?>
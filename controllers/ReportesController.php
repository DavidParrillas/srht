<?php
/**
 * Controlador de Reportes
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Genera y muestra reportes del sistema
 */

require_once 'controllers/AuthController.php';

class ReportesController {
    
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
     * Página principal de reportes
     * Solo accesible para Administradores y Gerencia
     */
    public function index() {
        // Requerir autenticación y rol específico
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        $page_title = "Reportes Gerenciales";
        $active_page = "reportes";
        $child_view = 'views/reportes/index.php';
        
        require_once 'views/layouts/main.php';
    }
}
?>
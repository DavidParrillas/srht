<?php
/**
 * Controlador del Dashboard
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 */

require_once 'controllers/AuthController.php';
require_once 'models/Reporte.php'; // Usaremos el modelo de reportes para las estadísticas

class DashboardController {
    
    private $db;
    private $reporteModel;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->reporteModel = new Reporte($this->db);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        AuthController::requerirAutenticacion();

        $page_title = "Dashboard";
        $active_page = "dashboard";
        $child_view = 'views/dashboard/content.php';
        
        // Obtener las estadísticas del modelo
        $stats = $this->reporteModel->getDashboardStats();
        
        require_once 'views/layouts/main.php';
    }
}
?>
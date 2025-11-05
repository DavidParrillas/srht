<?php
/**
 * Controlador de Reportes
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Genera y muestra reportes del sistema
 */

require_once 'controllers/AuthController.php';
require_once 'models/Reporte.php'; // Incluir el nuevo modelo

class ReportesController {
    
    private $db;
    private $reporteModel;
    
    /**
     * Constructor
     */
    public function __construct($conexion) {
        $this->db = $conexion;
        $this->reporteModel = new Reporte($this->db);
        
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

    /**
     * Genera y muestra un reporte específico
     */
    public function generar() {
        // Requerir autenticación y rol
        AuthController::requerirRol(['Administrador', 'Gerencia']);

        $tipo = $_GET['tipo'] ?? 'desconocido';
        $active_page = "reportes";

        // Fechas por defecto (ej. último mes)
        $fechaFin = date('Y-m-d');
        $fechaInicio = date('Y-m-d', strtotime('-1 month'));

        // Sobrescribir si vienen del formulario de filtro
        if (isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio'])) {
            $fechaInicio = $_GET['fecha_inicio'];
        }
        if (isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin'])) {
            $fechaFin = $_GET['fecha_fin'];
        }

        $datos = [];
        $child_view = '';
        $page_title = 'Reporte Desconocido';

        switch ($tipo) {
            case 'ocupacion':
                $page_title = "Reporte de Ocupación";
                $datos = $this->reporteModel->getReporteOcupacion($fechaInicio, $fechaFin);
                $child_view = 'views/reportes/ocupacion.php';
                break;

            case 'ingresos':
                $page_title = "Reporte de Ingresos";
                $datos = $this->reporteModel->getReporteIngresos($fechaInicio, $fechaFin);
                $child_view = 'views/reportes/ingresos.php';
                break;

            case 'historial':
                $page_title = "Historial de Reservaciones";
                $datos = $this->reporteModel->getHistorialReservaciones($fechaInicio, $fechaFin);
                $child_view = 'views/reportes/historial.php';
                break;

            default:
                $_SESSION['error_message'] = "El tipo de reporte solicitado no es válido.";
                header('Location: index.php?controller=reportes&action=index');
                exit();
        }

        if ($datos === false) {
            $_SESSION['error_message'] = "Ocurrió un error al generar el reporte.";
            // Podríamos redirigir, pero es mejor mostrar la vista con un mensaje
            $datos = []; // Asegurarse de que $datos sea un array vacío
        }

        // Cargar el layout principal que incluirá la vista del reporte
        require_once 'views/layouts/main.php';
    }
}
?>
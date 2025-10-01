<?php
class ReportesController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index() {
        $page_title = "Reportes y Análisis";
        $active_page = "reportes";
        $child_view = __DIR__ . '/../views/reportes/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function ocupacion() {
        $page_title = "Reporte de Ocupación";
        $active_page = "reportes";
        $child_view = __DIR__ . '/../views/reportes/ocupacion.php';
        
        // TODO: Procesar datos para el reporte
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function ingresos() {
        $page_title = "Reporte de Ingresos";
        $active_page = "reportes";
        $child_view = __DIR__ . '/../views/reportes/ingresos.php';
        
        // TODO: Procesar datos para el reporte
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function exportar() {
        $tipo = $_GET['tipo'] ?? '';
        $formato = $_GET['formato'] ?? 'pdf';
        
        // TODO: Generar y descargar reporte
        
        header('Location: index.php?controller=reportes');
        exit;
    }
}
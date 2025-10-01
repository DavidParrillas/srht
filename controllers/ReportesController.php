<?php
class ReportesController {
    /**
     * Constructor. Verifica si el usuario ha iniciado sesión.
     * Si no, lo redirige a la página de login.
     */
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    /**
     * Muestra la página principal de la sección de reportes.
     */
    public function index() {
        $page_title = "Reportes y Análisis";
        $active_page = "reportes";
        $child_view = __DIR__ . '/../views/reportes/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

}
<?php
class PaquetesController {
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
     * Muestra la página principal de gestión de paquetes.
     */
    public function index() {
        $page_title = "Gestión de Paquetes";
        $active_page = "paquetes";
        $child_view = __DIR__ . '/../views/paquetes/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

}

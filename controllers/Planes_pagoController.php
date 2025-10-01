<?php
class Planes_pagoController {
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
     * Muestra la página principal de gestión de planes de pago.
     */
    public function index() {
        $page_title = "Gestión de Planes de Pago";
        $active_page = "planes_pago";
        $child_view = __DIR__ . '/../views/planes_pago/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

}

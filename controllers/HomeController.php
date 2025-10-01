<?php
class HomeController {
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
     * Muestra el dashboard principal.
     */
    public function index() {
        $page_title = "Dashboard";
        $active_page = "dashboard";
        $child_view = "views/dashboard/content.php";
        
        include("views/layouts/main.php");
    }
}
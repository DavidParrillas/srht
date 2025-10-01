<?php
class HabitacionesController {
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
     * Muestra la página principal de gestión de habitaciones.
     */
    public function index() {
        $page_title = "Gestión de Habitaciones";
        $active_page = "habitaciones";
        $child_view = "views/habitaciones/index.php";
        
        include("views/layouts/main.php");
    }
}
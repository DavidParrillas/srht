<?php
class ClientesController {
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
     * Muestra la página principal de gestión de clientes.
     * Carga la lista de todos los clientes para mostrarlos en una tabla.
     */
    public function index() {
        $page_title = "Gestión de Clientes";
        $active_page = "clientes";
        $child_view = "views/clientes/index.php";

        include("views/layouts/main.php");
    }
}
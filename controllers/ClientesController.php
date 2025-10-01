<?php
class ClientesController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index() {
        $page_title = "Gestión de Clientes";
        $active_page = "clientes";
        $child_view = "views/clientes/index.php";
        
        // TODO: Obtener lista de clientes
        $clientes = [];
        
        include("views/layouts/main.php");
    }

    public function crear() {
        $page_title = "Crear Cliente";
        $active_page = "clientes";
        $child_view = "views/clientes/crear.php";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario de creación
            header('Location: index.php?controller=clientes');
            exit;
        }
        
        include("views/layouts/main.php");
    }

    public function editar() {
        $page_title = "Editar Cliente";
        $active_page = "clientes";
        $child_view = "views/clientes/editar.php";
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=clientes');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario de edición
            header('Location: index.php?controller=clientes');
            exit;
        }
        
        // TODO: Obtener datos del cliente
        $cliente = null;
        
        include("views/layouts/main.php");
    }

    public function ver() {
        $page_title = "Ver Cliente";
        $active_page = "clientes";
        $child_view = "views/clientes/ver.php";
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=clientes');
            exit;
        }
        
        // TODO: Obtener datos del cliente
        $cliente = null;
        
        include("views/layouts/main.php");
    }

    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=clientes');
            exit;
        }
        
        // TODO: Eliminar cliente
        
        header('Location: index.php?controller=clientes');
        exit;
    }
}
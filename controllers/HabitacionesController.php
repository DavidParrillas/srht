<?php
class HabitacionesController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index() {
        $page_title = "Gestión de Habitaciones";
        $active_page = "habitaciones";
        $child_view = "views/habitaciones/index.php";
        
        // TODO: Obtener lista de habitaciones
        $habitaciones = [];
        
        include("views/layouts/main.php");
    }

    public function crear() {
        $page_title = "Crear Habitación";
        $active_page = "habitaciones";
        $child_view = "views/habitaciones/crear.php";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario de creación
            header('Location: index.php?controller=habitaciones');
            exit;
        }
        
        include("views/layouts/main.php");
    }

    public function editar() {
        $page_title = "Editar Habitación";
        $active_page = "habitaciones";
        $child_view = "views/habitaciones/editar.php";
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=habitaciones');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario de edición
            header('Location: index.php?controller=habitaciones');
            exit;
        }
        
        // TODO: Obtener datos de la habitación
        $habitacion = null;
        
        include("views/layouts/main.php");
    }

    public function ver() {
        $page_title = "Ver Habitación";
        $active_page = "habitaciones";
        $child_view = "views/habitaciones/ver.php";
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=habitaciones');
            exit;
        }
        
        // TODO: Obtener datos de la habitación
        $habitacion = null;
        
        include("views/layouts/main.php");
    }

    public function cambiarEstado() {
        $id = $_GET['id'] ?? null;
        $estado = $_GET['estado'] ?? null;
        
        if (!$id || !$estado) {
            header('Location: index.php?controller=habitaciones');
            exit;
        }
        
        // TODO: Actualizar estado de la habitación
        
        header('Location: index.php?controller=habitaciones');
        exit;
    }
}
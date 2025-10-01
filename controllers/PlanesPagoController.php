<?php
class PlanesPagoController {
    public function __construct() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index() {
        $page_title = "Gestión de Planes de Pago";
        $active_page = "planes_pago";
        $child_view = __DIR__ . '/../views/planes_pago/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function crear() {
        $page_title = "Crear Plan de Pago";
        $active_page = "planes_pago";
        $child_view = __DIR__ . '/../views/planes_pago/crear.php';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario
            header('Location: index.php?controller=planes_pago');
            exit;
        }
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=planes_pago');
            exit;
        }

        $page_title = "Editar Plan de Pago";
        $active_page = "planes_pago";
        $child_view = __DIR__ . '/../views/planes_pago/editar.php';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario
            header('Location: index.php?controller=planes_pago');
            exit;
        }
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function ver() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=planes_pago');
            exit;
        }

        $page_title = "Ver Plan de Pago";
        $active_page = "planes_pago";
        $child_view = __DIR__ . '/../views/planes_pago/ver.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
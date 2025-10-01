<?php
class PaquetesController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index() {
        $page_title = "Gestión de Paquetes";
        $active_page = "paquetes";
        $child_view = __DIR__ . '/../views/paquetes/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function crear() {
        $page_title = "Crear Paquete";
        $active_page = "paquetes";
        $child_view = __DIR__ . '/../views/paquetes/crear.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario
            header('Location: index.php?controller=paquetes');
            exit;
        }
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=paquetes');
            exit;
        }
        $page_title = "Editar Paquete";
        $active_page = "paquetes";
        $child_view = __DIR__ . '/../views/paquetes/editar.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario
            header('Location: index.php?controller=paquetes');
            exit;
        }
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function ver() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=paquetes');
            exit;
        }
        $page_title = "Ver Paquete";
        $active_page = "paquetes";
        $child_view = __DIR__ . '/../views/paquetes/ver.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}

<?php
class ReservacionesController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index() {
        $page_title = "Gestión de Reservaciones";
        $active_page = "reservaciones";
        $child_view = __DIR__ . '/../views/reservaciones/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function crear() {
        $page_title = "Crear Reservación";
        $active_page = "reservaciones";
        $child_view = __DIR__ . '/../views/reservaciones/crear.php';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario
            header('Location: index.php?controller=reservaciones');
            exit;
        }
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=reservaciones');
            exit;
        }

        $page_title = "Editar Reservación";
        $active_page = "reservaciones";
        $child_view = __DIR__ . '/../views/reservaciones/editar.php';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario
            header('Location: index.php?controller=reservaciones');
            exit;
        }
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function ver() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=reservaciones');
            exit;
        }

        $page_title = "Ver Reservación";
        $active_page = "reservaciones";
        $child_view = __DIR__ . '/../views/reservaciones/ver.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function checkIn() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=reservaciones');
            exit;
        }

        // TODO: Procesar check-in
        header('Location: index.php?controller=reservaciones');
        exit;
    }

    public function checkOut() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=reservaciones');
            exit;
        }

        // TODO: Procesar check-out
        header('Location: index.php?controller=reservaciones');
        exit;
    }
}
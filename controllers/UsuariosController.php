<?php
class UsuariosController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index() {
        $page_title = "Gestión de Usuarios";
        $active_page = "usuarios";
        $child_view = __DIR__ . '/../views/usuarios/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function crear() {
        $page_title = "Crear Usuario";
        $active_page = "usuarios";
        $child_view = __DIR__ . '/../views/usuarios/crear.php';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario
            header('Location: index.php?controller=usuarios');
            exit;
        }
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=usuarios');
            exit;
        }

        $page_title = "Editar Usuario";
        $active_page = "usuarios";
        $child_view = __DIR__ . '/../views/usuarios/editar.php';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Procesar formulario
            header('Location: index.php?controller=usuarios');
            exit;
        }
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function ver() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=usuarios');
            exit;
        }

        $page_title = "Ver Usuario";
        $active_page = "usuarios";
        $child_view = __DIR__ . '/../views/usuarios/ver.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
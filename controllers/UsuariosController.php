<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuariosController {
    private $userModel;
    /**
     * Constructor. Verifica si el usuario ha iniciado sesión.
     * Si no, lo redirige a la página de login.
     */
    public function __construct() {
        $this->userModel = new Usuario();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    /**
     * Muestra la página principal de gestión de usuarios.
     */
    public function index() {
        $usuarios = $this->userModel->getAll(); // Obtenemos todos los usuarios
        $page_title = "Gestión de Usuarios";
        $active_page = "usuarios";
        $child_view = __DIR__ . '/../views/usuarios/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

}
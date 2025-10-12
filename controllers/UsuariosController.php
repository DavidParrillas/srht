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
        // if (!isset($_SESSION['user_id'])) {
        //     header('Location: index.php?controller=auth&action=login');
        //     exit;
        // }
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

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function crear() {
        $page_title = "Crear Nuevo Usuario";
        $active_page = "usuarios";
        $child_view = __DIR__ . '/../views/usuarios/crear.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreUsuario = $_POST['nombre_usuario'] ?? '';
            $correoUsuario = $_POST['correo_usuario'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $idRol = $_POST['id_rol'] ?? 0;

            // Validación simple
            if (empty($nombreUsuario) || empty($correoUsuario) || empty($contrasena) || empty($idRol)) {
                // Manejar error, quizás redirigir con un mensaje
                header('Location: index.php?controller=usuarios&action=crear&error=faltan_datos');
                exit;
            }

            // Hash de la contraseña
            $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

            // Llamar al modelo para crear el usuario
            $datos = [
                'NombreUsuario' => $nombreUsuario,
                'CorreoUsuario' => $correoUsuario,
                'ContrasenaUsuario' => $hashedPassword,
                'idRol' => $idRol
            ];
            $this->userModel->crear($datos);

            // Redirigir a la lista de usuarios
            header('Location: index.php?controller=usuarios&action=index');
            exit;
        }
    }


}
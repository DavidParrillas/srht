<?php
class HomeController {
    /**
     * Constructor.
     */
    public function __construct() {
        // Iniciar sesión si no está iniciada para poder acceder a $_SESSION
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Redirige al dashboard si el usuario está logueado,
     * o a la página de login si no lo está.
     */
    public function index() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        } else {
            header('Location: index.php?controller=auth&action=mostrarLogin');
            exit();
        }
    }
}
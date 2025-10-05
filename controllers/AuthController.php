<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuario;

    /**
     * Constructor. Inicializa el modelo de usuario.
     */
    public function __construct() {
        $this->usuario = new Usuario();
    }

    /**
        * Muestra el formulario de login y maneja la autenticación.
     */
    public function login() {
        // La sesión se iniciará en el router para evitar redundancia

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['nombre_usuario'] ?? '';
            $password = $_POST['contrasena'] ?? '';
            
            try {
                $user = $this->usuario->findByUsername($username);
                
                error_log('Intento login usuario: ' . $username);
                if ($user) {
                    error_log('Usuario encontrado: ' . print_r($user, true));
                    error_log('Hash almacenado: ' . $user['contrasena']);
                    error_log('Contraseña ingresada: ' . $password);
                    $verificacion = password_verify($password, $user['contrasena']);
                    error_log('Resultado password_verify: ' . ($verificacion ? 'true' : 'false'));
                    if ($verificacion) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['nombre_usuario'];
                        $_SESSION['role'] = $user['role_name'];
                        // Actualizar último login
                        $this->usuario->updateLastLogin($user['id']);
                        error_log("Login exitoso - Usuario: $username, Rol: {$user['role_name']}");
                        header('Location: index.php?controller=home');
                        exit;
                    } else {
                        error_log('Password no coincide.');
                        $error_message = "Usuario o contraseña incorrectos.";
                    }
                } else {
                    error_log("Usuario no encontrado: $username");
                    $error_message = "Usuario o contraseña incorrectos.";
                }
                
            } catch (PDOException $e) {
                error_log("Error de base de datos en login: " . $e->getMessage());
                // No mostrar el error de PDO al usuario final por seguridad
                $error_message = "Ocurrió un error inesperado. Por favor, intente de nuevo.";
            }
        }
        
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Cierra la sesión del usuario.
     * Destruye la sesión actual y redirige al formulario de login.
     */
    public function logout() {
        // La sesión ya debería estar iniciada
        session_destroy();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
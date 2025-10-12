<?php
/**
 * Controlador de Autenticación
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona login, logout y recuperación de contraseña
 */

require_once 'models/Usuario.php';

class AuthController {
    
    private $db;
    private $usuarioModel;
    
    /**
     * Constructor
     */
    public function __construct($conexion) {
        $this->db = $conexion;
        $this->usuarioModel = new Usuario($this->db);
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Mostrar formulario de login
     */
    public function mostrarLogin() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
        
        // Generar token CSRF si no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Mostrar vista de login
        require_once 'views/auth/login.php';
    }
    
    /**
     * Procesar login
     */
    public function login() {
        // Verificar que sea petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=auth&action=mostrarLogin');
            exit();
        }
        
        try {
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos requeridos
            if (empty($_POST['correo']) || empty($_POST['contrasena'])) {
                throw new Exception('Por favor complete todos los campos');
            }
            
            $correo = trim($_POST['correo']);
            $contrasena = $_POST['contrasena'];
            
            // Validar formato de correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Formato de correo electrónico inválido');
            }
            
            // Intentar login
            $usuario = $this->usuarioModel->login($correo, $contrasena);
            
            if ($usuario === false) {
                throw new Exception('Correo o contraseña incorrectos');
            }
            
            // Login exitoso - Configurar sesión
            $_SESSION['usuario_id'] = $usuario['idUsuario'];
            $_SESSION['usuario_nombre'] = $usuario['NombreUsuario'];
            $_SESSION['usuario_correo'] = $usuario['CorreoUsuario'];
            $_SESSION['usuario_rol_id'] = $usuario['idRol'];
            $_SESSION['usuario_rol_nombre'] = $usuario['NombreRol'];
            $_SESSION['login_time'] = time();
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            // Registrar último acceso (opcional)
            $this->registrarAcceso($usuario['idUsuario']);
            
            // Redirigir al dashboard
            header('Location: index.php?controller=dashboard&action=index');
            exit();
            
        } catch (Exception $e) {
            // Guardar mensaje de error en sesión
            $_SESSION['error_message'] = $e->getMessage();
            
            // Guardar correo para rellenar el formulario
            $_SESSION['login_correo'] = isset($correo) ? $correo : '';
            
            // Redirigir de vuelta al login
            header('Location: index.php?controller=auth&action=mostrarLogin');
            exit();
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Eliminar cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destruir sesión
        session_destroy();
        
        // Redirigir al login
        header('Location: index.php?controller=auth&action=mostrarLogin');
        exit();
    }
    
    /**
     * Verificar si el usuario está autenticado
     * @return bool
     */
    public static function estaAutenticado() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['usuario_id']);
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     * @param string|array $roles Rol o array de roles permitidos
     * @return bool
     */
    public static function tieneRol($roles) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!self::estaAutenticado()) {
            return false;
        }
        
        $rolUsuario = $_SESSION['usuario_rol_nombre'];
        
        // Si es un array de roles
        if (is_array($roles)) {
            return in_array($rolUsuario, $roles);
        }
        
        // Si es un solo rol
        return $rolUsuario === $roles;
    }
    
    /**
     * Requerir autenticación (middleware)
     * Redirige al login si no está autenticado
     */
    public static function requerirAutenticacion() {
        if (!self::estaAutenticado()) {
            $_SESSION['error_message'] = 'Debe iniciar sesión para acceder a esta página';
            header('Location: index.php?controller=auth&action=mostrarLogin');
            exit();
        }
    }
    
    /**
     * Requerir rol específico (middleware)
     * @param string|array $roles Rol o roles requeridos
     */
    public static function requerirRol($roles) {
        self::requerirAutenticacion();
        
        if (!self::tieneRol($roles)) {
            $_SESSION['error_message'] = 'No tiene permisos para acceder a esta página';
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
    }
    
    /**
     * Mostrar formulario de recuperación de contraseña
     */
    public function mostrarRecuperarContrasena() {
        require_once 'views/auth/recuperar_contrasena.php';
    }
    
    /**
     * Procesar solicitud de recuperación de contraseña
     */
    public function recuperarContrasena() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=auth&action=mostrarRecuperarContrasena');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            if (empty($_POST['correo'])) {
                throw new Exception('Por favor ingrese su correo electrónico');
            }
            
            $correo = trim($_POST['correo']);
            
            // Validar formato
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Formato de correo electrónico inválido');
            }
            
            // Aquí iría la lógica para enviar email de recuperación
            // Por ahora solo mostrar mensaje de éxito
            
            $_SESSION['success_message'] = 'Se han enviado las instrucciones de recuperación a su correo electrónico';
            header('Location: index.php?controller=auth&action=mostrarLogin');
            exit();
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=auth&action=mostrarRecuperarContrasena');
            exit();
        }
    }
    
    /**
     * Cambiar contraseña del usuario actual
     */
    public function cambiarContrasena() {
        self::requerirAutenticacion();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=dashboard&action=perfil');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos
            if (empty($_POST['contrasena_actual']) || empty($_POST['contrasena_nueva']) || empty($_POST['contrasena_confirmar'])) {
                throw new Exception('Por favor complete todos los campos');
            }
            
            $contrasenaActual = $_POST['contrasena_actual'];
            $contrasenaNueva = $_POST['contrasena_nueva'];
            $contrasenaConfirmar = $_POST['contrasena_confirmar'];
            
            // Validar que las contraseñas nuevas coincidan
            if ($contrasenaNueva !== $contrasenaConfirmar) {
                throw new Exception('Las contraseñas nuevas no coinciden');
            }
            
            // Validar longitud mínima
            if (strlen($contrasenaNueva) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
            
            // Verificar contraseña actual
            $correo = $_SESSION['usuario_correo'];
            $usuario = $this->usuarioModel->login($correo, $contrasenaActual);
            
            if ($usuario === false) {
                throw new Exception('La contraseña actual es incorrecta');
            }
            
            // Actualizar contraseña
            $this->usuarioModel->setIdUsuario($_SESSION['usuario_id']);
            $resultado = $this->usuarioModel->actualizarContrasena($contrasenaNueva);
            
            if (!$resultado) {
                throw new Exception('Error al actualizar la contraseña');
            }
            
            $_SESSION['success_message'] = 'Contraseña actualizada exitosamente';
            header('Location: index.php?controller=dashboard&action=perfil');
            exit();
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=dashboard&action=perfil');
            exit();
        }
    }
    
    /**
     * Registrar último acceso del usuario (opcional)
     * @param int $idUsuario
     */
    private function registrarAcceso($idUsuario) {
        try {
            $query = "UPDATE Usuario SET ultimo_acceso = NOW() WHERE idUsuario = :idUsuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario);
            $stmt->execute();
        } catch (PDOException $e) {
            // No es crítico, solo registrar en log
            error_log("Error al registrar acceso: " . $e->getMessage());
        }
    }
}
?>
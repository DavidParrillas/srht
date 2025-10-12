<?php
/**
 * AuthController - Controlador de Autenticación
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona el inicio de sesión, cierre de sesión y verificación de usuarios
 * Trabaja con la tabla Usuario y Rol de la base de datos
 */

// Incluir el modelo de Usuario
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    
    private $usuarioModel;
    
    /**
     * Constructor - Inicializa el modelo de Usuario
     */
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Mostrar formulario de login
     * Renderiza la vista de inicio de sesión
     */
    public function mostrarLogin() {
        // Si ya hay sesión activa, redirigir al dashboard
        if ($this->verificarSesion()) {
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
        
        // Incluir la vista de login
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Procesar inicio de sesión
     * Valida credenciales y crea sesión si son correctas
     */
    public function login() {
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=auth&action=mostrarLogin');
            exit();
        }
        
        // Obtener datos del formulario
        $nombreUsuario = $_POST['nombre_usuario'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';
        
        // Validar que los campos no estén vacíos
        if (empty($nombreUsuario) || empty($contrasena)) {
            $error_message = 'Por favor, complete todos los campos';
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }
        
        // Intentar autenticar al usuario
        $usuario = $this->usuarioModel->autenticar($nombreUsuario, $contrasena);
        
        if ($usuario) {
            // Credenciales correctas - crear sesión
            $this->crearSesion($usuario);
            
            // Registrar en log de auditoría (opcional)
            $this->registrarAccesoExitoso($usuario['idUsuario']);
            
            // Redirigir al dashboard
            header('Location: index.php?controller=dashboard&action=index');
            exit();
            
        } else {
            // Credenciales incorrectas
            $error_message = 'Usuario o contraseña incorrectos';
            
            // Registrar intento fallido (opcional - seguridad)
            $this->registrarIntentoFallido($nombreUsuario);
            
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }
    }
    
    /**
     * Cerrar sesión
     * Destruye la sesión activa y redirige al login
     */
    public function logout() {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Registrar cierre de sesión (opcional)
        if (isset($_SESSION['usuario_id'])) {
            $this->registrarCierreSesion($_SESSION['usuario_id']);
        }
        
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión si existe
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al login
        header('Location: index.php?controller=auth&action=mostrarLogin');
        exit();
    }
    
    /**
     * Crear sesión de usuario
     * Guarda información del usuario en variables de sesión
     * 
     * @param array $usuario Datos del usuario autenticado
     */
    private function crearSesion($usuario) {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
        
        // Guardar información del usuario en la sesión
        $_SESSION['usuario_id'] = $usuario['idUsuario'];
        $_SESSION['usuario_nombre'] = $usuario['NombreUsuario'];
        $_SESSION['usuario_email'] = $usuario['CorreoUsuario'];
        $_SESSION['usuario_rol_id'] = $usuario['idRol'];
        $_SESSION['usuario_rol_nombre'] = $usuario['NombreRol'];
        
        // Datos adicionales de sesión
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        
        // Configurar tiempo de expiración de sesión (30 minutos de inactividad)
        $_SESSION['expire_time'] = time() + (30 * 60);
    }
    
    /**
     * Verificar si existe una sesión activa válida
     * 
     * @return bool True si hay sesión activa, False si no
     */
    public function verificarSesion() {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si existe usuario en sesión
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }
        
        // Verificar tiempo de expiración por inactividad
        if (isset($_SESSION['expire_time']) && time() > $_SESSION['expire_time']) {
            // Sesión expirada por inactividad
            $this->logout();
            return false;
        }
        
        // Actualizar tiempo de última actividad
        $_SESSION['last_activity'] = time();
        $_SESSION['expire_time'] = time() + (30 * 60);
        
        return true;
    }
    
    /**
     * Verificar permisos de usuario según su rol
     * 
     * @param array $rolesPermitidos Array con nombres de roles permitidos
     * @return bool True si el usuario tiene permiso, False si no
     */
    public function verificarPermiso($rolesPermitidos) {
        // Verificar que hay sesión activa
        if (!$this->verificarSesion()) {
            return false;
        }
        
        // Verificar si el rol del usuario está en los roles permitidos
        $rolUsuario = $_SESSION['usuario_rol_nombre'];
        
        return in_array($rolUsuario, $rolesPermitidos);
    }
    
    /**
     * Middleware para proteger rutas
     * Redirige al login si no hay sesión activa
     */
    public function requerirAutenticacion() {
        if (!$this->verificarSesion()) {
            // Guardar la URL solicitada para redirigir después del login
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            
            header('Location: index.php?controller=auth&action=mostrarLogin');
            exit();
        }
    }
    
    /**
     * Middleware para verificar permisos por rol
     * Redirige si el usuario no tiene el rol requerido
     * 
     * @param array $rolesPermitidos Array con nombres de roles permitidos
     */
    public function requerirRol($rolesPermitidos) {
        $this->requerirAutenticacion();
        
        if (!$this->verificarPermiso($rolesPermitidos)) {
            // Usuario no tiene permiso - redirigir con mensaje de error
            $_SESSION['error_message'] = 'No tiene permisos para acceder a esta sección';
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
    }
    
    /**
     * Obtener información del usuario actual en sesión
     * 
     * @return array|null Datos del usuario o null si no hay sesión
     */
    public function getUsuarioActual() {
        if (!$this->verificarSesion()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email'],
            'rol_id' => $_SESSION['usuario_rol_id'],
            'rol_nombre' => $_SESSION['usuario_rol_nombre']
        ];
    }
    
    // =================================================
    // MÉTODOS DE AUDITORÍA (OPCIONALES)
    // =================================================
    
    /**
     * Registrar acceso exitoso en log de auditoría
     * 
     * @param int $usuarioId ID del usuario que inició sesión
     */
    private function registrarAccesoExitoso($usuarioId) {
        // Implementar si se requiere auditoría
        // Puede ser un archivo de log o tabla en la base de datos
        
        $logMessage = sprintf(
            "[%s] Usuario ID %d inició sesión exitosamente desde IP: %s\n",
            date('Y-m-d H:i:s'),
            $usuarioId,
            $_SERVER['REMOTE_ADDR']
        );
        
        // Guardar en archivo de log
        error_log($logMessage, 3, __DIR__ . '/../logs/auth.log');
    }
    
    /**
     * Registrar intento de acceso fallido
     * 
     * @param string $nombreUsuario Usuario que intentó acceder
     */
    private function registrarIntentoFallido($nombreUsuario) {
        $logMessage = sprintf(
            "[%s] Intento fallido de inicio de sesión - Usuario: %s - IP: %s\n",
            date('Y-m-d H:i:s'),
            $nombreUsuario,
            $_SERVER['REMOTE_ADDR']
        );
        
        error_log($logMessage, 3, __DIR__ . '/../logs/auth.log');
    }
    
    /**
     * Registrar cierre de sesión
     * 
     * @param int $usuarioId ID del usuario que cerró sesión
     */
    private function registrarCierreSesion($usuarioId) {
        $logMessage = sprintf(
            "[%s] Usuario ID %d cerró sesión\n",
            date('Y-m-d H:i:s'),
            $usuarioId
        );
        
        error_log($logMessage, 3, __DIR__ . '/../logs/auth.log');
    }
}
?>
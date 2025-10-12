<?php
/**
 * Controlador de Usuarios
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona el CRUD de usuarios del sistema
 */

require_once 'models/Usuario.php';
require_once 'controllers/AuthController.php';

class UsuariosController {
    
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
     * Listar todos los usuarios
     * Solo accesible para Administradores y Gerencia
     */
    public function index() {
        // Requerir autenticación y rol específico
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        try {
            // Obtener todos los usuarios
            $usuarios = $this->usuarioModel->leerTodos();
            
            $page_title = "Gestión de Usuarios";
            $active_page = "usuarios";
            $child_view = 'views/usuarios/index.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar usuarios: ' . $e->getMessage();
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
    }
    
    /**
     * Mostrar formulario de creación de usuario
     */
    public function crear() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        try {
            // Obtener roles para el select
            $roles = $this->obtenerRoles();
            
            $page_title = "Crear Nuevo Usuario";
            $active_page = "usuarios";
            $child_view = 'views/usuarios/crear.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar formulario: ' . $e->getMessage();
            header('Location: index.php?controller=usuarios&action=index');
            exit();
        }
    }
    
    /**
     * Guardar nuevo usuario
     */
    public function guardar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=usuarios&action=crear');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos requeridos
            if (empty($_POST['nombre']) || empty($_POST['correo']) || 
                empty($_POST['contrasena']) || empty($_POST['id_rol'])) {
                throw new Exception('Por favor complete todos los campos obligatorios');
            }
            
            $nombre = trim($_POST['nombre']);
            $correo = trim($_POST['correo']);
            $contrasena = $_POST['contrasena'];
            $idRol = intval($_POST['id_rol']);
            
            // Validar formato de correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Formato de correo electrónico inválido');
            }
            
            // Validar longitud de contraseña
            if (strlen($contrasena) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
            
            // Verificar si el correo ya existe
            if ($this->usuarioModel->correoExiste($correo)) {
                throw new Exception('El correo electrónico ya está registrado');
            }
            
            // Configurar datos del usuario
            $this->usuarioModel->setNombreUsuario($nombre);
            $this->usuarioModel->setCorreoUsuario($correo);
            $this->usuarioModel->setContrasenaUsuario($contrasena); // Se encripta automáticamente en el setter
            $this->usuarioModel->setIdRol($idRol);
            
            // Guardar usuario
            if ($this->usuarioModel->crear()) {
                $_SESSION['success_message'] = 'Usuario creado exitosamente';
                header('Location: index.php?controller=usuarios&action=index');
                exit();
            } else {
                throw new Exception('Error al guardar el usuario');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=usuarios&action=crear');
            exit();
        }
    }
    
    /**
     * Mostrar formulario de edición de usuario
     */
    public function editar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        try {
            // Validar ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de usuario inválido');
            }
            
            $idUsuario = intval($_GET['id']);
            
            // Obtener datos del usuario
            $this->usuarioModel->setIdUsuario($idUsuario);
            $usuario = $this->usuarioModel->leerPorId();
            
            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }
            
            // Obtener roles para el select
            $roles = $this->obtenerRoles();
            
            // Cargar vista
            require_once 'views/usuarios/editar.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=usuarios&action=index');
            exit();
        }
    }
    
    /**
     * Actualizar usuario existente
     */
    public function actualizar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=usuarios&action=index');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos requeridos
            if (empty($_POST['id']) || empty($_POST['nombre']) || 
                empty($_POST['correo']) || empty($_POST['id_rol'])) {
                throw new Exception('Por favor complete todos los campos obligatorios');
            }
            
            $idUsuario = intval($_POST['id']);
            $nombre = trim($_POST['nombre']);
            $correo = trim($_POST['correo']);
            $idRol = intval($_POST['id_rol']);
            
            // Validar formato de correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Formato de correo electrónico inválido');
            }
            
            // Verificar si el correo ya existe (excluyendo el usuario actual)
            if ($this->usuarioModel->correoExiste($correo, $idUsuario)) {
                throw new Exception('El correo electrónico ya está registrado por otro usuario');
            }
            
            // Configurar datos del usuario
            $this->usuarioModel->setIdUsuario($idUsuario);
            $this->usuarioModel->setNombreUsuario($nombre);
            $this->usuarioModel->setCorreoUsuario($correo);
            $this->usuarioModel->setIdRol($idRol);
            
            // Actualizar contraseña solo si se proporcionó una nueva
            if (!empty($_POST['contrasena'])) {
                $contrasena = $_POST['contrasena'];
                
                // Validar longitud
                if (strlen($contrasena) < 6) {
                    throw new Exception('La contraseña debe tener al menos 6 caracteres');
                }
                
                // Actualizar contraseña
                $this->usuarioModel->actualizarContrasena($contrasena);
            }
            
            // Actualizar usuario
            if ($this->usuarioModel->actualizar()) {
                $_SESSION['success_message'] = 'Usuario actualizado exitosamente';
                header('Location: index.php?controller=usuarios&action=index');
                exit();
            } else {
                throw new Exception('Error al actualizar el usuario');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            
            // Redirigir de vuelta al formulario de edición
            $idUsuario = isset($_POST['id']) ? intval($_POST['id']) : 0;
            header('Location: index.php?controller=usuarios&action=editar&id=' . $idUsuario);
            exit();
        }
    }
    
    /**
     * Eliminar usuario
     */
    public function eliminar() {
        AuthController::requerirRol(['Administrador']);
        
        try {
            // Validar ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de usuario inválido');
            }
            
            $idUsuario = intval($_GET['id']);
            
            // No permitir eliminar al usuario actual
            if ($idUsuario === $_SESSION['usuario_id']) {
                throw new Exception('No puede eliminar su propia cuenta');
            }
            
            // Eliminar usuario
            $this->usuarioModel->setIdUsuario($idUsuario);
            
            if ($this->usuarioModel->eliminar()) {
                $_SESSION['success_message'] = 'Usuario eliminado exitosamente';
            } else {
                throw new Exception('Error al eliminar el usuario');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
        
        header('Location: index.php?controller=usuarios&action=index');
        exit();
    }
    
    /**
     * Buscar usuarios
     */
    public function buscar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        try {
            $termino = isset($_GET['q']) ? trim($_GET['q']) : '';
            
            if (empty($termino)) {
                // Si no hay término, mostrar todos
                $usuarios = $this->usuarioModel->leerTodos();
            } else {
                // Buscar por nombre
                $usuarios = $this->usuarioModel->buscarPorNombre($termino);
            }
            
            // Cargar vista
            require_once 'views/usuarios/index.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error en la búsqueda: ' . $e->getMessage();
            header('Location: index.php?controller=usuarios&action=index');
            exit();
        }
    }
    
    /**
     * Ver perfil del usuario actual
     */
    public function perfil() {
        AuthController::requerirAutenticacion();
        
        try {
            // Obtener datos del usuario actual
            $this->usuarioModel->setIdUsuario($_SESSION['usuario_id']);
            $usuario = $this->usuarioModel->leerPorId();
            
            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }
            
            // Cargar vista de perfil
            require_once 'views/usuarios/perfil.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
    }
    
    /**
     * Actualizar perfil del usuario actual
     */
    public function actualizarPerfil() {
        AuthController::requerirAutenticacion();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=usuarios&action=perfil');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos
            if (empty($_POST['nombre']) || empty($_POST['correo'])) {
                throw new Exception('Por favor complete todos los campos obligatorios');
            }
            
            $nombre = trim($_POST['nombre']);
            $correo = trim($_POST['correo']);
            
            // Validar formato de correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Formato de correo electrónico inválido');
            }
            
            // Verificar si el correo ya existe (excluyendo el usuario actual)
            if ($this->usuarioModel->correoExiste($correo, $_SESSION['usuario_id'])) {
                throw new Exception('El correo electrónico ya está registrado');
            }
            
            // Actualizar datos
            $this->usuarioModel->setIdUsuario($_SESSION['usuario_id']);
            $this->usuarioModel->setNombreUsuario($nombre);
            $this->usuarioModel->setCorreoUsuario($correo);
            
            // Mantener el rol actual
            $this->usuarioModel->setIdRol($_SESSION['usuario_rol_id']);
            
            if ($this->usuarioModel->actualizar()) {
                // Actualizar variables de sesión
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_correo'] = $correo;
                
                $_SESSION['success_message'] = 'Perfil actualizado exitosamente';
            } else {
                throw new Exception('Error al actualizar el perfil');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
        
        header('Location: index.php?controller=usuarios&action=perfil');
        exit();
    }
    
    /**
     * Obtener lista de roles (método auxiliar)
     * @return array Lista de roles
     */
    private function obtenerRoles() {
        try {
            $query = "SELECT idRol, NombreRol, DescripcionRol FROM Rol ORDER BY NombreRol";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener roles: " . $e->getMessage());
            return [];
        }
    }
}
?>
<?php
/**
 * Controlador de Clientes
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona el CRUD de clientes del hotel
 */

require_once 'models/Clientes.php';
require_once 'controllers/AuthController.php';

class ClientesController {
    
    private $db;
    private $clienteModel;
    
    /**
     * Constructor
     */
    public function __construct($conexion) {
        $this->db = $conexion;
        $this->clienteModel = new Clientes($this->db);
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Listar todos los clientes
     * Accesible para todos los roles autenticados
     */
    public function index() {
        // Requerir autenticación
        AuthController::requerirAutenticacion();
        
        try {
            // Preparar filtros desde GET
            $filtros = [
                'nombre' => isset($_GET['nombre']) ? trim($_GET['nombre']) : '',
                'dui' => isset($_GET['dui']) ? trim($_GET['dui']) : '',
                'correo' => isset($_GET['correo']) ? trim($_GET['correo']) : ''
            ];
            
            // Obtener clientes con filtros
            $stmt = $this->clienteModel->obtenerTodos($filtros);
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $page_title = "Gestión de Clientes";
            $active_page = "clientes";
            $child_view = 'views/clientes/index.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar clientes: ' . $e->getMessage();
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
    }
    
    /**
     * Mostrar formulario de creación de cliente
     */
    public function crear() {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        
        try {
            $page_title = "Crear Nuevo Cliente";
            $active_page = "clientes";
            $child_view = 'views/clientes/crear.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar formulario: ' . $e->getMessage();
            header('Location: index.php?controller=clientes&action=index');
            exit();
        }
    }
    
    /**
     * Guardar nuevo cliente
     */
    public function guardar() {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=clientes&action=crear');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos requeridos
            if (empty($_POST['dui']) || empty($_POST['nombre']) || empty($_POST['correo'])) {
                throw new Exception('Por favor complete todos los campos obligatorios');
            }
            
            $dui = trim($_POST['dui']);
            $nombre = trim($_POST['nombre']);
            $correo = trim($_POST['correo']);
            $telefono = trim($_POST['telefono']);
            
            // Validar formato de DUI
            if (!$this->clienteModel->validarFormatoDui($dui)) {
                throw new Exception('Formato de DUI inválido. Debe ser: 12345678-9');
            }
            
            // Validar longitud del nombre
            if (strlen($nombre) < 3) {
                throw new Exception('El nombre debe tener al menos 3 caracteres');
            }
            
            // Validar formato de correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Formato de correo electrónico inválido');
            }
            
            // Validar teléfono si se proporcionó
            if (!empty($telefono)) {
                if (!preg_match('/^[0-9]{8,15}$/', $telefono)) {
                    throw new Exception('Formato de teléfono inválido. Solo números (8-15 dígitos)');
                }
            }
            
            // Verificar si el DUI ya existe
            if ($this->clienteModel->duiExiste($dui)) {
                throw new Exception('El DUI ya está registrado en el sistema');
            }
            
            // Verificar si el correo ya existe
            if ($this->clienteModel->correoExiste($correo)) {
                throw new Exception('El correo electrónico ya está registrado');
            }
            
            // Configurar datos del cliente
            $this->clienteModel->DuiCliente = $dui;
            $this->clienteModel->NombreCliente = $nombre;
            $this->clienteModel->CorreoCliente = $correo;
            $this->clienteModel->TelefonoCliente = $telefono;
            
            // Guardar cliente
            if ($this->clienteModel->crear()) {
                $_SESSION['success_message'] = 'Cliente creado exitosamente';
                header('Location: index.php?controller=clientes&action=index');
                exit();
            } else {
                throw new Exception('Error al guardar el cliente');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=clientes&action=crear');
            exit();
        }
    }
    
    /**
     * Mostrar formulario de edición de cliente
     */
    public function editar() {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        
        try {
            // Validar ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de cliente inválido');
            }
            
            $idCliente = intval($_GET['id']);
            
            // Obtener datos del cliente
            $this->clienteModel->idCliente = $idCliente;
            
            if (!$this->clienteModel->obtener()) {
                throw new Exception('Cliente no encontrado');
            }
            
            // Los datos ya están cargados en el modelo
            $cliente = [
                'idCliente' => $this->clienteModel->idCliente,
                'DuiCliente' => $this->clienteModel->DuiCliente,
                'NombreCliente' => $this->clienteModel->NombreCliente,
                'CorreoCliente' => $this->clienteModel->CorreoCliente,
                'TelefonoCliente' => $this->clienteModel->TelefonoCliente
            ];
            
            $page_title = "Editar Cliente";
            $active_page = "clientes";
            $child_view = 'views/clientes/editar.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=clientes&action=index');
            exit();
        }
    }
    
    /**
     * Actualizar cliente existente
     */
    public function actualizar() {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=clientes&action=index');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos requeridos
            if (empty($_POST['idCliente']) || empty($_POST['dui']) || 
                empty($_POST['nombre']) || empty($_POST['correo'])) {
                throw new Exception('Por favor complete todos los campos obligatorios');
            }
            
            $idCliente = intval($_POST['idCliente']);
            $dui = trim($_POST['dui']);
            $nombre = trim($_POST['nombre']);
            $correo = trim($_POST['correo']);
            $telefono = trim($_POST['telefono']);
            
            // Validar formato de DUI
            if (!$this->clienteModel->validarFormatoDui($dui)) {
                throw new Exception('Formato de DUI inválido. Debe ser: 12345678-9');
            }
            
            // Validar longitud del nombre
            if (strlen($nombre) < 3) {
                throw new Exception('El nombre debe tener al menos 3 caracteres');
            }
            
            // Validar formato de correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Formato de correo electrónico inválido');
            }
            
            // Validar teléfono si se proporcionó
            if (!empty($telefono)) {
                if (!preg_match('/^[0-9]{8,15}$/', $telefono)) {
                    throw new Exception('Formato de teléfono inválido. Solo números (8-15 dígitos)');
                }
            }
            
            // Verificar si el DUI ya existe (excluyendo el cliente actual)
            if ($this->clienteModel->duiExiste($dui, $idCliente)) {
                throw new Exception('El DUI ya está registrado por otro cliente');
            }
            
            // Verificar si el correo ya existe (excluyendo el cliente actual)
            if ($this->clienteModel->correoExiste($correo, $idCliente)) {
                throw new Exception('El correo electrónico ya está registrado por otro cliente');
            }
            
            // Configurar datos del cliente
            $this->clienteModel->idCliente = $idCliente;
            $this->clienteModel->DuiCliente = $dui;
            $this->clienteModel->NombreCliente = $nombre;
            $this->clienteModel->CorreoCliente = $correo;
            $this->clienteModel->TelefonoCliente = $telefono;
            
            // Actualizar cliente
            if ($this->clienteModel->editar()) {
                $_SESSION['success_message'] = 'Cliente actualizado exitosamente';
                header('Location: index.php?controller=clientes&action=index');
                exit();
            } else {
                throw new Exception('Error al actualizar el cliente');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            
            // Redirigir de vuelta al formulario de edición
            $idCliente = isset($_POST['idCliente']) ? intval($_POST['idCliente']) : 0;
            header('Location: index.php?controller=clientes&action=editar&id=' . $idCliente);
            exit();
        }
    }
    
    /**
     * Eliminar cliente
     * IMPORTANTE: No se puede eliminar si tiene reservas asociadas
     */
    public function eliminar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        try {
            // Validar ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de cliente inválido');
            }
            
            $idCliente = intval($_GET['id']);
            
            // Verificar si el cliente existe
            $this->clienteModel->idCliente = $idCliente;
            
            if (!$this->clienteModel->obtener()) {
                throw new Exception('Cliente no encontrado');
            }
            
            // Intentar eliminar cliente
            if ($this->clienteModel->eliminar()) {
                $_SESSION['success_message'] = 'Cliente eliminado exitosamente';
            } else {
                throw new Exception('Error al eliminar el cliente');
            }
            
        } catch (PDOException $e) {
            // Capturar error de constraint (cliente tiene reservas)
            if ($e->getCode() == '23000') {
                $_SESSION['error_message'] = 'No se puede eliminar el cliente porque tiene reservas asociadas. Debe eliminar primero todas sus reservas.';
            } else {
                $_SESSION['error_message'] = 'Error al eliminar el cliente: ' . $e->getMessage();
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
        
        header('Location: index.php?controller=clientes&action=index');
        exit();
    }
    
    /**
     * Buscar clientes
     */
    public function buscar() {
        AuthController::requerirAutenticacion();
        
        try {
            $tipoBusqueda = isset($_GET['tipo']) ? trim($_GET['tipo']) : 'nombre';
            $termino = isset($_GET['q']) ? trim($_GET['q']) : '';
            
            if (empty($termino)) {
                // Si no hay término, mostrar todos
                $stmt = $this->clienteModel->obtenerTodos();
            } else {
                // Buscar según el tipo
                switch($tipoBusqueda) {
                    case 'dui':
                        $stmt = $this->clienteModel->buscarPorDui($termino);
                        break;
                    case 'correo':
                        $stmt = $this->clienteModel->buscarPorCorreo($termino);
                        break;
                    default:
                        $stmt = $this->clienteModel->buscarPorNombre($termino);
                }
            }
            
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $page_title = "Búsqueda de Clientes";
            $active_page = "clientes";
            $child_view = 'views/clientes/index.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error en la búsqueda: ' . $e->getMessage();
            header('Location: index.php?controller=clientes&action=index');
            exit();
        }
    }
    
    /**
     * Ver detalles y reservas de un cliente
     */
    public function ver() {
        AuthController::requerirAutenticacion();
        
        try {
            // Validar ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de cliente inválido');
            }
            
            $idCliente = intval($_GET['id']);
            
            // Obtener datos del cliente
            $this->clienteModel->idCliente = $idCliente;
            
            if (!$this->clienteModel->obtenerPorId()) {
                throw new Exception('Cliente no encontrado');
            }
            
            // Los datos ya están cargados en el modelo
            $cliente = [
                'idCliente' => $this->clienteModel->idCliente,
                'DuiCliente' => $this->clienteModel->DuiCliente,
                'NombreCliente' => $this->clienteModel->NombreCliente,
                'CorreoCliente' => $this->clienteModel->CorreoCliente,
                'TelefonoCliente' => $this->clienteModel->TelefonoCliente
            ];
            
            // Obtener reservas del cliente
            $stmtReservas = $this->clienteModel->obtenerReservas($idCliente);
            $reservas = $stmtReservas ? $stmtReservas->fetchAll(PDO::FETCH_ASSOC) : [];
            
            $page_title = "Detalles del Cliente";
            $active_page = "clientes";
            $child_view = 'views/clientes/ver.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=clientes&action=index');
            exit();
        }
    }
    
    /**
     * Obtener cliente por DUI (para uso en AJAX o APIs internas)
     */
    public function obtenerPorDui() {
        AuthController::requerirAutenticacion();
        
        try {
            if (!isset($_GET['dui'])) {
                throw new Exception('DUI no proporcionado');
            }
            
            $dui = trim($_GET['dui']);
            
            // Validar formato
            if (!$this->clienteModel->validarFormatoDui($dui)) {
                throw new Exception('Formato de DUI inválido');
            }
            
            // Buscar cliente
            $stmt = $this->clienteModel->buscarPorDui($dui);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Retornar JSON
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $resultado ? true : false,
                'cliente' => $resultado ? $resultado : null
            ]);
            exit();
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }
}
?>
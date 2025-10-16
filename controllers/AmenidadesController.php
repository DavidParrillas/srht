<?php

require_once 'models/Amenidades.php';
require_once 'controllers/AuthController.php';

class AmenidadesController {
    
    private $db;
    private $amenidadModel;
    
    /**
     * Constructor
     */
    public function __construct($conexion) {
        $this->db = $conexion;
        $this->amenidadModel = new Amenidades($this->db);
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Listar todas las amenidades
     * Accesible para todos los roles autenticados
     */
    public function index() {
        // Requerir autenticación
        AuthController::requerirAutenticacion();
        
        try {
            // Obtener todas las amenidades
            $stmt = $this->amenidadModel->obtenerTodas();
            $amenidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $page_title = "Gestión de Amenidades";
            $active_page = "amenidades";
            $child_view = 'views/amenidades/index.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar amenidades: ' . $e->getMessage();
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
    }
    
    /**
     * Mostrar formulario de creación de amenidad
     */
    public function crear() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        try {
            $page_title = "Crear Nueva Amenidad";
            $active_page = "amenidades";
            $child_view = 'views/amenidades/crear.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar formulario: ' . $e->getMessage();
            header('Location: index.php?controller=amenidades&action=index');
            exit();
        }
    }
    
    /**
     * Guardar nueva amenidad
     */
    public function guardar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=amenidades&action=crear');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos requeridos
            if (empty($_POST['nombre'])) {
                throw new Exception('Por favor ingrese el nombre de la amenidad');
            }
            
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);
            
            // Validar longitud del nombre
            if (strlen($nombre) < 3) {
                throw new Exception('El nombre de la amenidad debe tener al menos 3 caracteres');
            }
            
            // Verificar si la amenidad ya existe
            if ($this->amenidadModel->existePorNombre($nombre)) {
                throw new Exception('Ya existe una amenidad con ese nombre');
            }
            
            // Configurar datos de la amenidad
            $this->amenidadModel->nombreAmenidad = $nombre;
            $this->amenidadModel->Descripcion = $descripcion;
            
            // Guardar amenidad
            if ($this->amenidadModel->crear()) {
                $_SESSION['success_message'] = 'Amenidad creada exitosamente';
                header('Location: index.php?controller=amenidades&action=index');
                exit();
            } else {
                throw new Exception('Error al guardar la amenidad');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=amenidades&action=crear');
            exit();
        }
    }
    
    /**
     * Mostrar formulario de edición de amenidad
     */
    public function editar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        try {
            // Validar ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de amenidad inválido');
            }
            
            $idAmenidad = intval($_GET['id']);
            
            // Obtener datos de la amenidad
            $this->amenidadModel->idAmenidad = $idAmenidad;
            
            if (!$this->amenidadModel->obtenerPorId()) {
                throw new Exception('Amenidad no encontrada');
            }
            
            // Los datos ya están cargados en el modelo
            $amenidad = [
                'idAmenidad' => $this->amenidadModel->idAmenidad,
                'nombreAmenidad' => $this->amenidadModel->nombreAmenidad,
                'Descripcion' => $this->amenidadModel->Descripcion
            ];
            
            $page_title = "Editar Amenidad";
            $active_page = "amenidades";
            $child_view = 'views/amenidades/editar.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=amenidades&action=index');
            exit();
        }
    }
    
    /**
     * Actualizar amenidad existente
     */
    public function actualizar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=amenidades&action=index');
            exit();
        }
        
        try {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }
            
            // Validar campos requeridos
            if (empty($_POST['idAmenidad']) || empty($_POST['nombre'])) {
                throw new Exception('Por favor complete todos los campos obligatorios');
            }
            
            $idAmenidad = intval($_POST['idAmenidad']);
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);
            
            // Validar longitud del nombre
            if (strlen($nombre) < 3) {
                throw new Exception('El nombre de la amenidad debe tener al menos 3 caracteres');
            }
            
            // Verificar si existe otra amenidad con el mismo nombre
            // (excluyendo la amenidad actual)
            $this->amenidadModel->idAmenidad = $idAmenidad;
            
            if ($this->amenidadModel->existePorNombre($nombre)) {
                // Verificar que no sea la misma amenidad
                if (!$this->amenidadModel->obtenerPorId() || 
                    $this->amenidadModel->nombreAmenidad !== $nombre) {
                    throw new Exception('Ya existe otra amenidad con ese nombre');
                }
            }
            
            // Configurar datos de la amenidad
            $this->amenidadModel->idAmenidad = $idAmenidad;
            $this->amenidadModel->nombreAmenidad = $nombre;
            $this->amenidadModel->Descripcion = $descripcion;
            
            // Actualizar amenidad
            if ($this->amenidadModel->editar()) {
                $_SESSION['success_message'] = 'Amenidad actualizada exitosamente';
                header('Location: index.php?controller=amenidades&action=index');
                exit();
            } else {
                throw new Exception('Error al actualizar la amenidad');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            
            // Redirigir de vuelta al formulario de edición
            $idAmenidad = isset($_POST['idAmenidad']) ? intval($_POST['idAmenidad']) : 0;
            header('Location: index.php?controller=amenidades&action=editar&id=' . $idAmenidad);
            exit();
        }
    }
    
    /**
     * Eliminar amenidad
     */
    public function eliminar() {
        AuthController::requerirRol(['Administrador', 'Gerencia']);
        
        try {
            // Validar ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de amenidad inválido');
            }
            
            $idAmenidad = intval($_GET['id']);
            
            // Verificar si la amenidad existe
            $this->amenidadModel->idAmenidad = $idAmenidad;
            
            if (!$this->amenidadModel->obtenerPorId()) {
                throw new Exception('Amenidad no encontrada');
            }
            
            // Eliminar amenidad
            if ($this->amenidadModel->eliminar()) {
                $_SESSION['success_message'] = 'Amenidad eliminada exitosamente';
            } else {
                throw new Exception('Error al eliminar la amenidad');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
        
        header('Location: index.php?controller=amenidades&action=index');
        exit();
    }
    
    /**
     * Ver detalles de una amenidad
     */
    public function ver() {
        AuthController::requerirAutenticacion();
        
        try {
            // Validar ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('ID de amenidad inválido');
            }
            
            $idAmenidad = intval($_GET['id']);
            
            // Obtener datos de la amenidad
            $this->amenidadModel->idAmenidad = $idAmenidad;
            
            if (!$this->amenidadModel->obtenerPorId()) {
                throw new Exception('Amenidad no encontrada');
            }
            
            // Los datos ya están cargados en el modelo
            $amenidad = [
                'idAmenidad' => $this->amenidadModel->idAmenidad,
                'nombreAmenidad' => $this->amenidadModel->nombreAmenidad,
                'Descripcion' => $this->amenidadModel->Descripcion
            ];
            
            $page_title = "Detalles de Amenidad";
            $active_page = "amenidades";
            $child_view = 'views/amenidades/ver.php';
            
            // Cargar vista
            require_once 'views/layouts/main.php';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=amenidades&action=index');
            exit();
        }
    }
}
?>
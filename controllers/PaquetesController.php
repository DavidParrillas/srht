<?php
require_once 'models/Paquetes.php';
require_once 'controllers/AuthController.php';

class PaquetesController {

    private $db;
    private $paqueteModel;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->paqueteModel = new Paquetes($this->db);
    }

    /** Listado */
    public function index() {
        AuthController::requerirAutenticacion();
        try {
            $stmt = $this->paqueteModel->obtenerTodas();
            $paquetes = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

            $page_title = "Gestión de Paquetes y Tarifas";
            $active_page = "paquetes";
            $child_view = 'views/paquetes/index.php';
            require_once 'views/layouts/main.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar paquetes: '.$e->getMessage();
            header('Location: index.php?controller=dashboard&action=index');
            exit();
        }
    }

    /** Form crear */
    public function crear() {
        AuthController::requerirRol(['Administrador','Gerencia']);
        try {
            $page_title = "Crear Paquete";
            $active_page = "paquetes";
            $child_view = 'views/paquetes/crear.php';
            require_once 'views/layouts/main.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar formulario: '.$e->getMessage();
            header('Location: index.php?controller=paquetes&action=index');
            exit();
        }
    }

    /** Guardar */
    public function guardar() {
        AuthController::requerirRol(['Administrador','Gerencia']);
        try {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }

            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tarifa = trim($_POST['tarifa'] ?? '');

            if ($nombre === '' || strlen($nombre) < 3) {
                throw new Exception('El nombre debe tener al menos 3 caracteres');
            }
            if ($tarifa === '' || !is_numeric($tarifa) || (float)$tarifa <= 0) {
                throw new Exception('La tarifa debe ser un número mayor a 0');
            }
            if ($this->paqueteModel->existePorNombre($nombre)) {
                throw new Exception('Ya existe un paquete con ese nombre');
            }

            $this->paqueteModel->NombrePaquete = $nombre;
            $this->paqueteModel->DescripcionPaquete = $descripcion;
            $this->paqueteModel->TarifaPaquete = (float)$tarifa;

            if ($this->paqueteModel->crear()) {
                $_SESSION['success_message'] = 'Paquete creado exitosamente';
                header('Location: index.php?controller=paquetes&action=index');
                exit();
            }
            throw new Exception('No se pudo crear el paquete');
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=paquetes&action=crear');
            exit();
        }
    }

    /** Form editar */
    public function editar() {
        AuthController::requerirRol(['Administrador','Gerencia']);
        try {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($id <= 0) throw new Exception('ID inválido');

            $this->paqueteModel->idPaquete = $id;
            if (!$this->paqueteModel->obtenerPorId()) {
                throw new Exception('Paquete no encontrado');
            }

            $paquete = [
                'idPaquete' => $id,
                'NombrePaquete' => $this->paqueteModel->NombrePaquete,
                'DescripcionPaquete' => $this->paqueteModel->DescripcionPaquete,
                'TarifaPaquete' => $this->paqueteModel->TarifaPaquete,
            ];

            $page_title = "Editar Paquete";
            $active_page = "paquetes";
            $child_view = 'views/paquetes/editar.php';
            require_once 'views/layouts/main.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=paquetes&action=index');
            exit();
        }
    }

    /** Actualizar */
    public function actualizar() {
        AuthController::requerirRol(['Administrador','Gerencia']);
        try {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Token de seguridad inválido');
            }

            $id = isset($_POST['idPaquete']) ? intval($_POST['idPaquete']) : 0;
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tarifa = trim($_POST['tarifa'] ?? '');

            if ($id <= 0) throw new Exception('ID inválido');
            if ($nombre === '' || strlen($nombre) < 3) {
                throw new Exception('El nombre debe tener al menos 3 caracteres');
            }
            if ($tarifa === '' || !is_numeric($tarifa) || (float)$tarifa <= 0) {
                throw new Exception('La tarifa debe ser un número mayor a 0');
            }

            // Si cambian el nombre, verificar duplicado
            $this->paqueteModel->idPaquete = $id;
            if (!$this->paqueteModel->obtenerPorId()) throw new Exception('Paquete no encontrado');
            if (strcasecmp($this->paqueteModel->NombrePaquete, $nombre) !== 0 &&
                $this->paqueteModel->existePorNombre($nombre)) {
                throw new Exception('Ya existe un paquete con ese nombre');
            }

            $this->paqueteModel->idPaquete = $id;
            $this->paqueteModel->NombrePaquete = $nombre;
            $this->paqueteModel->DescripcionPaquete = $descripcion;
            $this->paqueteModel->TarifaPaquete = (float)$tarifa;

            if ($this->paqueteModel->editar()) {
                $_SESSION['success_message'] = 'Paquete actualizado exitosamente';
                header('Location: index.php?controller=paquetes&action=index');
                exit();
            }
            throw new Exception('No se pudo actualizar el paquete');
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $id = isset($_POST['idPaquete']) ? intval($_POST['idPaquete']) : 0;
            header('Location: index.php?controller=paquetes&action=editar&id='.$id);
            exit();
        }
    }

    /** Eliminar */
    public function eliminar() {
        AuthController::requerirRol(['Administrador','Gerencia']);
        try {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($id <= 0) throw new Exception('ID inválido');

            $this->paqueteModel->idPaquete = $id;
            if ($this->paqueteModel->eliminar()) {
                $_SESSION['success_message'] = 'Paquete eliminado';
            } else {
                $_SESSION['error_message'] = 'No se pudo eliminar el paquete';
            }
            header('Location: index.php?controller=paquetes&action=index');
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: index.php?controller=paquetes&action=index');
            exit();
        }
    }
}
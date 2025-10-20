<?php
/**
 * Controlador de Habitaciones
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 * 
 * Gestiona el CRUD de las habitaciones del hotel
 */

require_once 'controllers/AuthController.php';
require_once 'services/HabitacionService.php';


class HabitacionesController
{

    private $db;
    private $habitacionService;

    public function __construct($conexion)
    {
        $this->db = $conexion;
        $this->habitacionService = new HabitacionService();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        $page_title = "Gestión de Habitaciones";
        $active_page = "habitaciones";

        $habitaciones = $this->habitacionService->listarHabitaciones();
        $tiposHabitacion = $this->habitacionService->listarTiposHabitacion();
        $estadosHabitacion = $this->habitacionService->listarEstadosHabitacion();

        $page_title = "Gestión de Habitaciones";
        $active_page = "habitaciones";
        $child_view = 'views/habitaciones/index.php';
        require_once 'views/layouts/main.php';
    }

    public function crear()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recibes los datos del formulario
            $numero = $_POST['numero'];
            $estado = $_POST['estadoHabitacion'];
            $detalle = $_POST['detalleHabitacion'];
            $idTipo = $_POST['tipoHabitacion'];
            $amenidadesSeleccionadas = $_POST['amenidades'] ?? [];

            $sqlVerificar = "SELECT COUNT(*) AS total FROM Habitacion WHERE numeroHabitacion = :numero";
            $stmtVerificar = $this->db->prepare($sqlVerificar);
            $stmtVerificar->execute([':numero' => $numero]);
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if ($resultado['total'] > 0) {
                $_SESSION['error'] = "La habitación número {$numero} ya se encuentra registrada. Por favor elija otro número.";
                header("Location: index.php?controller=habitaciones&action=crear");
                exit;
            }

            $tipo = new TipoHabitacion($idTipo, '', '', 0);

            $habitacion = new Habitacion(
                null,
                $tipo,
                $numero,
                $estado,
                $detalle
            );

            $idHabitacion = $this->habitacionService->crearHabitacion($habitacion);

            if (!empty($amenidadesSeleccionadas)) {
                $sql = "INSERT INTO HabitacionAmenidad (idHabitacion, idAmenidad)
                    VALUES (:idHabitacion, :idAmenidad)";
                $stmt = $this->db->prepare($sql);

                foreach ($amenidadesSeleccionadas as $idAmenidad) {
                    $stmt->execute([
                        ':idHabitacion' => $idHabitacion,
                        ':idAmenidad' => $idAmenidad
                    ]);
                }
            }

            $_SESSION['success'] = "La habitación #$numero fue registrada correctamente.";

            header("Location: index.php?controller=habitaciones&action=index");
            exit;
        } else {
            $tiposHabitacion = $this->habitacionService->listarTiposHabitacion();
            require_once 'models/Amenidades.php';
            $amenidadModel = new Amenidades($this->db);
            $stmt = $amenidadModel->obtenerTodas();
            $amenidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $page_title = "Registrar nueva habitación";
            $active_page = "habitaciones";
            $child_view = 'views/habitaciones/crear.php';
            require_once 'views/layouts/main.php';
        }
    }

    public function editar()
    {
        error_log("🟢 Entrando en HabitacionesController::editar()");

        AuthController::requerirRol(['Administrador', 'Gerencia']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=habitaciones");
            exit();
        }

        // Buscar la habitación por ID
        error_log("🟢 Entrando en HabitacionesController()");

        $habitacion = $this->habitacionService->obtenerHabitacionPorId($id);
        if (!$habitacion) {
            $_SESSION['error'] = "La habitación seleccionada no existe.";
            header("Location: index.php?controller=habitaciones");
            exit();
        }

        // Obtener tipos y amenidades
        $tiposHabitacion = $this->habitacionService->listarTiposHabitacion();
        $amenidades = $this->habitacionService->listarAmenidades();
        $estadosHabitacion = $this->habitacionService->listarEstadosHabitacion();

        $page_title = "Editar Habitación";
        $active_page = "habitaciones";
        $child_view = 'views/habitaciones/editar.php';
        require_once 'views/layouts/main.php';
    }

    public function actualizar()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia']);

        $id = $_POST['id'] ?? null;
        $estado = $_POST['estadoHabitacion'] ?? '';
        $tipo = $_POST['tipoHabitacion'] ?? '';
        $detalle = $_POST['detalleHabitacion'] ?? '';
        $amenidades = $_POST['amenidades'] ?? [];

        if (!$id) {
            $_SESSION['error'] = "Falta el ID de la habitación.";
            header("Location: index.php?controller=habitaciones");
            exit();
        }

        $actualizado = $this->habitacionService->actualizarHabitacion($id, $estado, $tipo, $detalle, $amenidades);

        if ($actualizado) {
            $_SESSION['success'] = "La habitación se actualizó correctamente.";
        } else {
            $_SESSION['error'] = "Ocurrió un error al actualizar la habitación.";
        }

        header("Location: index.php?controller=habitaciones");
        exit();
    }

    public function eliminar()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia']);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = "No se especificó la habitación a eliminar.";
            header("Location: index.php?controller=habitaciones");
            exit();
        }

        error_log("Solicitando eliminación de habitación ID=$id");

        $eliminado = $this->habitacionService->eliminarHabitacion($id);

        if ($eliminado) {
            $_SESSION['success'] = "La habitación se eliminó correctamente.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar la habitación (puede tener reservas asociadas o error interno).";
        }

        header("Location: index.php?controller=habitaciones");
        exit();
    }


}
?>
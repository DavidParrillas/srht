<?php
require_once 'models/Reserva.php';
require_once 'models/Paquetes.php';
require_once 'controllers/AuthController.php';

class ReservacionesController
{

    private $db;
    private $reservaModel;
    private $paqueteModel;

    public function __construct($conexion)
    {
        $this->db = $conexion;
        $this->reservaModel = new Reserva($this->db);
        $this->paqueteModel = new Paquetes($this->db);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Paso 1: Seleccionar fechas
     */

    public function crear()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fechaEntrada = $_POST['fechaEntrada'] ?? null;
            $fechaSalida = $_POST['fechaSalida'] ?? null;

            if (!$fechaEntrada || !$fechaSalida) {
                $_SESSION['error_message'] = "Debe ingresar ambas fechas";
                header("Location: index.php?controller=reservaciones&action=crear");
                exit();
            }

            if (strtotime($fechaSalida) <= strtotime($fechaEntrada)) {
                $_SESSION['error_message'] = "La fecha de salida debe ser mayor a la de entrada";
                header("Location: index.php?controller=reservaciones&action=crear");
                exit();
            }

            $_SESSION['reserva_temp']['fechaEntrada'] = $fechaEntrada;
            $_SESSION['reserva_temp']['fechaSalida'] = $fechaSalida;

            // Avanzar al paso 2
            header("Location: index.php?controller=reservaciones&action=verHabitacionesDisponibles");
            exit();
        }

        $page_title = "Seleccionar Fechas";
        $active_page = "reservas";
        $child_view = 'views/reservas/1-fechas.php';
        require_once 'views/layouts/main.php';
    }

    /**
     * [NUEVO] Inicia el flujo de modificación de una reserva (edit).
     * Siempre redirige al paso 1 (seleccionar fechas).
     */
    public function edit()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
            $_SESSION['error_message'] = "ID de reserva a modificar inválido.";
            header("Location: index.php?controller=reservaciones&action=index");
            exit();
        }

        $idOriginal = intval($_GET['id']);

        // 1. Guardar la ID de la reserva original en la sesión
        $_SESSION['reserva_original_id'] = $idOriginal;

        // 2. Obtener datos de la reserva original para precargar fechas
        $reservaOriginal = $this->reservaModel->obtenerReservaPorId($idOriginal); // Necesitas este nuevo método en el modelo (Ver nota B)

        if (!$reservaOriginal) {
            unset($_SESSION['reserva_original_id']);
            $_SESSION['error_message'] = "Reserva original no encontrada.";
            header("Location: index.php?controller=reservaciones&action=index");
            exit();
        }

        // 3. Precargar datos de la reserva original en reserva_temp
        $_SESSION['reserva_temp']['fechaEntrada'] = $reservaOriginal['FechaEntrada'];
        $_SESSION['reserva_temp']['fechaSalida'] = $reservaOriginal['FechaSalida'];
        $_SESSION['reserva_temp']['idHabitacion'] = $reservaOriginal['idHabitacion'];
        $_SESSION['reserva_temp']['idCliente'] = $reservaOriginal['idCliente'];
        $_SESSION['reserva_temp']['idPaquete'] = $reservaOriginal['idPaquete'];

        // Redirigir al paso 1 (crear/fechas)
        header("Location: index.php?controller=reservaciones&action=crear");
        exit();
    }

    /**
     * Paso 2: Ver habitaciones disponibles
     */
    public function verHabitacionesDisponibles()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        if (
            !isset($_SESSION['reserva_temp']['fechaEntrada']) ||
            !isset($_SESSION['reserva_temp']['fechaSalida'])
        ) {
            header("Location: index.php?controller=reservaciones&action=crear");
            exit();
        }

        $fechaEntrada = $_SESSION['reserva_temp']['fechaEntrada'];
        $fechaSalida = $_SESSION['reserva_temp']['fechaSalida'];

        // IDENTIFICAR SI LA DISPONIBILIDAD ES PARA UN CAMBIO O UNA NUEVA RESERVA
        $idOriginal = $_SESSION['reserva_original_id'] ?? null;

        $stmt = $this->reservaModel->obtenerHabitacionesDisponibles($fechaEntrada, $fechaSalida, $idOriginal);
        $habitaciones = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        $page_title = "Habitaciones Disponibles";
        $active_page = "reservas";
        $child_view = 'views/reservas/2-habitaciones.php';
        require_once 'views/layouts/main.php';
    }

    /**
     * Recibe la habitación seleccionada y decide el siguiente paso.
     * SALTA el paso 3 (Asignar Cliente) si estamos en modo edición.
     */
    public function seleccionarHabitacion()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        // 1. Validación básica de la solicitud
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['idHabitacion'])) {
            // Si no hay ID de habitación, regresa al paso anterior
            header("Location: index.php?controller=reservaciones&action=verHabitacionesDisponibles");
            exit();
        }

        // 2. Guardamos la habitación seleccionada en la sesión temporal
        $_SESSION['reserva_temp']['idHabitacion'] = intval($_POST['idHabitacion']);

        if (isset($_SESSION['reserva_original_id'])) {
            // Si es edición, saltamos el paso 3 (cliente)
            header("Location: index.php?controller=reservaciones&action=seleccionarPaquete");
            exit();
        }

        // 3. Si es creación normal, vamos al paso 3 (asignar cliente)
        header("Location: index.php?controller=reservaciones&action=asignarCliente");
        exit();
    }

    /**
     * Paso 3: Asignar cliente
     */
    public function asignarCliente()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        // Guardar cliente seleccionado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['idCliente'])) {
                $_SESSION['error_message'] = "Debe seleccionar un cliente";
                header("Location: index.php?controller=reservaciones&action=asignarCliente");
                exit();
            }

            $_SESSION['reserva_temp']['idCliente'] = intval($_POST['idCliente']);
            header("Location: index.php?controller=reservaciones&action=seleccionarPaquete");
            exit();
        }

        $page_title = "Asignar Cliente";
        $active_page = "reservas";
        $child_view = 'views/reservas/3-cliente.php';
        require_once 'views/layouts/main.php';
    }

    /**
     * Paso 4: Seleccionar paquete
     */
    public function seleccionarPaquete()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        $stmt = $this->paqueteModel->obtenerPaquetes();
        $paquetes = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idPaqueteSeleccionado = intval($_POST['idPaquete'] ?? 1);
            $_SESSION['reserva_temp']['idPaquete'] = $idPaqueteSeleccionado;
            header("Location: index.php?controller=reservaciones&action=confirmarReserva");
            exit();
        }

        $page_title = "Seleccionar Paquete";
        $active_page = "reservas";
        $child_view = 'views/reservas/4-paquete.php';
        require_once 'views/layouts/main.php';
    }

    /**
     * Confirma la reserva, manejando tanto la creación normal como la modificación.
     */
    public function confirmarReserva()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        $reserva_temp = $_SESSION['reserva_temp'] ?? null;

        // IDENTIFICAR MODO DE EDICIÓN
        $esEdicion = isset($_SESSION['reserva_original_id']);
        $idOriginal = $esEdicion ? $_SESSION['reserva_original_id'] : null;

        if (
            !$reserva_temp || !isset(
            $reserva_temp['fechaEntrada'],
            $reserva_temp['fechaSalida'],
            $reserva_temp['idHabitacion'],
            $reserva_temp['idCliente']
        )
        ) {
            header("Location: index.php?controller=reservaciones&action=crear");
            exit();
        }

        // Se pasa $idOriginal para excluir la reserva que se está modificando.
        if (!$this->validarDisponibilidadTemp($reserva_temp, $idOriginal)) {
            $_SESSION['error_message'] = "¡Error! La habitación ya no está disponible para esas fechas. Por favor, seleccione otra.";
            // Si la validación falla, se regresa al paso 2 (habitaciones)
            header("Location: index.php?controller=reservaciones&action=verHabitacionesDisponibles");
            exit();
        }

        // Carga de datos
        $cliente = $this->reservaModel->obtenerDatosCliente($reserva_temp['idCliente']);
        $habitacion = $this->reservaModel->obtenerDatosHabitacion($reserva_temp['idHabitacion']);
        $paquete = null;
        if (!empty($reserva_temp['idPaquete'])) {
            $paquete = $this->reservaModel->obtenerDatosPaquete($reserva_temp['idPaquete']);
        }

        // Cargar datos de la reserva original para la vista (si estamos editando)
        $reservaOriginalDatos = $esEdicion ? $this->reservaModel->obtenerReservaPorId($idOriginal) : null;


        $total = $this->reservaModel->calcularTotal(
            $reserva_temp['idHabitacion'],
            $reserva_temp['idPaquete'] ?? null,
            $reserva_temp['fechaEntrada'],
            $reserva_temp['fechaSalida']
        );
        $_SESSION['reserva_temp']['total'] = $total;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Instancia y llenado del objeto Reserva (nueva o modificada)
            $reserva = new Reserva($this->db);
            $reserva->idCliente = $reserva_temp['idCliente'];
            $reserva->idHabitacion = $reserva_temp['idHabitacion'];
            $reserva->idPaquete = $reserva_temp['idPaquete'] ?? null;
            $reserva->FechaEntrada = $reserva_temp['fechaEntrada'];
            $reserva->FechaSalida = $reserva_temp['fechaSalida'];
            $reserva->TotalReservacion = $reserva_temp['total'];
            $reserva->Comentario = $_POST['comentario'] ?? '';
            $reserva->EstadoReserva = 'Confirmada'; // Estado inicial

            //LÓGICA DE MODIFICACIÓN
            if ($esEdicion) {

                // Añadir el comentario de referencia al ID original
                $reserva->Comentario = "Reserva original modificada, ID: {$idOriginal}. " . $reserva->Comentario;

                // Intentar crear la nueva reserva
                if ($reserva->crear()) {
                    // Si se crea con éxito, cancelar la antigua
                    // Se asume que $this->reservaModel->cambiarEstado existe
                    if ($this->reservaModel->cambiarEstado($idOriginal, 'Cancelada')) {
                        unset($_SESSION['reserva_temp']);
                        unset($_SESSION['reserva_original_id']); // Limpiar ID original
                        $_SESSION['success_message'] = "Reserva modificada exitosamente. La reserva original #{$idOriginal} ha sido cancelada.";
                        header("Location: index.php?controller=reservaciones&action=index");
                        exit();
                    } else {
                        // Fallo, Nueva reserva creada, pero antigua no cancelada.
                        $_SESSION['error_message'] = "Reserva creada, pero falló la cancelación de la original #{$idOriginal}. Revisar manualmente.";
                    }
                } else {
                    $_SESSION['error_message'] = "Error al crear la nueva reserva (modificación).";
                }
            }
            //LÓGICA DE CREACIÓN NORMAL
            else {
                if ($reserva->crear()) {
                    unset($_SESSION['reserva_temp']);
                    $_SESSION['success_message'] = "Reserva creada exitosamente (#" . $reserva->idReserva . ")";
                    header("Location: index.php?controller=reservaciones&action=index");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Error al crear la reserva.";
                }
            }

            // Si hay un error, redirigir a la misma acción de confirmación para reintentar o ver el error
            header("Location: index.php?controller=reservaciones&action=confirmarReserva");
            exit();
        }

        // Muestra la vista
        $page_title = $esEdicion ? "Modificar Reserva #{$idOriginal}" : "Confirmar Reserva";
        $active_page = "reservas";
        $child_view = 'views/reservas/5-confirmar.php';
        require_once 'views/layouts/main.php';
    }

    private function validarDisponibilidadTemp($reserva_temp, $idReservaAExcluir = null)
    {
        // Asume que $this->reservaModel->obtenerHabitacionesDisponibles ya fue modificado
        $fechaEntrada = $reserva_temp['fechaEntrada'];
        $fechaSalida = $reserva_temp['fechaSalida'];
        $idHabitacion = $reserva_temp['idHabitacion'];

        // Se pasa la ID de exclusión al modelo
        $stmt = $this->reservaModel->obtenerHabitacionesDisponibles($fechaEntrada, $fechaSalida, $idReservaAExcluir);

        if (!$stmt) {
            // Manejo de error si el modelo falla
            return false;
        }

        // Verifica si la habitación seleccionada está en la lista de disponibles
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (intval($row['idHabitacion']) === intval($idHabitacion)) {
                return true; // Sigue disponible
            }
        }
        return false; // Ya no está disponible
    }

    /**
     * Listado de reservas (opcional)
     */
    public function index()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        $stmt = $this->reservaModel->obtenerTodas();
        $reservas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        $page_title = "Reservas";
        $active_page = "reservas";
        $child_view = 'views/reservas/index.php';
        require_once 'views/layouts/main.php';
    }

    /**
     * Manejador AJAX para buscar clientes desde el asistente de reservas.
     * Utiliza el método buscarCliente() del modelo de Reserva.
     */
    public function buscarClienteAjax()
    {
        // Solo roles autorizados pueden buscar
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        $termino = $_GET['q'] ?? '';

        if (strlen($termino) < 2) {
            echo '<div class="list-group-item">Escriba al menos 2 letras...</div>';
            exit();
        }

        // Usamos el método que SÍ está en tu ReservaModel
        $stmt = $this->reservaModel->buscarCliente($termino);

        if ($stmt && $stmt->rowCount() > 0) {

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                // CORRECCIÓN: Usamos los nombres de columna exactos
                $nombre = $row['NombreCliente'];
                $dui = $row['DuiCliente'];
                $correo = $row['CorreoCliente'];

                echo '<div class="list-group-item list-group-item-action cliente-resultado" 
                           data-id="' . $row['idCliente'] . '" 
                           data-nombre="' . htmlspecialchars($nombre) . '" 
                           style="cursor: pointer;">
                        <strong>' . htmlspecialchars($nombre) . '</strong><br>
                        <small>DUI: ' . htmlspecialchars($dui) . '</small> | 
                        <small>Correo: ' . htmlspecialchars($correo) . '</small>
                      </div>';
            }

        } else {
            echo '<div class="list-group-item">No se encontraron clientes.</div>';
        }

        exit();
    }

    /**
     * Cambia el estado de una reserva a 'Cancelada'
     */
    public function cancelar()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        //Validar que tengamos un ID
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "No se proporcionó ID de reserva.";
            header("Location: index.php?controller=reservaciones&action=index");
            exit();
        }

        $idReserva = intval($_GET['id']);
        $nuevoEstado = 'Cancelada';

        
        if ($this->reservaModel->cambiarEstado($idReserva, $nuevoEstado)) {
            $_SESSION['success_message'] = "La reserva #{$idReserva} ha sido cancelada.";
        } else {
            $_SESSION['error_message'] = "Error al cancelar la reserva.";
        }

        //Regresar al listado
        header("Location: index.php?controller=reservaciones&action=index");
        exit();
    }
}
?>
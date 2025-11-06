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

    public function crear()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fechaEntrada = $_POST['fechaEntrada'] ?? null;
            $fechaSalida = $_POST['fechaSalida'] ?? null;
            $cantidadPersonas = $_POST['cantidadPersonas'] ?? null;

            // Validación (incluye la nueva variable)
            if (!$fechaEntrada || !$fechaSalida || !$cantidadPersonas) {
                $_SESSION['error_message'] = "Debe ingresar ambas fechas y la cantidad de personas";
                header("Location: index.php?controller=reservaciones&action=crear");
                exit();
            }

            if (strtotime($fechaSalida) <= strtotime($fechaEntrada)) {
                $_SESSION['error_message'] = "La fecha de salida debe ser mayor a la de entrada";
                header("Location: index.php?controller=reservaciones&action=crear");
                exit();
            }

            // Guardar en sesión
            $_SESSION['reserva_temp']['fechaEntrada'] = $fechaEntrada;
            $_SESSION['reserva_temp']['fechaSalida'] = $fechaSalida;
            $_SESSION['reserva_temp']['cantidadPersonas'] = intval($cantidadPersonas);

            // Avanzar al paso 2
            header("Location: index.php?controller=reservaciones&action=verHabitacionesDisponibles");
            exit();
        }

        $page_title = "Seleccionar Fechas";
        $active_page = "reservas";
        $child_view = 'views/reservas/1-fechas.php';
        require_once 'views/layouts/main.php';
    }

    public function edit()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
            $_SESSION['error_message'] = "ID de reserva a modificar inválido.";
            header("Location: index.php?controller=reservaciones&action=index");
            exit();
        }

        $idOriginal = intval($_GET['id']);

        // Obtenemos la reserva original
        $reservaOriginal = $this->reservaModel->obtenerReservaPorId($idOriginal);

        if (!$reservaOriginal) {
            $_SESSION['error_message'] = "Reserva original no encontrada.";
            header("Location: index.php?controller=reservaciones&action=index");
            exit();
        }

        // Solo permitimos editar reservas que ya están 'Confirmadas' (tienen un pago).
        if ($reservaOriginal['EstadoReserva'] !== 'Confirmada') {
            $_SESSION['error_message'] = "Solo las reservas 'Confirmadas' pueden ser modificadas. Esta reserva se encuentra en estado '{$reservaOriginal['EstadoReserva']}'.";
            header("Location: index.php?controller=reservaciones&action=index");
            exit();
        }
        // --- FIN DE LA VALIDACIÓN ---

        // Si es válida, guardamos los datos para iniciar el flujo de edición
        $_SESSION['reserva_original_id'] = $idOriginal;

        $_SESSION['reserva_temp']['fechaEntrada'] = $reservaOriginal['FechaEntrada'];
        $_SESSION['reserva_temp']['fechaSalida'] = $reservaOriginal['FechaSalida'];
        $_SESSION['reserva_temp']['idHabitacion'] = $reservaOriginal['idHabitacion'];
        $_SESSION['reserva_temp']['idCliente'] = $reservaOriginal['idCliente'];
        $_SESSION['reserva_temp']['idPaquete'] = $reservaOriginal['idPaquete'];
        $_SESSION['reserva_temp']['cantidadPersonas'] = $reservaOriginal['CantidadPersonas'];

        header("Location: index.php?controller=reservaciones&action=crear");
        exit();
    }

    public function verHabitacionesDisponibles()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        // Validación 
        if (
            !isset($_SESSION['reserva_temp']['fechaEntrada']) ||
            !isset($_SESSION['reserva_temp']['fechaSalida']) ||
            !isset($_SESSION['reserva_temp']['cantidadPersonas'])
        ) {
            $_SESSION['error_message'] = "Por favor, complete el primer paso.";
            header("Location: index.php?controller=reservaciones&action=crear");
            exit();
        }

        $fechaEntrada = $_SESSION['reserva_temp']['fechaEntrada'];
        $fechaSalida = $_SESSION['reserva_temp']['fechaSalida'];
        $cantidadPersonas = $_SESSION['reserva_temp']['cantidadPersonas'];
        $idOriginal = $_SESSION['reserva_original_id'] ?? null;

        // Pasamos la cantidad de personas al modelo
        $stmt = $this->reservaModel->obtenerHabitacionesDisponibles(
            $fechaEntrada,
            $fechaSalida,
            $idOriginal,
            $cantidadPersonas
        );

        $habitaciones = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        $page_title = "Habitaciones Disponibles";
        $active_page = "reservas";
        $child_view = 'views/reservas/2-habitaciones.php';
        require_once 'views/layouts/main.php';
    }

    public function seleccionarHabitacion()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['idHabitacion'])) {
            header("Location: index.php?controller=reservaciones&action=verHabitacionesDisponibles");
            exit();
        }
        $_SESSION['reserva_temp']['idHabitacion'] = intval($_POST['idHabitacion']);

        if (isset($_SESSION['reserva_original_id'])) {
            header("Location: index.php?controller=reservaciones&action=seleccionarPaquete");
            exit();
        }

        header("Location: index.php?controller=reservaciones&action=asignarCliente");
        exit();
    }

    public function asignarCliente()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
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

    public function confirmarReserva()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        $reserva_temp = $_SESSION['reserva_temp'] ?? null;

        $esEdicion = isset($_SESSION['reserva_original_id']);
        $idOriginal = $esEdicion ? $_SESSION['reserva_original_id'] : null;

        // Validación de sesión
        if (
            !$reserva_temp || !isset(
            $reserva_temp['fechaEntrada'],
            $reserva_temp['fechaSalida'],
            $reserva_temp['idHabitacion'],
            $reserva_temp['idCliente'],
            $reserva_temp['cantidadPersonas']
        )
        ) {
            header("Location: index.php?controller=reservaciones&action=crear");
            exit();
        }

        // Validación de disponibilidad
        if (!$this->validarDisponibilidadTemp($reserva_temp, $idOriginal)) {
            $_SESSION['error_message'] = "¡Error! La habitación ya no está disponible para esas fechas. Por favor, seleccione otra.";
            header("Location: index.php?controller=reservaciones&action=verHabitacionesDisponibles");
            exit();
        }

        // Obtención de datos
        $cliente = $this->reservaModel->obtenerDatosCliente($reserva_temp['idCliente']);
        $habitacion = $this->reservaModel->obtenerDatosHabitacion($reserva_temp['idHabitacion']);
        $paquete = null;
        if (!empty($reserva_temp['idPaquete'])) {
            $paquete = $this->reservaModel->obtenerDatosPaquete($reserva_temp['idPaquete']);
        }

        // Obtener datos originales SOLO si es edición
        $reservaOriginalDatos = $esEdicion ? $this->reservaModel->obtenerReservaPorId($idOriginal) : null;

        // Cálculo de total
        $total = $this->reservaModel->calcularTotal(
            $reserva_temp['idHabitacion'],
            $reserva_temp['idPaquete'] ?? null,
            $reserva_temp['fechaEntrada'],
            $reserva_temp['fechaSalida']
        );
        $_SESSION['reserva_temp']['total'] = $total;

        // --- INICIO DE LÓGICA POST ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Instanciar y llenar la nueva reserva
            $reserva = new Reserva($this->db);
            $reserva->idCliente = $reserva_temp['idCliente'];
            $reserva->idHabitacion = $reserva_temp['idHabitacion'];
            $reserva->idPaquete = $reserva_temp['idPaquete'] ?? null;
            $reserva->FechaEntrada = $reserva_temp['fechaEntrada'];
            $reserva->FechaSalida = $reserva_temp['fechaSalida'];
            $reserva->TotalReservacion = $reserva_temp['total'];
            $reserva->Comentario = $_POST['comentario'] ?? '';

            $reserva->CantidadPersonas = $reserva_temp['cantidadPersonas'];
            $reserva->PrecioHabitacion = $habitacion['PrecioTipoHabitacion'] ?? 0.00;
            $reserva->PrecioPaquete = $paquete['TarifaPaquete'] ?? 0.00;

            // 2. Definir variables de pago
            $tipoPago = $_POST['tipoPago'] ?? 'no_pago';
            $formaPago = $_POST['formaPago'] ?? null;
            $comprobante = $_POST['comprobante'] ?? null;
            $montoPago = 0;
            $tipoTransaccion = '';


            // 3. Lógica bifurcada: Edición vs. Creación
            if ($esEdicion) {

                // --- LÓGICA DE EDICIÓN ---
                $reserva->RegistroCambio = "Reserva original modificada, ID: {$idOriginal}.";
                $reserva->EstadoReserva = 'Confirmada';
                $reserva->EstadoPago = 'Pendiente';

                if ($reserva->crear()) {
                    $nuevoIdReserva = $reserva->idReserva;

                    // --- Transferencia de Pagos ---
                    $pagosAntiguos = $this->reservaModel->obtenerPagosPorReserva($idOriginal);
                    $totalPagadoAntiguo = 0;

                    if ($pagosAntiguos) {
                        foreach ($pagosAntiguos as $pago) {

                            $this->reservaModel->agregarPago(
                                $nuevoIdReserva,
                                $pago['MontoPago'],
                                'Pago Transferido',
                                $pago['FormaPago'],
                                $pago['Comprobante'],
                                "Transferido de reserva #{$idOriginal}"
                            );



                            $totalPagadoAntiguo += (float) $pago['MontoPago'];
                        }
                    }

                    // --- Registrar Pago de Diferencia (si existe) ---
                    $montoPagoDiferencia = (float) ($_POST['montoPagoDiferencia'] ?? 0);

                    if ($montoPagoDiferencia > 0 && $formaPago) {
                        $this->reservaModel->agregarPago(
                            $nuevoIdReserva,
                            $montoPagoDiferencia,
                            'Abono Diferencia',
                            $formaPago,
                            $comprobante
                        );
                    }

                    // --- Lógica de Reembolso ---
                    $totalNuevo = $reserva_temp['total'];
                    $logRefund = "";
                    $mensajeReembolso = ""; // Para el mensaje de éxito

                    if ($totalNuevo < $totalPagadoAntiguo) {
                        $reembolso = $totalPagadoAntiguo - $totalNuevo;
                        $reembolsoFormateado = number_format($reembolso, 2);

                        $logRefund = "REEMBOLSO PROCESADO: $" . $reembolsoFormateado . " (Pagado: $" . $totalPagadoAntiguo . " / Nueva Tarifa: $" . $totalNuevo . ")";
                        $mensajeReembolso = " Se registró un reembolso a favor de $" . $reembolsoFormateado . ".";

                        // Añadir log a la NUEVA reserva
                        $this->reservaModel->actualizarRegistroCambio($nuevoIdReserva, $logRefund);

                        $logAntigua = "CANCELADA (movida a #{$nuevoIdReserva}). " . $logRefund;
                    } else {
                        $logAntigua = "CANCELADA: Movida a nueva reserva #{$nuevoIdReserva}.";
                    }

                    // --- Actualizar EstadoPago y Cancelar Antigua ---
                    $this->reservaModel->actualizarEstadoPago($nuevoIdReserva);
                    $this->reservaModel->actualizarRegistroCambio($idOriginal, $logAntigua);
                    $this->reservaModel->cambiarEstado($idOriginal, 'Cancelada');

                    // --- Limpieza y redirección ---
                    unset($_SESSION['reserva_temp']);
                    unset($_SESSION['reserva_original_id']);
                    $_SESSION['success_message'] = "Reserva modificada exitosamente. La reserva original #{$idOriginal} ha sido cancelada." . $mensajeReembolso;
                    header("Location: index.php?controller=reservaciones&action=index");
                    exit();

                } else {
                    $_SESSION['error_message'] = "Error al crear la nueva reserva (modificación).";
                }

            } else {

                // --- LÓGICA DE CREACIÓN ---
                $reserva->RegistroCambio = null;

                // Determinar estados y montos según el tipo de pago
                if ($tipoPago === 'parcial') {
                    $montoPago = $reserva_temp['total'] * 0.50;
                    $reserva->EstadoReserva = 'Confirmada';
                    $reserva->EstadoPago = 'Parcial';
                    $tipoTransaccion = 'Depósito';

                } elseif ($tipoPago === 'total') {
                    $montoPago = $reserva_temp['total'];
                    $reserva->EstadoReserva = 'Confirmada';
                    $reserva->EstadoPago = 'Completado';
                    $tipoTransaccion = 'Pago Único';

                } else { // 'no_pago'
                    $reserva->EstadoReserva = 'Pendiente';
                    $reserva->EstadoPago = 'Pendiente';
                }

                // Intentar crear la reserva
                if ($reserva->crear()) {

                    // Si se va a registrar un pago, agregarlo
                    if ($montoPago > 0 && $formaPago) {
                        $this->reservaModel->agregarPago(
                            $reserva->idReserva,
                            $montoPago,
                            $tipoTransaccion,
                            $formaPago,
                            $comprobante
                        );
                    }

                    // --- Limpieza y redirección ---
                    unset($_SESSION['reserva_temp']);
                    $_SESSION['success_message'] = "Reserva creada exitosamente (#" . $reserva->idReserva . "). Estado: {$reserva->EstadoReserva}";
                    header("Location: index.php?controller=reservaciones&action=index");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Error al crear la reserva.";
                }
            }

            // Si algo falla, recargar la página de confirmación
            header("Location: index.php?controller=reservaciones&action=confirmarReserva");
            exit();
        }
        // --- FIN DE LÓGICA POST ---

        // --- CÁLCULO PARA LA VISTA (GET) ---
        $totalPagadoAntiguo = 0;
        if ($esEdicion) {
            $pagosAntiguos = $this->reservaModel->obtenerPagosPorReserva($idOriginal);
            if ($pagosAntiguos) {
                foreach ($pagosAntiguos as $pago) {
                    $totalPagadoAntiguo += (float) $pago['MontoPago'];
                }
            }
        }

        // Lógica para mostrar la vista (GET)
        $page_title = $esEdicion ? "Modificar Reserva #{$idOriginal}" : "Confirmar Reserva";
        $active_page = "reservas";
        $child_view = 'views/reservas/5-confirmar.php';
        require_once 'views/layouts/main.php';
    }

    private function validarDisponibilidadTemp($reserva_temp, $idReservaAExcluir = null)
    {
        $fechaEntrada = $reserva_temp['fechaEntrada'];
        $fechaSalida = $reserva_temp['fechaSalida'];
        $idHabitacion = $reserva_temp['idHabitacion'];
        $cantidadPersonas = $reserva_temp['cantidadPersonas'];

        // Se pasa la ID de exclusión y la cantidad de personas al modelo
        $stmt = $this->reservaModel->obtenerHabitacionesDisponibles(
            $fechaEntrada,
            $fechaSalida,
            $idReservaAExcluir,
            $cantidadPersonas
        );

        if (!$stmt) {
            return false;
        }

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (intval($row['idHabitacion']) === intval($idHabitacion)) {
                return true;
            }
        }
        return false; // No disponible (sea por fecha o por capacidad)
    }

    public function index()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        $filtro_texto = $_GET['filtro_texto'] ?? null;
        $filtro_fecha = $_GET['filtro_fecha'] ?? null;
        $stmt = $this->reservaModel->obtenerTodas($filtro_texto, $filtro_fecha);
        $reservas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        $page_title = "Reservas";
        $active_page = "reservas";
        $child_view = 'views/reservas/index.php';
        require_once 'views/layouts/main.php';
    }

    public function buscarClienteAjax()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        $termino = $_GET['q'] ?? '';
        if (strlen($termino) < 2) {
            echo '<div class="list-group-item">Escriba al menos 2 letras...</div>';
            exit();
        }
        $stmt = $this->reservaModel->buscarCliente($termino);
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

    public function cancelar()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        // 1. Validar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Acción no permitida.";
            header("Location: index.php?controller=reservaciones&action=index");
            exit();
        }

        // 2. Obtener datos del formulario del modal
        $idReserva = $_POST['idReservaCancelar'] ?? null;
        $comentario = $_POST['comentarioCancelacion'] ?? ''; // <-- Usar string vacío
        $nuevoEstado = 'Cancelada';

        if (!$idReserva) {
            $_SESSION['error_message'] = "No se proporcionó ID de reserva.";
            header("Location: index.php?controller=reservaciones&action=index");
            exit();
        }

        // 3. Construir el comentario (Siempre se construye el encabezado)

        $usuarioActual = $_SESSION['NombreUsuario'] ?? 'Sistema';

        $fechaActual = date('d-m-Y H:i');

        // Creamos el encabezado SIEMPRE
        $comentarioFinal = "--- CANCELACIÓN ($fechaActual por $usuarioActual) ---";

        // Añadimos el comentario del usuario (del textarea) 
        if (!empty($comentario)) {
            $comentarioFinal .= "\n" . $comentario;
        }

        // 4. Llamar a la función del modelo 
        if ($this->reservaModel->cambiarEstado($idReserva, $nuevoEstado, $comentarioFinal)) {
            $_SESSION['success_message'] = "La reserva #{$idReserva} ha sido cancelada.";
        } else {
            $_SESSION['error_message'] = "Error al cancelar la reserva.";
        }

        header("Location: index.php?controller=reservaciones&action=index");
        exit();
    }

    public function cancelarCreacion()
    {
        unset($_SESSION['reserva_temp']);
        unset($_SESSION['reserva_original_id']);
        header("Location: index.php?controller=reservaciones&action=index");
        exit();
    }

    public function obtenerDetalleAjax()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
            header('Content-Type: application/json');
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'ID de reserva inválido.']);
            exit();
        }

        $idReserva = intval($_GET['id']);

        // 1. Obtener los datos principales de la reserva
        $reserva = $this->reservaModel->obtenerReservaPorId($idReserva);

        if (!$reserva) {
            header('Content-Type: application/json');
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Reserva no encontrada.']);
            exit();
        }

        // 2. Obtener el historial de pagos
        $pagos = $this->reservaModel->obtenerPagosPorReserva($idReserva);

        if ($pagos === false) {
            header('Content-Type: application/json');
            http_response_code(500); // Server Error
            echo json_encode(['error' => 'Error al obtener historial de pagos.']);
            exit();
        }

        // 3. Devolver todo como un solo objeto JSON
        header('Content-Type: application/json');
        echo json_encode([
            'reserva' => $reserva,
            'pagos' => $pagos
        ]);
        exit();
    }

    public function realizarPagoAjax()
    {
        // 1. Seguridad y autenticación
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        // 2. Definir tipo de respuesta
        header('Content-Type: application/json');

        // 3. Validar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
            exit();
        }

        // 4. Recopilar y validar datos de entrada
        $idReserva = $_POST['idReserva'] ?? null;
        $monto = $_POST['monto'] ?? null;
        $formaPago = $_POST['formaPago'] ?? null;
        $tipoTransaccion = $_POST['tipoTransaccion'] ?? 'Abono'; // 'Abono' o 'Pago Final'
        $comprobante = $_POST['comprobante'] ?? null;

        if (!$idReserva || !$monto || !$formaPago) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'error' => 'Datos incompletos: Se requiere idReserva, monto y formaPago.']);
            exit();
        }

        $montoFloat = floatval($monto);
        if ($montoFloat <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'El monto debe ser un número positivo.']);
            exit();
        }

        // 5. Obtener reserva para validación
        $reserva = $this->reservaModel->obtenerReservaPorId($idReserva);
        if (!$reserva) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Reserva no encontrada.']);
            exit();
        }

        // 6. Procesar el pago
        try {
            $pagoExitoso = $this->reservaModel->agregarPago(
                $idReserva,
                $montoFloat,
                $tipoTransaccion,
                $formaPago,
                $comprobante
            );

            if (!$pagoExitoso) {
                throw new Exception('Error al guardar el pago en el modelo.');
            }

            // 7. Actualizar el EstadoPago (crítico)
            $actualizacionEstadoExito = $this->reservaModel->actualizarEstadoPago($idReserva);
            if (!$actualizacionEstadoExito) {
                // El pago se guardó, pero el estado de la reserva no se actualizó.
                // Es un estado inconsistente, pero informamos al usuario.
                throw new Exception('Pago guardado, pero falló la actualización del estado de pago de la reserva.');
            }

            // 8. Si la reserva estaba 'Pendiente' (sin pago), la movemos a 'Confirmada'
            if ($reserva['EstadoReserva'] === 'Pendiente') {
                $this->reservaModel->cambiarEstado($idReserva, 'Confirmada');
            }

            // 9. Devolver éxito
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Pago agregado y estado de reserva actualizado exitosamente.'
            ]);
            exit();

        } catch (Exception $e) {
            // 10. Manejar errores
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor: ' . $e->getMessage()]);
            exit();
        }
    }

    public function actualizarComentarioAjax()
    {
        // 1. Seguridad y autenticación
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);
        header('Content-Type: application/json');

        // 2. Validar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
            exit();
        }

        // 3. Recopilar y validar datos
        $idReserva = $_POST['idReserva'] ?? null;
        $comentario = $_POST['comentario'] ?? ''; // Acepta comentario vacío

        if (!$idReserva) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'error' => 'ID de reserva inválido.']);
            exit();
        }

        // 4. Ejecutar la actualización
        try {
            if ($this->reservaModel->actualizarComentario($idReserva, $comentario)) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Comentario actualizado.']);
            } else {
                throw new Exception('Error al guardar en el modelo.');
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
        }
        exit();
    }

    public function registrarCheckIn()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        $idReserva = $_GET['id'] ?? null;

        if (!$idReserva) {
            $_SESSION['error_message'] = "No se proporcionó ID de reserva.";
        } else {
            if ($this->reservaModel->registrarCheckIn($idReserva)) {
                $_SESSION['success_message'] = "Check-In registrado exitosamente para la reserva #{$idReserva}.";
            } else {
                $_SESSION['error_message'] = "Error al registrar el Check-In. Verifique el estado de la reserva.";
            }
        }

        header("Location: index.php?controller=reservaciones&action=index");
        exit();
    }

    public function registrarCheckOut()
    {
        AuthController::requerirRol(['Administrador', 'Gerencia', 'Recepción']);

        $idReserva = $_GET['id'] ?? null;

        if (!$idReserva) {
            $_SESSION['error_message'] = "No se proporcionó ID de reserva.";
        } else {
            if ($this->reservaModel->registrarCheckOut($idReserva)) {
                $_SESSION['success_message'] = "Check-Out registrado exitosamente para la reserva #{$idReserva}.";
            } else {
                $_SESSION['error_message'] = "Error al registrar el Check-Out. Verifique el estado de la reserva.";
            }
        }

        header("Location: index.php?controller=reservaciones&action=index");
        exit();
    }
}
?>
<?php
// --- LÓGICA DE VISUALIZACIÓN  ---
$esEdicion = isset($reservaOriginalDatos);
$tituloPagina = $esEdicion ? "Modificar Reserva #{$reservaOriginalDatos['idReserva']}" : "Paso 5: Confirmar Reserva";

// --- FORMATEAR FECHAS (d-m-Y) ---
$fechaEntradaNueva = date('d-m-Y', strtotime($_SESSION['reserva_temp']['fechaEntrada']));
$fechaSalidaNueva = date('d-m-Y', strtotime($_SESSION['reserva_temp']['fechaSalida']));

// --- LÓGICA DE CÁLCULO DE PAGOS ---
$totalNuevo = (float) ($total ?? 0);

// Aseguramos que $totalPagadoAntiguo sea un float (0.0) si no existe.
$totalPagadoAntiguo = (float) ($totalPagadoAntiguo ?? 0.0);

$diferencia = 0.0;
$montoParcialNuevo = $totalNuevo * 0.5;

if ($esEdicion) {
    $diferencia = $totalNuevo - $totalPagadoAntiguo;
}
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <div class="card shadow-sm border-0">

            <div class="card-header bg-primary text-white p-3">
                <h2 class="h4 mb-0"><?php echo htmlspecialchars($tituloPagina); ?></h2>
            </div>

            <form method="POST" action="index.php?controller=reservaciones&action=confirmarReserva" id="formConfirmar">

                <div class="card-body p-4">

                    <?php if (!empty($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error_message'];
                            unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">

                        <?php if ($esEdicion): ?>
                            <div class="col-md-6 mb-3">
                                <h5 class="mb-2">Reserva Original (#<?= $reservaOriginalDatos['idReserva'] ?>)</h5>
                                <ul class="list-group list-group-flush border border-warning rounded">
                                    <li class="list-group-item bg-light py-2"><strong>Cliente:</strong>
                                        <?= htmlspecialchars($reservaOriginalDatos['NombreCliente']) ?></li>
                                    <li class="list-group-item bg-light py-2"><strong>Habitación:</strong>
                                        <?= htmlspecialchars($reservaOriginalDatos['NombreTipoHabitacion']) ?>
                                        (<?= htmlspecialchars($reservaOriginalDatos['NumeroHabitacion']) ?>)</li>
                                    <li class="list-group-item bg-light py-2"><strong>Fechas:</strong>
                                        <?= date('d-m-Y', strtotime($reservaOriginalDatos['FechaEntrada'])) ?> →
                                        <?= date('d-m-Y', strtotime($reservaOriginalDatos['FechaSalida'])) ?>
                                    </li>

                                    <li class="list-group-item bg-light fw-bold py-2"><strong>Total Pagado:</strong>
                                        $<?= number_format($totalPagadoAntiguo, 2) ?></li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="<?= $esEdicion ? 'col-md-6' : 'col-md-12'; ?>">
                            <h5 class="mb-2"><?= $esEdicion ? 'Nueva Propuesta' : 'Detalles de la Reserva'; ?></h5>

                            <ul class="list-group list-group-flush border border-primary rounded">

                                <li class="list-group-item py-2">
                                    <strong>Cliente:</strong>
                                    <?= htmlspecialchars($cliente['NombreCliente'] ?? 'Cliente no encontrado') ?>
                                </li>

                                <li class="list-group-item py-2">
                                    <div class="row">
                                        <div class="col-8">
                                            <strong>Habitación:</strong>
                                            <?= htmlspecialchars($habitacion['NombreTipoHabitacion'] ?? 'Tipo no encontrado') ?>
                                            (<?= htmlspecialchars($habitacion['NumeroHabitacion'] ?? 'N/A') ?>)
                                        </div>
                                        <div class="col-4 text-end fw-bold">
                                            $<?= number_format($habitacion['PrecioTipoHabitacion'] ?? 0, 2) ?>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item py-2">
                                    <strong>Nuevas Fechas:</strong>
                                    <?= $fechaEntradaNueva ?> → <?= $fechaSalidaNueva ?>
                                </li>

                                <li class="list-group-item py-2">
                                    <div class="row">
                                        <div class="col-8"><strong>Huéspedes:</strong></div>
                                        <div class="col-4 text-end fw-bold">
                                            <?= htmlspecialchars($_SESSION['reserva_temp']['cantidadPersonas']) ?>
                                            persona(s)
                                        </div>
                                    </div>
                                </li>

                                <?php
                                $tarifaPaquete = 0;
                                $nombrePaquete = "Solo Habitación";

                                if ($paquete) {
                                    $nombrePaquete = $paquete['NombrePaquete'];
                                    $tarifaPaquete = $paquete['TarifaPaquete'];
                                }
                                ?>
                                <li class="list-group-item py-2">
                                    <div class="row">
                                        <div class="col-8">
                                            <strong>Paquete:</strong>
                                            <?= htmlspecialchars($nombrePaquete) ?>
                                        </div>
                                        <div class="col-4 text-end fw-bold">
                                            + $<?= number_format($tarifaPaquete, 2) ?>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item bg-primary text-white py-2">
                                    <div class="row">
                                        <div class="col-8">
                                            <strong class="fs-5">TOTAL FINAL</strong>
                                        </div>
                                        <div class="col-4 text-end">
                                            <strong class="fs-5">$<?= number_format($totalNuevo, 2) ?></strong>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-4" id="seccion-pago">
                        <h5 class="mb-3">Gestión de Pago</h5>

                        <?php if ($esEdicion): ?>

                            <input type="hidden" name="tipoPago" value="edicion">

                            <?php if ($diferencia > 0): ?>
                                <div class="alert alert-warning">
                                    <strong>Monto a Pagar (Diferencia): $<?= number_format($diferencia, 2) ?></strong>
                                </div>
                                <p>Para confirmar el cambio, debe registrar el pago de la diferencia.</p>

                                <input type="hidden" id="montoPagoDiferencia" name="montoPagoDiferencia"
                                    value="<?= $diferencia ?>">
                                <input type="hidden" id="tipoPago" name="tipoPago" value="diferencia">

                                <div class="row" id="camposPagoContenedor">
                                    <div class="col-md-6">
                                        <label for="formaPago" class="form-label fw-bold">Forma de Pago:</label>
                                        <select id="formaPago" name="formaPago" class="form-select" required>
                                            <option value="" disabled selected>Seleccione...</option>
                                            <option value="Tarjeta">Tarjeta</option>
                                            <option value="Transferencia">Transferencia</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="comprobante" class="form-label fw-bold">Comprobante (Opcional):</label>
                                        <input type="text" id="comprobante" name="comprobante" class="form-control">
                                    </div>
                                </div>

                            <?php elseif ($diferencia < 0): ?>
                                <div class="alert alert-success">
                                    La nueva reserva es más económica, se deberá procesar un reembolso de:
                                    <strong>$<?= number_format(abs($diferencia), 2) ?></strong>
                                </div>
                                <input type="hidden" id="tipoPago" name="tipoPago" value="reembolso">
                                <div id="camposPagoContenedor" style="display: none;"></div> <?php else: ?>
                                <div class="alert alert-info">
                                    No hay diferencia de tarifa. El pago transferido cubre el nuevo total.
                                </div>
                                <input type="hidden" id="tipoPago" name="tipoPago" value="sin_diferencia">
                                <div id="camposPagoContenedor" style="display: none;"></div> <?php endif; ?>

                        <?php else: ?>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="tipoPago" id="tipoPagoNo" value="no_pago"
                                    checked>
                                <label class="form-check-label" for="tipoPagoNo">
                                    <strong>No añadir pago ahora (Pendiente)</strong>
                                    <small class="d-block text-danger">El pago mínimo (50%) es requerido para confirmar. La
                                        reserva podría cancelarse (48h / 1 sem. antes).</small>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="tipoPago" id="tipoPagoParcial"
                                    value="parcial">
                                <label class="form-check-label" for="tipoPagoParcial">
                                    <strong>Abono Parcial (50%): $<?= number_format($montoParcialNuevo, 2) ?></strong>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="tipoPago" id="tipoPagoTotal"
                                    value="total">
                                <label class="form-check-label" for="tipoPagoTotal">
                                    <strong>Pago Total: $<?= number_format($totalNuevo, 2) ?></strong>
                                </label>
                            </div>

                            <div class="row mt-3" id="camposPagoContenedor" style="display: none;">
                                <div class="col-md-6">
                                    <label for="formaPago" class="form-label fw-bold">Forma de Pago:</label>
                                    <select id="formaPago" name="formaPago" class="form-select">
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                        <option value="Transferencia">Transferencia</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="comprobante" class="form-label fw-bold">Comprobante (Opcional):</label>
                                    <input type="text" id="comprobante" name="comprobante" class="form-control">
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                    <div class="form-group mt-4">
                        <label for="comentario" class="form-label fw-bold">Comentario Adicional (Opcional):</label>
                        <textarea id="comentario" name="comentario" class="form-control" rows="3"
                            placeholder="Anotaciones sobre el cliente, peticiones especiales, o motivo del cambio..."><?= htmlspecialchars($reservaOriginalDatos['Comentario'] ?? '') ?></textarea>
                    </div>

                    <?php if ($esEdicion): ?>
                        <p class="mt-3 text-danger fw-bold">
                            <i class="fas fa-exclamation-triangle"></i>
                            Al confirmar, la reserva original (ID: #<?= $reservaOriginalDatos['idReserva'] ?>) será
                            <strong>CANCELADA</strong> y los pagos serán transferidos.
                        </p>
                    <?php endif; ?>

                </div>
                <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3">
                    <div>
                        <a href="index.php?controller=reservaciones&action=seleccionarPaquete"
                            class="btn btn-outline-secondary">
                            ← Volver a Paquetes
                        </a>
                        <a href="index.php?controller=reservaciones&action=cancelarCreacion"
                            class="btn btn-secondary ms-2">
                            Cancelar
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" id="btnConfirmarSubmit">
                        <i class="fas fa-check"></i> Confirmar <?= $esEdicion ? 'Cambio' : 'Reserva'; ?>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- Referencias a elementos ---
        const esEdicion = <?= $esEdicion ? 'true' : 'false' ?>;

        // Formulario y botón principal
        const form = document.getElementById('formConfirmar');
        const btnConfirmar = document.getElementById('btnConfirmarSubmit');

        // Contenedor de campos de pago
        const camposPagoContenedor = document.getElementById('camposPagoContenedor');

        // Selectores de tipo de pago (solo creación)
        const radioNoPago = document.getElementById('tipoPagoNo');
        const radioParcial = document.getElementById('tipoPagoParcial');
        const radioTotal = document.getElementById('tipoPagoTotal');
        const tipoPagoRadios = document.querySelectorAll('input[name="tipoPago"]');

        // Campos de pago
        const selectFormaPago = document.getElementById('formaPago');

        // Datos para lógica
        // Usamos '<?php echo $fechaEntradaNueva; ?>' para obtener la fecha formateada (d-m-Y)
        const fechaEntradaNueva = '<?php echo $fechaEntradaNueva; ?>';
        const hoy = new Date().toLocaleDateString('es-SV', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const esParaHoy = (fechaEntradaNueva === hoy);

        // --- Función principal de validación ---
        function validarEstadoFormulario() {

            let tipoPagoSeleccionado = '';
            if (esEdicion) {
                tipoPagoSeleccionado = document.getElementById('tipoPago')?.value || 'sin_diferencia';
            } else {
                tipoPagoSeleccionado = document.querySelector('input[name="tipoPago"]:checked')?.value || 'no_pago';
            }

            let camposPagoVisibles = false;
            let formaPagoRequerida = false;

            // Lógica para mostrar/ocultar "Efectivo"
            actualizarOpcionesEfectivo(tipoPagoSeleccionado);

            if (esEdicion) {
                // --- LÓGICA DE EDICIÓN ---
                if (tipoPagoSeleccionado === 'diferencia') {
                    camposPagoVisibles = true;
                    formaPagoRequerida = true;
                } else {
                    // 'reembolso' o 'sin_diferencia'
                    camposPagoVisibles = false;
                    formaPagoRequerida = false;
                }
            } else {
                // --- LÓGICA DE CREACIÓN ---
                if (tipoPagoSeleccionado === 'parcial' || tipoPagoSeleccionado === 'total') {
                    camposPagoVisibles = true;
                    formaPagoRequerida = true;
                } else {
                    // 'no_pago'
                    camposPagoVisibles = false;
                    formaPagoRequerida = false;
                }
            }

            // Aplicar visibilidad y 'required'
            if (camposPagoVisibles) {
                camposPagoContenedor.style.display = 'flex'; // Usamos flex para que el .row funcione
                if (selectFormaPago) {
                    selectFormaPago.required = formaPagoRequerida;
                }
            } else {
                camposPagoContenedor.style.display = 'none';
                if (selectFormaPago) {
                    selectFormaPago.required = false;
                    selectFormaPago.value = ''; // Limpiar selección si se oculta
                }
            }

            // Validar el botón de confirmar
            validarBotonSubmit();
        }

        // --- Función para habilitar/deshabilitar el botón de confirmar ---
        function validarBotonSubmit() {
            if (!selectFormaPago) {
                // Si no hay selector de forma de pago (ej. edición con reembolso), siempre está habilitado
                btnConfirmar.disabled = false;
                return;
            }

            if (selectFormaPago.required && !selectFormaPago.value) {
                // Si el selector es requerido pero no tiene valor
                btnConfirmar.disabled = true;
            } else {
                // Si no es requerido, o si es requerido y tiene valor
                btnConfirmar.disabled = false;
            }
        }

        // --- Función para manejar las opciones de "Efectivo" ---
        function actualizarOpcionesEfectivo(tipoPago) {
            if (!selectFormaPago) return; // No hay nada que hacer si no existe el select

            const opcionEfectivoExistente = selectFormaPago.querySelector('option[value="Efectivo"]');

            // Tus reglas:
            // 1. Si es PARCIAL (creación) -> NUNCA efectivo
            // 2. Si es TOTAL (creación) Y esParaHoy -> SÍ efectivo
            // 3. Si es DIFERENCIA (edición) Y esParaHoy -> SÍ efectivo

            let permitirEfectivo = false;

            if (tipoPago === 'total' && esParaHoy) {
                permitirEfectivo = true;
            } else if (tipoPago === 'diferencia' && esParaHoy) {
                permitirEfectivo = true;
            }
            // En 'parcial' o 'no_pago', 'permitirEfectivo' se queda en false

            if (permitirEfectivo) {
                if (!opcionEfectivoExistente) {
                    // Añadirlo
                    const opt = document.createElement('option');
                    opt.value = 'Efectivo';
                    opt.textContent = 'Efectivo';
                    selectFormaPago.appendChild(opt);
                }
            } else {
                if (opcionEfectivoExistente) {
                    // Quitarlo
                    // Si estaba seleccionado, limpiar la selección
                    if (selectFormaPago.value === 'Efectivo') {
                        selectFormaPago.value = '';
                    }
                    opcionEfectivoExistente.remove();
                }
            }
        }

        // --- Event Listeners ---
        if (esEdicion) {
            // En edición, solo necesitamos validar el botón si el select de pago existe
            if (selectFormaPago) {
                selectFormaPago.addEventListener('change', validarBotonSubmit);
            }
        } else {
            // En creación, escuchamos los cambios en los radios
            tipoPagoRadios.forEach(radio => {
                radio.addEventListener('change', validarEstadoFormulario);
            });
            // Y también en el select de forma de pago
            if (selectFormaPago) {
                selectFormaPago.addEventListener('change', validarBotonSubmit);
            }
        }

        // --- Ejecución Inicial ---
        validarEstadoFormulario();
    });
</script>
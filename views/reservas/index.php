<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Reservas'); ?></h1>
    <a href="index.php?controller=reservaciones&action=crear" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Reserva
    </a>
</div>

<!-- Contenedor principal estilo tarjeta -->
<div class="card shadow-sm border-0">
    <div class="card-body">

        <!-- Formulario de Filtros -->
        <form method="GET" action="index.php" class="p-3 mb-3">
            <input type="hidden" name="controller" value="reservaciones">
            <input type="hidden" name="action" value="index">

            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="filtro_texto" class="form-label">Cliente o ID Reserva:</label>
                    <input type="text" class="form-control" name="filtro_texto" id="filtro_texto"
                        value="<?= htmlspecialchars($_GET['filtro_texto'] ?? '') ?>"
                        placeholder="Nombre del cliente o ID de la reservación">
                </div>
                <div class="col-md-4">
                    <label for="filtro_fecha" class="form-label">Fecha de Entrada:</label>
                    <input type="date" class="form-control" name="filtro_fecha" id="filtro_fecha"
                        value="<?= htmlspecialchars($_GET['filtro_fecha'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="index.php?controller=reservaciones&action=index" class="btn btn-light">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>

        <?php
        // Mensajes de éxito o error
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $_SESSION['success_message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo $_SESSION['error_message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <!-- Tabla de Reservas -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Habitación</th>
                        <th>Fechas</th>
                        <th>Estado Reserva</th>
                        <th>Estado Pago</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($reservas)) {
                        foreach ($reservas as $row) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['idReserva']); ?></td>
                                <td><?php echo htmlspecialchars($row['NombreCliente']); ?></td>
                                <td>
                                    <?php
                                    echo htmlspecialchars($row['NombreTipoHabitacion']) .
                                        " (<strong>" . htmlspecialchars($row['NumeroHabitacion']) . "</strong>)<br>" .
                                        "<small class='text-muted'>" . htmlspecialchars($row['CantidadPersonas']) . " Persona(s)</small>";
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $entrada = date('d-m-Y', strtotime($row['FechaEntrada']));
                                    $salida = date('d-m-Y', strtotime($row['FechaSalida']));
                                    ?>
                                    <strong>Entrada:</strong> <?php echo $entrada; ?><br>
                                    <strong>Salida:</strong> <?php echo $salida; ?>
                                </td>
                                <td>
                                    <?php
                                    $estado = $row['EstadoReserva'];
                                    $badgeClass = '';
                                    switch ($estado) {
                                        case 'Confirmada':
                                            $badgeClass = 'badge bg-success';
                                            break;
                                        case 'En Curso':
                                            $badgeClass = 'badge bg-primary';
                                            break;
                                        case 'Cancelada':
                                            $badgeClass = 'badge bg-danger';
                                            break;
                                        case 'Pendiente':
                                            $badgeClass = 'badge bg-warning text-dark';
                                            break;
                                        case 'Completada':
                                            $badgeClass = 'badge bg-dark';
                                            break;
                                        default:
                                            $badgeClass = 'badge bg-secondary';
                                    }
                                    ?>
                                    <span class="<?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($estado); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $pago = $row['EstadoPago'];
                                    $pagoBadge = '';
                                    if ($pago == 'Completado') {
                                        $pagoBadge = 'badge bg-success';
                                    } elseif ($pago == 'Pendiente') {
                                        $pagoBadge = 'badge bg-warning text-dark';
                                    } elseif ($pago == 'Parcial') {
                                        $pagoBadge = 'badge bg-info text-dark';
                                    } else {
                                        $pagoBadge = 'badge bg-secondary';
                                    }
                                    ?>
                                    <span class="<?php echo $pagoBadge; ?>">
                                        <?php echo htmlspecialchars($pago); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['CheckIn'])): ?>
                                        <?php $checkInF = date('d-m-Y h:i A', strtotime($row['CheckIn'])); ?>
                                        <span class="badge bg-info text-dark"><?php echo $checkInF; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['CheckOut'])): ?>
                                        <?php $checkOutF = date('d-m-Y h:i A', strtotime($row['CheckOut'])); ?>
                                        <span class="badge bg-dark"><?php echo $checkOutF; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">Pendiente</span>
                                    <?php endif; ?>
                                </td>

                                <td style="white-space: nowrap;">

                                    <button type="button" class="btn-action btn-info btnVerDetalle" data-bs-toggle="modal"
                                        data-bs-target="#modalDetalleReserva" data-id="<?php echo $row['idReserva']; ?>"
                                        title="Ver Detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <?php
                                    // Lógica de botones de estado
                                    $estado = $row['EstadoReserva'];

                                    if ($estado == 'Confirmada'):
                                        ?>
                                        <a href="index.php?controller=reservaciones&action=registrarCheckIn&id=<?php echo $row['idReserva']; ?>"
                                            class="btn-action btn-success"
                                            onclick="return confirm('¿Desea registrar el ingreso de los clientes?');"
                                            title="Registrar Check-In">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </a>

                                        <a href="index.php?controller=reservaciones&action=edit&id=<?php echo $row['idReserva']; ?>"
                                            class="btn-action btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn-action btn-delete btnAbrirModalCancelar"
                                            data-bs-toggle="modal" data-bs-target="#modalCancelar"
                                            data-id-reserva="<?php echo $row['idReserva']; ?>" title="Cancelar">
                                            <i class="fas fa-ban"></i>
                                        </button>

                                    <?php elseif ($estado == 'En Curso'): ?>

                                        <a href="index.php?controller=reservaciones&action=registrarCheckOut&id=<?php echo $row['idReserva']; ?>"
                                            class="btn-action btn-dark"
                                            onclick="return confirm('¿Desea registrar la salida de los clientes?');"
                                            title="Registrar Check-Out">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </a>

                                    <?php elseif ($estado == 'Pendiente'): ?>

                                        <button type="button" class="btn-action btn-delete btnAbrirModalCancelar"
                                            data-bs-toggle="modal" data-bs-target="#modalCancelar"
                                            data-id-reserva="<?php echo $row['idReserva']; ?>" title="Cancelar">
                                            <i class="fas fa-ban"></i>
                                        </button>

                                    <?php else: // Cancelada, Completada ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="9" class="text-center">No se encontraron reservaciones que coincidan con los
                                filtros.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>



<div class="modal fade" id="modalDetalleReserva" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- El contenido se genera por JS y se inserta aquí -->
            <div class="modal-body p-0" id="modalDetalleContenido">

                <!-- Spinner de carga inicial -->
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 fs-5">Cargando detalles...</p>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCancelar" tabindex="-1" aria-labelledby="modalCancelarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="index.php?controller=reservaciones&action=cancelar" method="POST">

                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="modalCancelarLabel">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar Cancelación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>¿Está seguro de que desea <strong>CANCELAR</strong> esta reservación? Esta acción no se puede
                        deshacer.</p>

                    <input type="hidden" name="idReservaCancelar" id="idReservaCancelarInput" value="">

                    <div class="form-group">
                        <label for="comentarioCancelacion" class="form-label fw-bold">Motivo de la Cancelación
                            (Opcional):</label>
                        <textarea name="comentarioCancelacion" id="comentarioCancelacion" class="form-control" rows="3"
                            placeholder="Ej: Cliente llamó, no se presentó, duplicada..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">
                        Sí, Cancelar Reservación
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- REFERENCIAS A MODALES ---
        const modalDetalle = document.getElementById('modalDetalleReserva');
        const modalContenido = document.getElementById('modalDetalleContenido');
        const modalCancelar = document.getElementById('modalCancelar');

        let pagoRealizadoConExito = false;

        // --- Funciones de formato  ---
        function formatLocalDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString + 'T00:00:00');
            return date.toLocaleDateString('es-SV', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function formatLocalDateTime(dateTimeString) {
            if (!dateTimeString) return 'N/A';
            const date = new Date(dateTimeString);
            return date.toLocaleDateString('es-SV', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }

        function construirModalHTML(data) {
            const r = data.reserva;
            const pagos = data.pagos;

            // ... Lógica de Pagos y Balance - ...
            let pagosHtml = '<p class="text-muted p-3">No se han registrado pagos.</p>';
            let totalPagado = 0;
            if (pagos && pagos.length > 0) {
                pagosHtml = '<ul class="list-group list-group-flush">';
                pagos.forEach(pago => {
                    totalPagado += parseFloat(pago.MontoPago);
                    pagosHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                            <div><strong>${pago.TipoTransaccion}</strong> (${pago.FormaPago})<small class="d-block text-muted">${formatLocalDateTime(pago.FechaPago)}</small></div>
                            <span class="badge bg-success rounded-pill fs-6">$${parseFloat(pago.MontoPago).toFixed(2)}</span>
                        </li>`;
                });
                pagosHtml += '</ul>';
            }
            const totalReserva = parseFloat(r.TotalReservacion);
            const balance = totalReserva - totalPagado;
            let balanceHtml = '';
            const epsilon = 0.001;
            if (balance <= epsilon) {
                balanceHtml =
                    `<li class="list-group-item list-group-item-success d-flex justify-content-between fs-5 p-3"><strong>Balance:</strong> <strong>$0.00</strong></li>`;
            } else {
                balanceHtml =
                    `<li class="list-group-item list-group-item-warning d-flex justify-content-between fs-5 p-3"><strong>Balance Pendiente:</strong> <strong class="text-danger">$${balance.toFixed(2)}</strong></li>`;
            }

            // ... (Lógica Historial de Cambios - ) ...
            let historialCambiosHtml = '';
            if (r.RegistroCambio && r.RegistroCambio.trim() !== '') {
                historialCambiosHtml = `
                <h5 class="text-dark mt-4">Historial de Cambios</h5>
                <div class="p-3 bg-light border border-warning rounded">
                    <i class="fas fa-info-circle text-warning me-2"></i>
                    ${r.RegistroCambio.replace(/\n/g, '<br>')}
                </div>`;
            }

            // ... (Lógica Formulario de Pago - ) ...
            let pagoFormHtml = '';
            if (balance > epsilon && (r.EstadoReserva === 'Pendiente' || r.EstadoReserva === 'Confirmada')) {
                const fechaEntrada = new Date(r.FechaEntrada + 'T00:00:00');
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                const esParaHoy = fechaEntrada.getTime() === hoy.getTime();
                const opcionEfectivo = esParaHoy ? '<option value="Efectivo">Efectivo</option>' : '';
                pagoFormHtml = `
                <h5 class="text-dark mt-4">Registrar Nuevo Pago</h5>
                <form id="formAgregarPago" class="p-3 border rounded bg-light">
                    <input type="hidden" name="idReserva" value="${r.idReserva}">
                    <div id="pagoError" class="alert alert-danger" style="display: none;"></div>
                    <div class="mb-3">
                        <label for="montoPago" class="form-label fw-bold">Monto a Pagar:</label>
                        <input type="number" class="form-control" id="montoPago" name="monto" step="0.01" min="1.00" max="${balance.toFixed(2)}" placeholder="Máx. ${balance.toFixed(2)}" required>
                    </div>
                    <div class="mb-3">
                        <label for="formaPagoModal" class="form-label fw-bold">Forma de Pago:</label>
                        <select class="form-select" id="formaPagoModal" name="formaPago" required>
                            <option value="" selected disabled>Seleccione...</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                            ${opcionEfectivo}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comprobanteModal" class="form-label fw-bold">Comprobante (Opcional):</label>
                        <input type="text" class="form-control" id="comprobanteModal" name="comprobante">
                    </div>
                    <input type="hidden" name="tipoTransaccion" value="Abono">
                    <button type="submit" class="btn btn-primary w-100" id="btnSubmitPago"><i class="fas fa-save"></i> Registrar Pago</button>
                </form>`;
            }

            // --- Lógica del Comentario Editable ---
            const comentarioOriginal = r.Comentario ? r.Comentario.replace(/<br>/g, '\n') : '';
            const comentarioHtml = `
                <h5 class="text-dark mt-4">Comentarios</h5>
                <form id="formEditarComentario">
                    <input type="hidden" name="idReserva" value="${r.idReserva}">
                    <input type="hidden" id="comentarioOriginal" value="${comentarioOriginal}">
                    
                    <textarea class="form-control" id="comentarioEditable" name="comentario" rows="4">${comentarioOriginal}</textarea>
                    
                    <div id="comentarioError" class="alert alert-danger mt-2" style="display: none;"></div>
                    
                    <button type="submit" id="btnGuardarComentario" class="btn btn-success btn-sm mt-2" style="display: none;">
                        <i class="fas fa-save"></i> Guardar Comentario
                    </button>
                </form>
                `;

            // --- Construir Modal Completo ---
            const html = `
            <div class="p-3 bg-light d-flex justify-content-between align-items-center border-bottom">
                <h5 class="modal-title mb-0" id="modalDetalleLabel">Detalles de la Reserva #${r.idReserva}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <h5 class="text-dark">Resumen de la Estancia</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 py-2"><strong>Cliente:</strong> ${r.NombreCliente}</li>
                            <li class="list-group-item px-0 py-2"><strong>Fechas:</strong> ${formatLocalDate(r.FechaEntrada)} al ${formatLocalDate(r.FechaSalida)}</li>
                            <li class="list-group-item px-0 py-2"><strong>Huéspedes:</strong> ${r.CantidadPersonas}</li>
                            <li class="list-group-item px-0 py-2"><strong>Habitación:</strong> ${r.NombreTipoHabitacion} (${r.NumeroHabitacion}) <small class="d-block text-muted">Tarifa base: $${parseFloat(r.PrecioHabitacion).toFixed(2)}</small></li>
                            
                            <li class="list-group-item px-0 py-2">
                                <strong>Paquete:</strong> ${r.NombrePaquete ? r.NombrePaquete : 'N/A'}
                                <small class="d-block text-muted">Tarifa paquete: $${parseFloat(r.PrecioPaquete).toFixed(2)}</small>
                            </li>
                            <li class="list-group-item px-0 py-2"><strong>Check-In:</strong> ${formatLocalDateTime(r.CheckIn)}</li>
                            <li class="list-group-item px-0 py-2"><strong>Check-Out:</strong> ${formatLocalDateTime(r.CheckOut)}</li>
                            <li class="list-group-item px-0 py-2"><strong>Fecha Creación:</strong> ${formatLocalDateTime(r.FechaCreacion)}</li>
                            ${r.FechaCancelacion ? `<li class="list-group-item px-0 py-2 text-danger"><strong>Fecha Cancelación:</strong> ${formatLocalDateTime(r.FechaCancelacion)}</li>` : ''}
                        </ul>
                        
                        ${comentarioHtml} ${historialCambiosHtml}

                    </div>
                    <div class="col-lg-5">
                        <h5 class="text-dark">Resumen de Pago</h5>
                        <ul class="list-group border-bottom">
                            <li class="list-group-item d-flex justify-content-between fs-5 p-3"><strong>Total:</strong> <strong>$${totalReserva.toFixed(2)}</strong></li>
                            <li class="list-group-item d-flex justify-content-between p-3"><strong>Total Pagado:</strong> <span class="text-success fw-bold">$${totalPagado.toFixed(2)}</span></li>
                            ${balanceHtml}
                        </ul>
                        <h5 class="text-dark mt-4">Historial de Pagos</h5>
                        <div class="border rounded" style="max-height: 300px; overflow-y: auto;">${pagosHtml}</div>
                        ${pagoFormHtml} 
                    </div>
                </div>
            </div>
            <div class="p-3 bg-light text-end border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>`;

            return html;
        }


        modalDetalle.addEventListener('show.bs.modal', async function (event) {

            pagoRealizadoConExito = false;
            const boton = event.relatedTarget;
            const idReserva = boton.dataset.id;

            async function cargarDatosModal(idReserva) {
                // 1. Mostrar spinner
                modalContenido.innerHTML =
                    `<div class="text-center p-5"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Cargando...</span></div><p class="mt-3 fs-5">Cargando detalles...</p></div>`;

                try {
                    // 2. AJAX Call
                    const response = await fetch(
                        `index.php?controller=reservaciones&action=obtenerDetalleAjax&id=${idReserva}`
                    );
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || `Error ${response.status}`);
                    }
                    const data = await response.json();

                    // 3. Construir HTML
                    modalContenido.innerHTML = construirModalHTML(data);

                    // 4. Adjuntar listeners de formularios (Pago Y Comentario)
                    adjuntarListenerFormularioPago(idReserva);
                    adjuntarListenerFormularioComentario(idReserva); //

                } catch (error) {
                    // 5. Manejar error
                    modalContenido.innerHTML =
                        `<div class="p-3 bg-light d-flex justify-content-between align-items-center border-bottom"><h5 class="modal-title text-danger">Error</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="p-4"><div class="alert alert-danger"><strong>Error:</strong> ${error.message}</div></div><div class="p-3 bg-light text-end border-top"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>`;
                }
            }

            // --- Adjuntar listener al formulario de PAGO ---
            function adjuntarListenerFormularioPago(idReserva) {
                const formPago = document.getElementById('formAgregarPago');
                if (formPago) {
                    formPago.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const btnSubmit = document.getElementById('btnSubmitPago');
                        const errorDiv = document.getElementById('pagoError');
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML =
                            '<span class="spinner-border spinner-border-sm"></span> Procesando...';
                        errorDiv.style.display = 'none';

                        try {
                            const formData = new FormData(formPago);
                            const response = await fetch(
                                'index.php?controller=reservaciones&action=realizarPagoAjax', {
                                method: 'POST',
                                body: formData
                            });
                            const result = await response.json();
                            if (!response.ok || !result.success) {
                                throw new Error(result.error || 'Error desconocido.');
                            }

                            pagoRealizadoConExito =
                                true; // 
                            await cargarDatosModal(idReserva); // Recargamos el modal

                        } catch (error) {
                            errorDiv.textContent = error.message;
                            errorDiv.style.display = 'block';
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML =
                                '<i class="fas fa-save"></i> Registrar Pago';
                        }
                    });
                }
            }

            // ---  Adjuntar listener al formulario de COMENTARIO ---
            function adjuntarListenerFormularioComentario(idReserva) {
                const formComentario = document.getElementById('formEditarComentario');
                const textarea = document.getElementById('comentarioEditable');
                const original = document.getElementById('comentarioOriginal').value;
                const btnGuardar = document.getElementById('btnGuardarComentario');
                const errorDiv = document.getElementById('comentarioError');

                if (!formComentario) return;

                // 1. Mostrar/ocultar el botón de guardar si el texto cambia
                textarea.addEventListener('input', function () {
                    if (textarea.value !== original) {
                        btnGuardar.style.display = 'inline-block';
                    } else {
                        btnGuardar.style.display = 'none';
                    }
                });

                // 2. Manejar el envío (submit) del formulario
                formComentario.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    btnGuardar.disabled = true;
                    btnGuardar.innerHTML =
                        '<span class="spinner-border spinner-border-sm"></span> Guardando...';
                    errorDiv.style.display = 'none';

                    try {
                        const formData = new FormData(formComentario);
                        const response = await fetch(
                            'index.php?controller=reservaciones&action=actualizarComentarioAjax', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();
                        if (!response.ok || !result.success) {
                            throw new Error(result.error || 'Error desconocido.');
                        }

                        // ¡Éxito! Recargamos el modal
                        // No es necesario recargar la página completa
                        await cargarDatosModal(idReserva);

                    } catch (error) {
                        errorDiv.textContent = error.message;
                        errorDiv.style.display = 'block';
                        btnGuardar.disabled = false;
                        btnGuardar.innerHTML =
                            '<i class="fas fa-save"></i> Guardar Comentario';
                    }
                });
            }

            // --- Llamada inicial al abrir el modal ---
            await cargarDatosModal(idReserva);
        });

        // --- Event listener para RECARGAR PÁGINA al cerrar modal (si hubo pago) ---
        modalDetalle.addEventListener('hidden.bs.modal', function (event) {
            if (pagoRealizadoConExito) {
                location.reload();
            }
        });


        // ---  Event listener para el MODAL DE CANCELACIÓN ---
        if (modalCancelar) {
            modalCancelar.addEventListener('show.bs.modal', function (event) {
                // 1. Obtener el botón que abrió el modal
                const boton = event.relatedTarget;
                // 2. Extraer el ID de la reserva (del atributo data-)
                const idReserva = boton.dataset.idReserva;
                // 3. Encontrar el input oculto dentro del modal
                const inputId = document.getElementById('idReservaCancelarInput');
                // 4. Asignar el ID a ese input
                inputId.value = idReserva;

                // (Opcional) Limpiar el textarea por si acaso
                document.getElementById('comentarioCancelacion').value = '';
            });
        }
    });
</script>
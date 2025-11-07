<?php
// Determinar si estamos en "modo edición" (aunque en este paso no se usa, es bueno saberlo)
$esEdicion = !empty($_SESSION['reserva_original_id']);
?>

<!-- ESTRUCTURA DE LAYOUT -->
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8"> <!-- Centra y limita el ancho -->

        <div class="card shadow-sm border-0">

            <div class="card-header bg-primary text-white p-3">
                <h2 class="h4 mb-0"><?php echo htmlspecialchars($page_title ?? 'Paso 3: Asignar Cliente'); ?></h2>
            </div>

            <!-- El formulario ahora solo envuelve el body y el footer -->
            <form method="POST" action="index.php?controller=reservaciones&action=asignarCliente" id="formCliente">

                <div class="card-body p-4">

                    <?php if (!empty($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error_message'];
                            unset($_SESSION['error_message']); ?></div>
                    <?php endif; ?>

                    <!-- Input oculto que guardará el ID del cliente -->
                    <input type="hidden" name="idCliente" id="idClienteSeleccionado" value="">

                    <div class="form-group mb-3">
                        <label for="buscarCliente" class="form-label fw-bold">Buscar Cliente:</label>
                        <input type="text" id="buscarCliente" class="form-control"
                            placeholder="Escriba el Nombre, DUI o correo (mín. 2 letras)" autocomplete="off">
                    </div>

                    <!-- Div para mostrar los resultados de la búsqueda AJAX -->
                    <div id="resultadosCliente" class="mt-2 list-group" style="max-height: 300px; overflow-y: auto;">
                        <!-- Los resultados de la búsqueda aparecerán aquí -->
                    </div>
                </div> <!-- Fin .card-body -->

                <!-- FOOTER PARA BOTONES -->
                <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3">
                    <div>
                        <!-- Botones de navegación izquierda -->
                        <a href="index.php?controller=reservaciones&action=verHabitacionesDisponibles"
                            class="btn btn-outline-secondary">
                            ← Volver a Habitaciones
                        </a>
                        <a href="index.php?controller=reservaciones&action=cancelarCreacion"
                            class="btn btn-secondary ms-2">
                            Cancelar
                        </a>
                    </div>

                    <!-- Botón de navegación derecha -->
                    <button type="submit" id="btnSiguiente" class="btn btn-primary" disabled>
                        Siguiente: Paquetes →
                    </button>
                </div> <!-- Fin .card-footer -->

            </form>

        </div> <!-- Fin .card -->
    </div> <!-- Fin .col -->
</div> <!-- Fin .row.justify-content-center -->


<!-- Modal para Crear Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- El iframe cargará la acción 'crear' del controlador de Clientes -->
                <iframe id="iframeCliente" src="index.php?controller=clientes&action=crear&context=modal"
                    style="width:100%; height:450px; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>


<!-- JavaScript para la búsqueda AJAX -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const inputBusqueda = document.getElementById('buscarCliente');
        const resultadosDiv = document.getElementById('resultadosCliente');
        const inputOculto = document.getElementById('idClienteSeleccionado');
        const btnSiguiente = document.getElementById('btnSiguiente');
        const modalCliente = new bootstrap.Modal(document.getElementById('modalNuevoCliente'));
        const iframeCliente = document.getElementById('iframeCliente');

        // --- 1. BUSCAR CLIENTES (AJAX) ---
        inputBusqueda.addEventListener('input', function () {
            let q = this.value;

            // Limpiamos la selección si el usuario borra la búsqueda
            inputOculto.value = "";
            btnSiguiente.disabled = true;

            if (q.length < 2) {
                resultadosDiv.innerHTML = "";
                return;
            }

            // Llamada AJAX al controlador de reservaciones
            fetch('index.php?controller=reservaciones&action=buscarClienteAjax&q=' + q)
                .then(res => res.text())
                .then(html => {
                    resultadosDiv.innerHTML = html;
                })
                .catch(err => console.error('Error en búsqueda:', err));
        });

        // --- 2. SELECCIONAR UN CLIENTE DE LA LISTA ---
        resultadosDiv.addEventListener('click', function (e) {
            // Buscamos el elemento "padre" que sea un resultado
            let resultado = e.target.closest('.cliente-resultado');

            if (resultado) {
                // Obtenemos los datos del cliente (de los atributos data-)
                const id = resultado.dataset.id;
                const nombre = resultado.dataset.nombre;

                // Ponemos los valores
                inputOculto.value = id;
                inputBusqueda.value = nombre; // Mostramos el nombre en el input

                // Habilitamos el botón de siguiente
                btnSiguiente.disabled = false;

                // Ocultamos los resultados
                resultadosDiv.innerHTML = "";
            }
        });

        // --- 3. (OPCIONAL) Refrescar el iframe del modal ---
        // Esto es útil si el usuario cierra y vuelve a abrir el modal
        document.getElementById('modalNuevoCliente').addEventListener('show.bs.modal', function () {
            // Recargamos el iframe para que esté limpio
            iframeCliente.src = "index.php?controller=clientes&action=crear&context=modal";
        });

    });
</script>
<?php
// --- LÓGICA DE PHP  ---
$esEdicion = !empty($_SESSION['reserva_original_id']);
$fechaEntradaValue = $_SESSION['reserva_temp']['fechaEntrada'] ?? '';
$fechaSalidaValue = $_SESSION['reserva_temp']['fechaSalida'] ?? '';
$cantidadPersonasValue = $_SESSION['reserva_temp']['cantidadPersonas'] ?? 1;
$hoy = date('Y-m-d');
$minFechaEntrada = !$esEdicion ? $hoy : '';
$disabledFechaSalida = empty($fechaEntradaValue) ? 'disabled' : '';
?>

<!-- ESTRUCTURA DE LAYOUT -->
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8"> <!-- Centra y limita el ancho en pantallas grandes -->

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white p-3">
                <h2 class="h4 mb-0">
                    <?php echo $esEdicion ? 'Modificar Fechas y Huéspedes' : 'Paso 1: Fechas y Huéspedes'; ?>
                </h2>
            </div>

            <form method="POST" action="index.php?controller=reservaciones&action=crear">
                <div class="card-body p-4">

                    <?php if (!empty($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error_message'];
                            unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Columna Fecha Entrada -->
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="fechaEntrada" class="form-label fw-bold">Fecha de Entrada:</label>
                                <input type="date" name="fechaEntrada" id="fechaEntrada" class="form-control" required
                                    value="<?= htmlspecialchars($fechaEntradaValue) ?>" min="<?= $minFechaEntrada ?>">
                            </div>
                        </div>

                        <!-- Columna Fecha Salida -->
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="fechaSalida" class="form-label fw-bold">Fecha de Salida:</label>
                                <input type="date" name="fechaSalida" id="fechaSalida" class="form-control" required
                                    value="<?= htmlspecialchars($fechaSalidaValue) ?>" <?= $disabledFechaSalida ?>>
                            </div>
                        </div>

                        <!-- Selector de Personas -->
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="cantidadPersonas" class="form-label fw-bold">Huéspedes:</label>
                                <select name="cantidadPersonas" id="cantidadPersonas" class="form-select" required>
                                    <?php
                                    // Genera opciones del 1 al 6
                                    for ($i = 1; $i <= 6; $i++) {
                                        $selected = ($i == $cantidadPersonasValue) ? 'selected' : '';
                                        $texto = ($i > 1) ? 'Personas' : 'Persona';
                                        echo "<option value=\"$i\" $selected>$i $texto</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div> <!-- Fin del .row -->

                </div> <!-- Fin .card-body -->

                <!-- FOOTER PARA BOTONES -->
                <div class="card-footer bg-light text-end p-3">
                    <a href="index.php?controller=reservaciones&action=cancelarCreacion" class="btn btn-secondary ms-2">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Siguiente: Ver Habitaciones →
                    </button>
                </div> <!-- Fin .card-footer -->

            </form>

        </div> <!-- Fin .card -->
    </div> <!-- Fin .col -->
</div> <!-- Fin .row.justify-content-center -->


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const fechaEntradaEl = document.getElementById('fechaEntrada');
        const fechaSalidaEl = document.getElementById('fechaSalida');

        function actualizarFechaSalida() {
            if (fechaEntradaEl.value) {
                fechaSalidaEl.disabled = false;
                const fechaEntrada = new Date(fechaEntradaEl.value + 'T00:00:00');
                fechaEntrada.setDate(fechaEntrada.getDate() + 1);

                const anio = fechaEntrada.getFullYear();
                const mes = String(fechaEntrada.getMonth() + 1).padStart(2, '0');
                const dia = String(fechaEntrada.getDate()).padStart(2, '0');
                const fechaMinSalida = `${anio}-${mes}-${dia}`;

                fechaSalidaEl.min = fechaMinSalida;

                if (fechaSalidaEl.value < fechaMinSalida) {
                    fechaSalidaEl.value = '';
                }
            } else {
                fechaSalidaEl.disabled = true;
                fechaSalidaEl.value = '';
            }
        }

        fechaEntradaEl.addEventListener('change', actualizarFechaSalida);

        if (fechaEntradaEl.value) {
            actualizarFechaSalida();
        }
    });
</script>
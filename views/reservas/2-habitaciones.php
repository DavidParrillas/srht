<?php
// --- LÓGICA DE PRESELECCIÓN ---
// 1. Determinar si estamos en "modo edición"
$esEdicion = !empty($_SESSION['reserva_original_id']);

// 2. Obtener la habitación que se pre-cargó en el paso 'edit()'
$idHabitacionPreseleccionada = $_SESSION['reserva_temp']['idHabitacion'] ?? null;

// 3. Verificar si la habitación preseleccionada REALMENTE
//    está en la lista de habitaciones disponibles que pasó el modelo.
$preseleccionValida = false;
if ($idHabitacionPreseleccionada !== null) {
    foreach ($habitaciones as $hab) {
        if ($hab['idHabitacion'] == $idHabitacionPreseleccionada) {
            $preseleccionValida = true;
            break;
        }
    }
}
// Si la habitación original ya no está disponible con las nuevas
// fechas, $preseleccionValida será 'false' y no se marcará nada.
?>

<!-- Estilos CSS para las tarjetas seleccionables -->
<style>
    .habitacion-card {
        cursor: pointer;
        border: 2px solid #e9ecef;
        /* Borde gris claro por defecto */
        transition: all 0.2s ease-in-out;
        border-radius: 0.5rem;
    }

    .habitacion-card:hover {
        border-color: #0d6efd;
        /* Azul de Bootstrap */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .habitacion-card.selected {
        border-color: #0d6efd;
        background-color: #f0f6ff;
        /* Un fondo azulado claro */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: scale(1.02);
        /* Efecto ligero de "pop" */
    }

    /* Ocultamos el radio button real, la tarjeta es el control */
    .radio-habitacion {
        display: none;
    }
</style>

<!-- ¡ESTRUCTURA DE LAYOUT! -->
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8"> <!-- Centra y limita el ancho -->

        <div class="card shadow-sm border-0">

            <div class="card-header bg-primary text-white p-3">
                <h2 class="h4 mb-0"><?php echo htmlspecialchars($page_title ?? 'Paso 2: Habitaciones Disponibles'); ?>
                </h2>
            </div>

            <!-- El formulario solo envuelve el body y el footer -->
            <form method="POST" action="index.php?controller=reservaciones&action=seleccionarHabitacion"
                id="formHabitaciones">

                <div class="card-body p-4">

                    <p class="text-muted">
                        Para las fechas:
                        <strong><?= htmlspecialchars(date('d-m-Y', strtotime($_SESSION['reserva_temp']['fechaEntrada']))) ?></strong>
                        al
                        <strong><?= htmlspecialchars(date('d-m-Y', strtotime($_SESSION['reserva_temp']['fechaSalida']))) ?></strong>
                        para
                        <strong><?= htmlspecialchars($_SESSION['reserva_temp']['cantidadPersonas']) ?></strong>
                        persona(s).
                    </p>

                    <?php if (empty($habitaciones)): ?>

                        <div class="alert alert-warning" role="alert">
                            <h4 class="alert-heading">No hay disponibilidad</h4>
                            <p>No se encontraron habitaciones disponibles que coincidan con las fechas y la cantidad de
                                personas seleccionadas.</p>
                            <hr>
                            <!-- Este botón lleva al paso 1, dentro del layout unificado -->
                            <a href="index.php?controller=reservaciones&action=crear" class="btn btn-secondary">
                                ← Cambiar Fechas o Personas
                            </a>
                        </div>

                    <?php else: ?>

                        <!-- LAYOUT DE TARJETAS -->
                        <div class="row">
                            <?php foreach ($habitaciones as $row): ?>
                                <?php
                                $esSeleccionada = ($preseleccionValida && $row['idHabitacion'] == $idHabitacionPreseleccionada);
                                ?>
                                <!-- Ahora son 2 columnas en pantallas medianas y grandes -->
                                <div class="col-md-6 mb-3">
                                    <label class="card h-100 habitacion-card <?= $esSeleccionada ? 'selected' : '' ?>">
                                        <div class="card-body">
                                            <!-- Radio button oculto -->
                                            <input type="radio" name="idHabitacion" value="<?= $row['idHabitacion'] ?>"
                                                class="radio-habitacion" <?= $esSeleccionada ? 'checked' : '' ?> required>

                                            <h5 class="card-title"><?= htmlspecialchars($row['NombreTipoHabitacion']) ?></h5>
                                            <h6 class="card-subtitle mb-2 text-muted">Habitación N°:
                                                <?= htmlspecialchars($row['NumeroHabitacion']) ?></h6>
                                            <p class="card-text mb-1">
                                                <strong>Capacidad:</strong> <?= htmlspecialchars($row['Capacidad']) ?> personas
                                            </p>
                                            <p class="card-text fs-5 text-success fw-bold">
                                                $<?= number_format($row['PrecioTipoHabitacion'], 2) ?> / noche
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php endif; ?> <!-- Fin del if(empty($habitaciones)) -->

                </div> <!-- Fin .card-body -->

                <!-- FOOTER PARA BOTONES -->
                <!-- Solo se muestra el footer si hay habitaciones, sino, el alert ya tiene su botón -->
                <?php if (!empty($habitaciones)): ?>
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3">
                        <div>
                            <!-- Botones de navegación izquierda -->
                            <a href="index.php?controller=reservaciones&action=crear" class="btn btn-outline-secondary">
                                ← Volver a Fechas
                            </a>
                            <a href="index.php?controller=reservaciones&action=cancelarCreacion"
                                class="btn btn-secondary ms-2">
                                Cancelar
                            </a>
                        </div>

                        <!-- Botón de navegación derecha -->
                        <button type="submit" id="btnSiguiente" class="btn btn-primary" <?= !$preseleccionValida ? 'disabled' : '' ?>>
                            Siguiente →
                        </button>
                    </div> <!-- Fin .card-footer -->
                <?php endif; ?>

            </form>

        </div> <!-- Fin .card -->
    </div> <!-- Fin .col -->
</div> <!-- Fin .row.justify-content-center -->


<!-- JavaScript para la selección interactiva -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formHabitaciones');
        // Verificamos si el form existe (por si no hay habitaciones)
        if (form) {
            const btnSiguiente = document.getElementById('btnSiguiente');
            const cards = document.querySelectorAll('.habitacion-card');

            form.addEventListener('change', function (e) {
                // Nos aseguramos que fue un radio button
                if (e.target.type === 'radio') {

                    // 1. Quitar la clase 'selected' de todas las tarjetas
                    cards.forEach(card => {
                        card.classList.remove('selected');
                    });

                    // 2. Añadir 'selected' a la tarjeta (label) que fue clickeada
                    const selectedCard = e.target.closest('.habitacion-card');
                    if (selectedCard) {
                        selectedCard.classList.add('selected');
                    }

                    // 3. Habilitar el botón de siguiente
                    btnSiguiente.disabled = false;
                }
            });
        }
    });
</script>
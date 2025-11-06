<?php
// --- LÓGICA DE PRESELECCIÓN ---
$paqueteBaseId = 1; // ID del paquete "Solo habitación"
$idPaquetePreseleccionado = $_SESSION['reserva_temp']['idPaquete'] ?? $paqueteBaseId;
?>

<!-- Estilos CSS para las tarjetas de paquetes -->
<style>
    .paquete-label {
        display: block;
        /* Hacemos que la etiqueta ocupe todo el ancho */
        cursor: pointer;
        border: 2px solid #e9ecef;
        transition: all 0.2s ease-in-out;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        /* Espacio entre opciones */
    }

    .paquete-label:hover {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }

    /* Estilo cuando el radio button dentro de la etiqueta está seleccionado */
    .paquete-label:has(input:checked) {
        border-color: #0d6efd;
        background-color: #f0f6ff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    }

    .paquete-label input[type="radio"] {
        margin-right: 0.5em;
    }

    .paquete-descripcion {
        font-size: 0.9rem;
        color: #6c757d;
        /* Color de texto "muted" */
        display: block;
        /* Para que aparezca en su propia línea */
        margin-left: 1.75rem;
        /* Alinear con el texto principal */
    }
</style>

<!-- ESTRUCTURA DE LAYOUT -->
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8"> <!-- Centra y limita el ancho -->

        <div class="card shadow-sm border-0">

            <div class="card-header bg-primary text-white p-3">
                <h2 class="h4 mb-0"><?php echo htmlspecialchars($page_title ?? 'Paso 4: Seleccionar Paquete'); ?></h2>
            </div>

            <form method="POST" action="index.php?controller=reservaciones&action=seleccionarPaquete" id="formPaquete">

                <div class="card-body p-4">

                    <?php if (!empty($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error_message'];
                            unset($_SESSION['error_message']); ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label fw-bold mb-3">Selecciona un paquete para la estadía:</label>

                        <?php foreach ($paquetes as $p):
                            // Comprobamos si este debe ser el paquete seleccionado
                            $isChecked = ($p['idPaquete'] == $idPaquetePreseleccionado) ? 'checked' : '';
                            ?>
                            <!-- Usamos <label> como el contenedor clickeable que se estilizará como una tarjeta. -->
                            <label class="paquete-label" for="paquete<?= $p['idPaquete'] ?>">
                                <input class="form-check-input" type="radio" name="idPaquete" value="<?= $p['idPaquete'] ?>"
                                    id="paquete<?= $p['idPaquete'] ?>" <?= $isChecked ?> required>

                                <strong><?= htmlspecialchars($p['NombrePaquete']) ?></strong>
                                <span class"text-success">(+$<?= number_format($p['TarifaPaquete'], 2) ?> / noche)</span>

                                <small class="paquete-descripcion">
                                    <?= htmlspecialchars($p['DescripcionPaquete']) ?>
                                </small>
                            </label>
                        <?php endforeach; ?>

                    </div>

                </div> <!-- Fin .card-body -->

                <!-- FOOTER PARA BOTONES -->
                <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3">
                    <div>
                        <!-- Botones de navegación izquierda -->
                        <a href="index.php?controller=reservaciones&action=asignarCliente"
                            class="btn btn-outline-secondary">
                            ← Volver a Cliente
                        </a>
                        <a href="index.php?controller=reservaciones&action=cancelarCreacion"
                            class="btn btn-secondary ms-2">
                            Cancelar
                        </a>
                    </div>

                    <!-- Botón de navegación derecha -->
                    <button type="submit" id="btnSiguiente" class="btn btn-primary">
                        Siguiente: Confirmar →
                    </button>
                </div> <!-- Fin .card-footer -->

            </form>

        </div> <!-- Fin .card -->
    </div> <!-- Fin .col -->
</div> <!-- Fin .row.justify-content-center -->
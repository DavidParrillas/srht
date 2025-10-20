<div class="page-header">
    <h1>Editar Habitación</h1>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="mb-0">Información de la Habitación</h3>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="index.php?controller=habitaciones&action=actualizar" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($habitacion->getIdHabitacion()) ?>">

            <div class="row">
                <!-- Número de Habitación (readonly) -->
                <div class="col-md-6 mb-3">
                    <label for="numero" class="form-label">Número de Habitación *</label>
                    <input type="text" class="form-control" id="numero" name="numero"
                        value="<?= htmlspecialchars($habitacion->getNumeroHabitacion()) ?>" readonly
                        style="background-color: #e9ecef; color: #6c757d; cursor: not-allowed;">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="estadoHabitacion" class="form-label">Estado de la Habitación *</label>
                    <select class="form-select" id="estadoHabitacion" name="estadoHabitacion" required>
                        <?php foreach ($estadosHabitacion as $estado): ?>
                            <option value="<?= htmlspecialchars($estado) ?>"
                                <?= $estado === $habitacion->getEstadoHabitacion() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($estado) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tipoHabitacion" class="form-label">Tipo de Habitación *</label>
                    <select class="form-select" name="tipoHabitacion" id="tipoHabitacion" required>
                        <?php foreach ($tiposHabitacion as $tipo): ?>
                            <option value="<?= htmlspecialchars($tipo->getId()) ?>"
                                data-precio="<?= htmlspecialchars($tipo->getPrecio()) ?>"
                                data-capacidad="<?= htmlspecialchars($tipo->getCapacidad()) ?>"
                                <?= $habitacion->getTipoHabitacion()->getId() === $tipo->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipo->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="precio" class="form-label">Precio por Noche ($) *</label>
                    <input type="number" class="form-control" id="precio" name="precio"
                        value="<?= htmlspecialchars($habitacion->getTipoHabitacion()->getPrecio()) ?>"
                        min="0" step="0.01" readonly required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="capacidad" class="form-label">Capacidad (personas) *</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad"
                        value="<?= htmlspecialchars($habitacion->getTipoHabitacion()->getCapacidad()) ?>"
                        min="1" readonly required>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="detalleHabitacion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="detalleHabitacion" name="detalleHabitacion" rows="4"><?= htmlspecialchars($habitacion->getDetalleHabitacion()) ?></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Amenidades</label>
                    <div class="row">
                        <?php foreach ($amenidades as $amenidad): ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        id="amenidad_<?= htmlspecialchars($amenidad['idAmenidad']) ?>"
                                        name="amenidades[]"
                                        value="<?= htmlspecialchars($amenidad['idAmenidad']) ?>"
                                        <?= in_array($amenidad['idAmenidad'], $habitacion->getAmenidadesIds()) ? 'checked' : '' ?>>
                                    <label class="form-check-label"
                                        for="amenidad_<?= htmlspecialchars($amenidad['idAmenidad']) ?>">
                                        <?= htmlspecialchars($amenidad['nombreAmenidad']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Habitación
                </button>
                <a href="index.php?controller=habitaciones" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

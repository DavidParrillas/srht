<div class="page-header">
    <h1>Nueva Habitación</h1>
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
        <form action="index.php?controller=habitaciones&action=crear" method="POST" data-validate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="numero" class="form-label">Número de Habitación *</label>
                    <input type="text" class="form-control" id="numero" name="numero" required>
                    <div id="numeroError" class="text-danger mt-1" style="display:none;"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="estadoHabitacion" class="form-label">Estado de la Habitación *</label>
                    <select class="form-select" id="estadoHabitacion" name="estadoHabitacion" required>
                        <option value="">Seleccione...</option>
                        <option value="Disponible">Disponible</option>
                        <option value="Ocupada">Ocupada</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                        <option value="Fuera de Servicio">Fuera de Servicio</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tipoHabitacion" class="form-label">Tipo de Habitación *</label>
                    <select class="form-select" name="tipoHabitacion" id="tipoHabitacion" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($tiposHabitacion as $tipo): ?>
                            <option value="<?= htmlspecialchars($tipo->getId()) ?>"
                                data-precio="<?= htmlspecialchars($tipo->getPrecio()) ?>"
                                data-capacidad="<?= htmlspecialchars($tipo->getCapacidad()) ?>">
                                <?= htmlspecialchars($tipo->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="precio" class="form-label">Precio por Noche ($) *</label>
                    <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" readonly
                        required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="capacidad" class="form-label">Capacidad (personas) *</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" min="1" readonly required>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="detalleHabitacion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="detalleHabitacion" name="detalleHabitacion" rows="4"></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Amenidades (Puede seleccionar mas de una)</label>
                    <div class="row">
                        <?php if (!empty($amenidades)): ?>
                            <?php foreach ($amenidades as $amenidad): ?>
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            id="amenidad_<?= htmlspecialchars($amenidad['idAmenidad']) ?>" name="amenidades[]"
                                            value="<?= htmlspecialchars($amenidad['idAmenidad']) ?>">
                                        <label class="form-check-label"
                                            for="amenidad_<?= htmlspecialchars($amenidad['idAmenidad']) ?>">
                                            <?= htmlspecialchars($amenidad['nombreAmenidad']) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay amenidades registradas en el sistema.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Habitación
                </button>
                <a href="index.php?controller=habitaciones" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
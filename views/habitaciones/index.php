<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Habitaciones'); ?></h1>
    <a href="index.php?controller=habitaciones&action=crear" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Habitación
    </a>
</div>

<!-- Filtros con Bootstrap -->
<div class="card mb-4">
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" id="buscarHabitacion" placeholder="Buscar habitación...">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="tipoHabitacion" id="tipoHabitacion">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($tiposHabitacion as $tipo): ?>
                        <option value="<?= htmlspecialchars($tipo->getId()) ?>">
                            <?= htmlspecialchars($tipo->getNombre()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="estadoHabitacion" id="estadoHabitacion">
                    <option value="">Todos los estados</option>
                    <?php if (!empty($estadosHabitacion)): ?>
                        <?php foreach ($estadosHabitacion as $estado): ?>
                            <option value="<?= htmlspecialchars($estado) ?>">
                                <?= htmlspecialchars($estado) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No hay estados registrados</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Tabla Responsive -->
<div class="table-responsive">
    <table class="table tabla-habitaciones">
        <thead>
            <tr>
                <th>Número</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Precio/Noche</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($habitaciones)): ?>
                <?php foreach ($habitaciones as $habitacion): ?>
                    <tr>
                        <td><?= htmlspecialchars($habitacion->getNumeroHabitacion()) ?></td>
                        <td><?= htmlspecialchars($habitacion->getTipoHabitacion()->getNombre()) ?></td>
                        <td>
                            <?php
                            $estado = strtolower($habitacion->getEstadoHabitacion());
                            $badgeClass = match ($estado) {
                                'disponible' => 'bg-success',
                                'ocupada', 'ocupado' => 'bg-danger',
                                'mantenimiento' => 'bg-warning text-dark',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($habitacion->getEstadoHabitacion()) ?>
                            </span>
                        </td>
                        <td>$<?= number_format($habitacion->getTipoHabitacion()->getPrecio(), 2) ?></td>
                        <td class="text-center">
                            <div class="action-buttons d-flex justify-content-center gap-2">
                                <a href="index.php?controller=habitaciones&action=editar&id=<?= $habitacion->getIdHabitacion() ?>"
                                    class="btn-action btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="index.php?controller=habitaciones&action=eliminar&id=<?= $habitacion->getIdHabitacion() ?>"
                                    class="btn-action btn-delete" title="Eliminar"
                                    onclick="return confirm('¿Está seguro de que desea eliminar la habitación #<?= $habitacion->getNumeroHabitacion() ?>?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        <i class="fas fa-bed me-2"></i> No hay habitaciones registradas.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
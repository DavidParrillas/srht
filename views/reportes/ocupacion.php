<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Reporte'); ?></h1>
    <p>Mostrando datos desde <strong><?php echo htmlspecialchars($fechaInicio); ?></strong> hasta <strong><?php echo htmlspecialchars($fechaFin); ?></strong>.</p>
</div>

<!-- Formulario de Filtro de Fechas -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title"><i class="fas fa-filter"></i> Filtrar por Fechas</h5>
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="reportes">
            <input type="hidden" name="action" value="generar">
            <input type="hidden" name="tipo" value="ocupacion">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fechaInicio); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fechaFin); ?>" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Generar Reporte
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Resultados -->
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>Tipo de Habitación</th>
                <th>Número de Reservas</th>
                <th>Noches Vendidas</th>
                <th>Tarifa Promedio (ADR)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($datos)): ?>
                <tr>
                    <td colspan="4" class="text-center">No se encontraron datos de ocupación para el período seleccionado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($datos as $fila): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['NombreTipoHabitacion']); ?></td>
                        <td><?php echo htmlspecialchars($fila['NumeroReservas']); ?></td>
                        <td><?php echo htmlspecialchars($fila['NochesVendidas'] ?? 0); ?></td>
                        <td>$<?php echo number_format($fila['TarifaPromedio'] ?? 0, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="index.php?controller=reportes&action=index" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver a Reportes</a>
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
            <input type="hidden" name="tipo" value="historial">
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
    <table class="table table-hover table-striped table-sm">
        <thead class="table-dark">
            <tr>
                <th>ID Reserva</th>
                <th>Cliente</th>
                <th>Habitación</th>
                <th>Paquete</th>
                <th>Fecha Entrada</th>
                <th>Fecha Salida</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($datos)): ?>
                <tr>
                    <td colspan="8" class="text-center">No se encontraron reservaciones para el período seleccionado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($datos as $fila): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['idReserva']); ?></td>
                        <td><?php echo htmlspecialchars($fila['NombreCliente']); ?></td>
                        <td><?php echo htmlspecialchars($fila['NumeroHabitacion']); ?></td>
                        <td><?php echo htmlspecialchars($fila['NombrePaquete'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($fila['FechaEntrada']))); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($fila['FechaSalida']))); ?></td>
                        <td>$<?php echo number_format($fila['TotalReservacion'], 2); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($fila['EstadoReserva']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="index.php?controller=reportes&action=index" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver a Reportes</a>
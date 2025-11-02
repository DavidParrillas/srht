<h2><?= isset($reservaOriginalDatos) ? 'Modificar Reserva' : 'Confirmar Reserva'; ?></h2>

<div class="row">
    
    <?php if (isset($reservaOriginalDatos)): ?>
    <div class="col-md-6 mb-3">
        <h4>Reserva Original #<?= $reservaOriginalDatos['idReserva'] ?></h4>
        <ul class="list-group list-group-flush border border-warning">
            <li class="list-group-item bg-light"><strong>Cliente:</strong> <?= htmlspecialchars($reservaOriginalDatos['NombreCliente']) ?></li>
            <li class="list-group-item bg-light"><strong>Habitación:</strong> <?= htmlspecialchars($reservaOriginalDatos['NombreTipoHabitacion']) ?> (<?= htmlspecialchars($reservaOriginalDatos['NumeroHabitacion']) ?>)</li>
            <li class="list-group-item bg-light"><strong>Fechas:</strong> <?= htmlspecialchars($reservaOriginalDatos['FechaEntrada']) ?> → <?= htmlspecialchars($reservaOriginalDatos['FechaSalida']) ?></li>
            <li class="list-group-item bg-light"><strong>Total Original:</strong> $<?= number_format($reservaOriginalDatos['TotalReservacion'], 2) ?></li>
        </ul>
    </div>
    <?php endif; ?>

    <div class="<?= isset($reservaOriginalDatos) ? 'col-md-6' : 'col-md-12'; ?>">
        <h4><?= isset($reservaOriginalDatos) ? 'Nueva Propuesta de Reserva' : 'Detalles de la Reserva'; ?></h4>
        <ul class="list-group list-group-flush border border-primary">
            
            <li class="list-group-item"><strong>Cliente:</strong> 
                <?= htmlspecialchars($cliente['NombreCliente'] ?? 'Cliente no encontrado') ?>
            </li>
            
            <li class="list-group-item"><strong>Habitación:</strong> 
                <?= htmlspecialchars($habitacion['NombreTipoHabitacion'] ?? 'Tipo no encontrado') ?> 
                - 
                <?= htmlspecialchars($habitacion['NumeroHabitacion'] ?? 'N/A') ?>
            </li>
            
            <li class="list-group-item"><strong>Nuevas Fechas:</strong> 
                <?= htmlspecialchars($_SESSION['reserva_temp']['fechaEntrada']) ?> → 
                <?= htmlspecialchars($_SESSION['reserva_temp']['fechaSalida']) ?>
            </li>
            
            <?php if($paquete): ?>
                <li class="list-group-item"><strong>Paquete:</strong> 
                    <?= htmlspecialchars($paquete['NombrePaquete']) ?> 
                    (+ $<?= number_format($paquete['TarifaPaquete'], 2) ?>)
                </li>
            <?php else: ?>
                 <li class="list-group-item"><strong>Paquete:</strong> Ninguno</li>
            <?php endif; ?>
            
            <li class="list-group-item bg-success text-white">
                <strong>TOTAL FINAL:</strong> 
                $<?= number_format($total ?? 0, 2) ?>
            </li>
        </ul>
        
        <?php if(isset($reservaOriginalDatos)): ?>
            <p class="mt-2 text-danger">⚠️ Al confirmar, la reserva original (ID: #<?= $reservaOriginalDatos['idReserva'] ?>) será **CANCELADA**.</p>
            <?php endif; ?>
    </div>
</div>

<form method="POST" action="index.php?controller=reservaciones&action=confirmarReserva" class="mt-3">
    
    <?php if(isset($reservaOriginalDatos)): ?>
        <textarea name="comentario" class="form-control" rows="3" placeholder="Comentario adicional para la nueva reserva (opcional)"></textarea>
    <?php endif; ?>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Confirmar <?= isset($reservaOriginalDatos) ? 'Cambio' : 'Reserva'; ?>
        </button>
        
        <a href="index.php?controller=reservaciones&action=index" class="btn btn-secondary">
            <i class="fas fa-times"></i> Descartar
        </a>
    </div>
</form>
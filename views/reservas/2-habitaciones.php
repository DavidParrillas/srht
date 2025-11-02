<h2>Habitaciones Disponibles</h2>

<?php if(empty($habitaciones)): ?>
    <div class="alert alert-warning">No hay habitaciones disponibles en las fechas seleccionadas.</div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Número</th>
                <th>Tipo</th>
                <th>Tarifa por noche</th>
                <th>Estado</th>
                <th>Seleccionar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($habitaciones as $h): ?>
                <tr>
                    <td><?= $h['NumeroHabitacion'] ?></td>
                    <td><?= $h['NombreTipoHabitacion'] ?></td>
                    <td>$<?= number_format($h['PrecioTipoHabitacion'], 2) ?></td>
                    <td><?= $h['EstadoHabitacion'] ?></td>
                    <td>
                        <form method="POST" action="index.php?controller=reservaciones&action=seleccionarHabitacion">
                            <input type="hidden" name="idHabitacion" value="<?= $h['idHabitacion'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">Seleccionar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

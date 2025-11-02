<h2>Seleccionar Fechas</h2>

<?php if(!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<form method="POST" action="index.php?controller=reservaciones&action=crear">
    <div class="form-group">
        <label for="fechaEntrada">Fecha de Entrada:</label>
        <input type="date" name="fechaEntrada" id="fechaEntrada" class="form-control" required
               value="<?= $_SESSION['reserva_temp']['fechaEntrada'] ?? '' ?>">
    </div>

    <div class="form-group">
        <label for="fechaSalida">Fecha de Salida:</label>
        <input type="date" name="fechaSalida" id="fechaSalida" class="form-control" required
               value="<?= $_SESSION['reserva_temp']['fechaSalida'] ?? '' ?>">
    </div>

    <button type="submit" class="btn btn-primary mt-2">Siguiente →</button>
</form>
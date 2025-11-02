// views/reservas/4-paquete.php

<h2>Seleccionar Paquete (Opcional)</h2>

<form method="POST" action="index.php?controller=reservaciones&action=seleccionarPaquete">
    <div class="form-group">
        <?php 
        $paqueteBaseId = 1;
        
        foreach($paquetes as $p): 
            $isChecked = '';
            // Si no se ha seleccionado nada, seleccionamos el paquete base por defecto (el 1)
            if (!isset($_SESSION['reserva_temp']['idPaquete']) && $p['idPaquete'] == $paqueteBaseId) {
                 $isChecked = 'checked';
            }
            // Si ya hay un paquete seleccionado en la sesión, lo marcamos.
            if (isset($_SESSION['reserva_temp']['idPaquete']) && $p['idPaquete'] == $_SESSION['reserva_temp']['idPaquete']) {
                 $isChecked = 'checked';
            }
        ?>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idPaquete" 
                        value="<?= $p['idPaquete'] ?>" id="paquete<?= $p['idPaquete'] ?>" <?= $isChecked ?>>
                <label class="form-check-label" for="paquete<?= $p['idPaquete'] ?>">
                    <?= htmlspecialchars($p['NombrePaquete']) ?> - <?= htmlspecialchars($p['DescripcionPaquete']) ?> 
                    (Tarifa: $<?= number_format($p['TarifaPaquete'], 2) ?>)
                </label>
            </div>
        <?php endforeach; ?>
        
        </div>

    <button type="submit" class="btn btn-success mt-2">Siguiente →</button>
</form>
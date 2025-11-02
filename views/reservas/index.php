<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Paquetes y Tarifas'); ?></h1>
    <a href="index.php?controller=reservaciones&action=crear" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Reserva
    </a>
</div>

<?php
// Mostrar mensaje de éxito si existe
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo $_SESSION['success_message'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    // Eliminar el mensaje para que no se muestre de nuevo
    unset($_SESSION['success_message']);
}

// Mostrar mensaje de error si existe 
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo $_SESSION['error_message'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    // Eliminar el mensaje
    unset($_SESSION['error_message']);
}
?>

<div class="table-responsive">
    <table class="table data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Habitación</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            if (!empty($reservas)) {
                // Iteramos sobre cada fila de resultados
                foreach ($reservas as $row) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['idReserva']); ?></td>

                        <td><?php echo htmlspecialchars($row['NombreCliente']); ?></td>

                        <td>
                            <?php
                            echo htmlspecialchars($row['NombreTipoHabitacion']) .
                                " (" . htmlspecialchars($row['NumeroHabitacion']) . ")";
                            ?>
                        </td>

                        <td><?php echo htmlspecialchars($row['FechaEntrada']); ?></td>

                        <td><?php echo htmlspecialchars($row['FechaSalida']); ?></td>

                        <td><?php
                        $estado = $row['EstadoReserva'];
                        $badgeClass = ''; // Clase por defecto
                
                        switch ($estado) {
                            case 'Confirmada':
                                $badgeClass = 'badge bg-success'; 
                                break;
                            case 'Cancelada':
                                $badgeClass = 'badge bg-danger';  
                                break;
                            case 'Pendiente':
                                $badgeClass = 'badge bg-warning text-dark'; 
                                break;
                            case 'CheckIn':
                                $badgeClass = 'badge bg-info text-dark';
                                break;
                            default:
                                $badgeClass = 'badge bg-secondary'; 
                        }
                        ?>
                            <span class="<?php echo $badgeClass; ?>">
                                <?php echo htmlspecialchars($estado); ?>
                            </span>
                        </td>

                        <td style="white-space: nowrap;">

                            <?php if ($row['EstadoReserva'] == 'Confirmada'): ?>

                                <a href="index.php?controller=reservaciones&action=edit&id=<?php echo $row['idReserva']; ?>"
                                    class="btn btn-sm btn-primary me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                    Cambiar
                                </a>

                                <a href="index.php?controller=reservaciones&action=cancelar&id=<?php echo $row['idReserva']; ?>"
                                    class="btn btn-sm btn-primary"
                                    onclick="return confirm('¿Está seguro de CANCELAR esta reservación?');" title="Cancelar">
                                    Cancelar
                                </a>

                            <?php else: ?>

                                <a href="#" class="btn btn-sm btn-secondary disabled me-2" title="No disponible"
                                    aria-disabled="true">
                                    <i class="fas fa-edit"></i>
                                    Cambiar
                                </a>

                                <a href="#" class="btn btn-sm btn-secondary disabled" title="No disponible" aria-disabled="true">
                                    Cancelar
                                </a>

                            <?php endif; ?>

                        </td>
                    </tr>
                    <?php
                } // Fin del while
            } else {
                // Mensaje si no hay reservaciones
                ?>
                <tr>
                    <td colspan="7" class="text-center">No se encontraron reservaciones.</td>
                </tr>
                <?php
            } // Fin del if
            ?>
        </tbody>
    </table>
</div>
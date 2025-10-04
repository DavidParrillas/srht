<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Reservaciones'); ?></h1>
    <a href="index.php?controller=reservaciones&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nueva Reservación
    </a>
</div>

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
            <?php /* } */ ?>
        </tbody>
    </table>
</div>
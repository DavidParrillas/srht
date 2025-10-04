<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Usuarios'); ?></h1>
    <a href="index.php?controller=usuarios&action=create" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Agregar Usuario
    </a>
</div>

<div class="table-responsive">
    <table class="table data-table">
        <thead>
            <tr>
                <th>Nombre de Usuario</th>
                <th>Correo Electrónico</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td colspan="5" class="text-center">No hay usuarios para mostrar. La lógica para cargarlos desde la base de datos aún no está implementada.</td>
            </tr>
            <?php /* } */ ?>
        </tbody>
    </table>
</div>
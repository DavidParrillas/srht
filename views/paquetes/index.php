<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Paquetes y Tarifas'); ?></h1>
    <a href="index.php?controller=paquetes&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Paquete
    </a>
</div>

<div class="table-responsive">
    <table class="table data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" class="text-center">No hay paquetes para mostrar. La lógica para cargarlos desde la base de datos aún no está implementada.</td>
            </tr>
        </tbody>
    </table>
</div>
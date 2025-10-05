<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Planes de Pago'); ?></h1>
    <a href="index.php?controller=planes_pago&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Plan de Pago
    </a>
</div>

<div class="table-responsive">
    <table class="table data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Número de Cuotas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>
<div class="page-content">
    <div class="page-header">
        <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Clientes'); ?></h1>
        <div class="page-actions">
            <a href="index.php?controller=clientes&action=crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>DUI</th>
                    <th>Correo Electrónico</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay clientes para mostrar.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td data-label="Nombre"><?php echo htmlspecialchars($cliente['NombreCliente'] ?? ''); ?></td>
                            <td data-label="DUI"><?php echo htmlspecialchars($cliente['DuiCliente'] ?? ''); ?></td>
                            <td data-label="Correo"><?php echo htmlspecialchars($cliente['CorreoCliente'] ?? ''); ?></td>
                            <td data-label="Teléfono">
                                <?php 
                                $telefono = $cliente['TelefonoCliente'] ?? '';
                                echo $telefono ? htmlspecialchars($telefono) : '<em class="text-muted">No registrado</em>';
                                ?>
                            </td>
                            <td data-label="Acciones">
                                <div class="action-buttons">
                                    <a href="index.php?controller=clientes&action=editar&id=<?php echo $cliente['idCliente']; ?>" class="btn-action btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?controller=clientes&action=eliminar&id=<?php echo $cliente['idCliente']; ?>" class="btn-action btn-delete" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar este cliente? Esta acción no se puede deshacer si el cliente no tiene reservas asociadas.');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
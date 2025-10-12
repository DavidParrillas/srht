<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Usuarios'); ?></h1>
    <a href="index.php?controller=usuarios&action=crear" class="btn btn-primary">
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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="4" class="text-center">No hay usuarios para mostrar.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td data-label="Usuario"><?php echo htmlspecialchars($usuario['NombreUsuario'] ?? ''); ?></td>
                        <td data-label="Correo"><?php echo htmlspecialchars($usuario['CorreoUsuario'] ?? ''); ?></td>
                        <td data-label="Rol">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($usuario['NombreRol'] ?? ''); ?></span>
                        </td>
                        <td data-label="Acciones">
                            <div class="action-buttons">
                                <a href="index.php?controller=usuarios&action=editar&id=<?php echo $usuario['idUsuario']; ?>" class="btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="index.php?controller=usuarios&action=eliminar&id=<?php echo $usuario['idUsuario']; ?>" class="btn-action btn-delete" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar a este usuario?');"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
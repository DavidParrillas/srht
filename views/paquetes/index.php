<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Paquetes y Tarifas'); ?></h1>
    <a href="index.php?controller=paquetes&action=crear" class="btn btn-primary">
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
            <?php if (!empty($paquetes)): ?>
                <?php foreach ($paquetes as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['NombrePaquete']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($p['DescripcionPaquete'])); ?></td>
                        <td><?php echo number_format((float)$p['TarifaPaquete'], 2); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?controller=paquetes&action=editar&id=<?php echo (int)$p['idPaquete']; ?>"
                                   class="btn-action btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?controller=paquetes&action=eliminar&id=<?php echo (int)$p['idPaquete']; ?>"
                                   class="btn-action btn-delete" title="Eliminar"
                                   onclick="return confirm('¿Eliminar paquete? Esta acción no se puede deshacer.');">
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
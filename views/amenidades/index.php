<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Amenidades'); ?></h1>
    <a href="index.php?controller=amenidades&action=crear" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Amenidad
    </a>
</div>

<div class="table-responsive">
    <table class="table data-table">
        <thead>
            <tr>
                <th>Nombre de Amenidad</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($amenidades)): ?>
                <tr>
                    <td colspan="3" class="text-center">No hay amenidades para mostrar.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($amenidades as $amenidad): ?>
                    <tr>
                        <td data-label="Amenidad"><?php echo htmlspecialchars($amenidad['nombreAmenidad'] ?? ''); ?></td>
                        <td data-label="Descripción">
                            <?php 
                            $descripcion = $amenidad['Descripcion'] ?? '';
                            echo $descripcion ? htmlspecialchars($descripcion) : '<em class="text-muted">Sin descripción</em>';
                            ?>
                        </td>
                        <td data-label="Acciones">
                            <div class="action-buttons">
                                <a href="index.php?controller=amenidades&action=editar&id=<?php echo $amenidad['idAmenidad']; ?>" class="btn-action btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?controller=amenidades&action=eliminar&id=<?php echo $amenidad['idAmenidad']; ?>" class="btn-action btn-delete" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar esta amenidad? Esta acción eliminará todas sus asignaciones a habitaciones.');">
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
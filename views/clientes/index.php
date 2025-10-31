<div class="page-content">
    <div class="page-header">
        <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Clientes'); ?></h1>
        <div class="page-actions">
            <a href="index.php?controller=clientes&action=crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <?php
    // Obtener valores de filtros
    $filterNombre = htmlspecialchars($_GET['nombre'] ?? '');
    $filterDui = htmlspecialchars($_GET['dui'] ?? '');
    $filterCorreo = htmlspecialchars($_GET['correo'] ?? '');
    ?>

    <form method="get" action="index.php" class="row g-3 mb-4">
        <input type="hidden" name="controller" value="clientes">
        <input type="hidden" name="action" value="index">
        
        <div class="col-md-3">
            <label for="filter-nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="filter-nombre" name="nombre" 
                   value="<?php echo $filterNombre; ?>" placeholder="Buscar por nombre">
        </div>
        
        <div class="col-md-3">
            <label for="filter-dui" class="form-label">DUI</label>
            <input type="text" class="form-control" id="filter-dui" name="dui" 
                   value="<?php echo $filterDui; ?>" placeholder="Buscar por DUI">
        </div>
        
        <div class="col-md-3">
            <label for="filter-correo" class="form-label">Correo</label>
            <input type="text" class="form-control" id="filter-correo" name="correo" 
                   value="<?php echo $filterCorreo; ?>" placeholder="Buscar por correo">
        </div>
        
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary me-2">Filtrar</button>
            <a href="index.php?controller=clientes&action=index" class="btn btn-light">Limpiar</a>
        </div>
    </form>

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
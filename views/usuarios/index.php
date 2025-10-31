<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Usuarios'); ?></h1>
    <a href="index.php?controller=usuarios&action=crear" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Agregar Usuario
    </a>
</div>

<!-- Filtros con Bootstrap -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="controller" value="usuarios">
            <input type="hidden" name="action" value="index">
            <?php
            $filterNombre = htmlspecialchars($_GET['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
            $filterRol = $_GET['rol'] ?? '';
            ?>
            <div class="col-md-4">
                <label for="filter-nombre" class="form-label">Nombre de Usuario</label>
                <input id="filter-nombre" type="text" name="nombre" value="<?php echo $filterNombre; ?>" class="form-control" placeholder="Buscar por nombre...">
            </div>
            <div class="col-md-4">
                <label for="filter-rol" class="form-label">Rol</label>
                <select id="filter-rol" name="rol" class="form-select">
                    <option value="">Todos los roles</option>
                    <?php if (!empty($roles) && is_array($roles)): ?>
                        <?php foreach ($roles as $rol): ?>
                            <?php
                            $rolId = $rol['idRol'] ?? '';
                            $rolNombre = $rol['NombreRol'] ?? '';
                            ?>
                            <option value="<?php echo htmlspecialchars($rolId, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($filterRol !== '' && (string)$filterRol === (string)$rolId) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rolNombre); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary me-2">Filtrar</button>
                <a href="index.php?controller=usuarios&action=index" class="btn btn-light">Limpiar</a>
            </div>
        </form>
    </div>
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
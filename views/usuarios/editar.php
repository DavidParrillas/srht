<div class="container mt-4">
    <h2>Editar Usuario</h2>
 
    <form action="index.php?controller=usuarios&action=actualizar" method="POST">
        <!-- Token CSRF para seguridad -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <!-- ID del usuario a editar -->
        <input type="hidden" name="idUsuario" value="<?php echo htmlspecialchars($usuario['idUsuario']); ?>">
 
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['NombreUsuario'] ?? ''); ?>" required>
        </div>
 
        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['CorreoUsuario'] ?? ''); ?>" required>
        </div>
 
        <div class="mb-3">
            <label for="contrasena" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Dejar en blanco para no cambiar">
            <small class="form-text text-muted">Si no desea cambiar la contraseña, deje este campo vacío.</small>
        </div>
 
        <div class="mb-3">
            <label for="id_rol" class="form-label">Rol</label>
            <select class="form-select" id="id_rol" name="id_rol" required>
                <option value="">Seleccione un rol...</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?php echo htmlspecialchars($rol['idRol']); ?>" <?php echo ($rol['idRol'] == ($usuario['idRol'] ?? null)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($rol['NombreRol']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        <a href="index.php?controller=usuarios&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

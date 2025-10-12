<div class="container mt-4">
    <h2>Crear Nuevo Usuario</h2>
    <p>Crea un nuevo usuario para acceder al sistema.</p>
 
    <form action="index.php?controller=usuarios&action=guardar" method="POST">
        <!-- Token CSRF para seguridad -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
 
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
 
        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
 
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>
 
        <div class="mb-3">
            <label for="id_rol" class="form-label">Rol</label>
            <select class="form-select" id="id_rol" name="id_rol" required>
                <option value="">Seleccione un rol...</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?php echo htmlspecialchars($rol['idRol']); ?>"><?php echo htmlspecialchars($rol['NombreRol']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        <a href="index.php?controller=usuarios&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

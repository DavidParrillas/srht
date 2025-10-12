<div class="container mt-4">
    <h2>Crear Nuevo Usuario</h2>
    <p>Crea un nuevo usuario para acceder al sistema.</p>

    <form action="index.php?controller=usuarios&action=guardar" method="POST">
        <div class="mb-3">
            <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
        </div>

        <div class="mb-3">
            <label for="correo_usuario" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo_usuario" name="correo_usuario" required>
        </div>

        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>

        <div class="mb-3">
            <label for="id_rol" class="form-label">Rol</label>
            <select class="form-select" id="id_rol" name="id_rol" required>
                <!-- Aquí se deberían cargar los roles desde la base de datos -->
                <option value="1">Recepción</option>
                <option value="2">Gerencia</option>
                <option value="3">Subgerencia</option>
                <option value="4">Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        <a href="index.php?controller=usuarios&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

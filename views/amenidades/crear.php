<div class="container mt-4">
    <h2>Crear Nueva Amenidad</h2>
    <p>Agregue una nueva amenidad que podrá asignarse a las habitaciones del hotel.</p>
 
    <form action="index.php?controller=amenidades&action=guardar" method="POST">
        <!-- Token CSRF para seguridad -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
 
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Amenidad <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100" placeholder="Ej: Vista al Mar, Jacuzzi, Balcón">
            <small class="form-text text-muted">Ingrese un nombre descriptivo para la amenidad (mínimo 3 caracteres)</small>
        </div>
 
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" maxlength="255" placeholder="Describa las características de esta amenidad (opcional)"></textarea>
            <small class="form-text text-muted">Proporcione detalles adicionales sobre la amenidad</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Amenidad
            </button>
            <a href="index.php?controller=amenidades&action=index" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>
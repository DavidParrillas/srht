<div class="container mt-4">
    <h2>Crear Paquete</h2>
    <p>Define un nuevo paquete con su tarifa.</p>

    <form action="index.php?controller=paquetes&action=guardar" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Paquete <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100"
                   placeholder="Ej: Todo Incluido, Media Pensión">
            <small class="form-text text-muted">Mínimo 3 caracteres.</small>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                      placeholder="Detalles del paquete..."></textarea>
        </div>

        <div class="mb-3">
            <label for="tarifa" class="form-label">Tarifa (USD) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" class="form-control" id="tarifa" name="tarifa" required
                   placeholder="0.00">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Paquete
            </button>
            <a href="index.php?controller=paquetes&action=index" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>
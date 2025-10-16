<div class="container mt-4">
    <h2>Crear Nuevo Cliente</h2>
    <p>Registre un nuevo cliente/huésped en el sistema del hotel.</p>

    <form action="index.php?controller=clientes&action=guardar" method="POST">
        <!-- Token CSRF para seguridad -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-3">
            <label for="dui" class="form-label">DUI <span class="text-danger">*</span></label>
            <input type="text" 
                class="form-control" 
                id="dui" 
                name="dui" 
                required 
                maxlength="10" 
                pattern="[0-9]{8}-[0-9]"
                placeholder="12345678-9"
                title="Formato: 12345678-9 (8 dígitos, guion, 1 dígito)">
            <small class="form-text text-muted">Formato: 12345678-9 (Documento Único de Identidad)</small>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
            <input type="text" 
                class="form-control" 
                id="nombre" 
                name="nombre" 
                required 
                maxlength="150" 
                placeholder="Ingrese el nombre completo del cliente">
            <small class="form-text text-muted">Nombre completo del huésped (mínimo 3 caracteres)</small>
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
            <input type="email" 
                class="form-control" 
                id="correo" 
                name="correo" 
                required 
                maxlength="100" 
                placeholder="ejemplo@correo.com">
            <small class="form-text text-muted">Correo para notificaciones y confirmaciones de reserva</small>
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" 
                class="form-control" 
                id="telefono" 
                name="telefono" 
                maxlength="15" 
                pattern="[0-9]{8,15}"
                placeholder="12345678">
            <small class="form-text text-muted">Número de teléfono de contacto (opcional, solo números)</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Cliente
            </button>
            <a href="index.php?controller=clientes&action=index" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>
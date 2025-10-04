<div class="page-header">
    <h1>Nueva Habitación</h1>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="mb-0">Información de la Habitación</h3>
    </div>
    <div class="card-body">
        <form action="index.php?controller=habitaciones&action=save" method="POST" data-validate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="numero" class="form-label">Número de Habitación *</label>
                    <input type="text" class="form-control" id="numero" name="numero" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="tipo" class="form-label">Tipo de Habitación *</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="">Seleccione...</option>
                        <option value="simple">Simple</option>
                        <option value="doble">Doble</option>
                        <option value="suite">Suite</option>
                        <option value="presidencial">Presidencial</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="precio" class="form-label">Precio por Noche (Q) *</label>
                    <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="capacidad" class="form-label">Capacidad (personas) *</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" min="1" required>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4"></textarea>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Amenidades</label>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="wifi" name="amenidades[]" value="wifi">
                                <label class="form-check-label" for="wifi">
                                    <i class="fas fa-wifi"></i> WiFi
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tv" name="amenidades[]" value="tv">
                                <label class="form-check-label" for="tv">
                                    <i class="fas fa-tv"></i> TV
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ac" name="amenidades[]" value="ac">
                                <label class="form-check-label" for="ac">
                                    <i class="fas fa-snowflake"></i> A/C
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="minibar" name="amenidades[]" value="minibar">
                                <label class="form-check-label" for="minibar">
                                    <i class="fas fa-glass-martini"></i> Minibar
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Habitación
                </button>
                <a href="index.php?controller=habitaciones" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
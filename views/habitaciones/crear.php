<div class="page-content">
    <div class="page-header">
        <h1>Nueva Habitación</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?controller=habitaciones&action=crear" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero">Número de Habitación</label>
                            <input type="text" class="form-control" id="numero" name="numero" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo">Tipo de Habitación</label>
                            <select class="form-control" id="tipo" name="tipo" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="individual">Individual</option>
                                <option value="doble">Doble</option>
                                <option value="suite">Suite</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="precio">Precio por Noche</label>
                            <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="disponible">Disponible</option>
                                <option value="mantenimiento">Mantenimiento</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Amenidades</label>
                    <div class="checkbox-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="amenidad_wifi" name="amenidades[]" value="wifi">
                            <label class="custom-control-label" for="amenidad_wifi">WiFi</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="amenidad_tv" name="amenidades[]" value="tv">
                            <label class="custom-control-label" for="amenidad_tv">TV</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="amenidad_aire" name="amenidades[]" value="aire">
                            <label class="custom-control-label" for="amenidad_aire">Aire Acondicionado</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="amenidad_vista" name="amenidades[]" value="vista">
                            <label class="custom-control-label" for="amenidad_vista">Vista al Mar</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar Habitación</button>
                    <a href="index.php?controller=habitaciones" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

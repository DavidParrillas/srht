<div class="page-content">
    <div class="page-header">
        <h1>Gestión de Habitaciones</h1>
        <div class="page-actions">
            <a href="index.php?controller=habitaciones&action=crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Habitación
            </a>
        </div>
    </div>

    <div class="filters-section">
        <div class="row">
            <div class="col">
                <input type="text" class="form-control" placeholder="Buscar por número...">
            </div>
            <div class="col">
                <select class="form-control">
                    <option value="">Todos los estados</option>
                    <option value="disponible">Disponible</option>
                    <option value="ocupada">Ocupada</option>
                    <option value="mantenimiento">Mantenimiento</option>
                </select>
            </div>
            <div class="col">
                <select class="form-control">
                    <option value="">Todos los tipos</option>
                    <option value="individual">Individual</option>
                    <option value="doble">Doble</option>
                    <option value="suite">Suite</option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Amenidades</th>
                    <th>Precio/Noche</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán dinámicamente -->
            </tbody>
        </table>
    </div>
</div>


<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Gestión de Habitaciones'); ?></h1>
    <a href="index.php?controller=habitaciones&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Habitación
    </a>
</div>

<!-- Filtros con Bootstrap -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Buscar habitación..." data-table-search="habitaciones-table">
            </div>
            <div class="col-md-4">
                <select class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="simple">Simple</option>
                    <option value="doble">Doble</option>
                    <option value="suite">Suite</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="disponible">Disponible</option>
                    <option value="ocupado">Ocupado</option>
                    <option value="mantenimiento">Mantenimiento</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Tabla Responsive -->
<div class="table-responsive">
    <table class="table table-hover" id="habitaciones-table">
        <thead>
            <tr>
                <th>Número</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Precio/Noche</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>


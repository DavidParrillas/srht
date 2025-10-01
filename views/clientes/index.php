<div class="page-content">
    <div class="page-header">
        <h1>Gestión de Clientes</h1>
        <div class="page-actions">
            <a href="index.php?controller=clientes&action=crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <div class="filters-section">
        <div class="row">
            <div class="col">
                <input type="text" class="form-control" placeholder="Buscar por nombre o DUI...">
            </div>
            <div class="col">
                <select class="form-control">
                    <option value="">Estado de Pago</option>
                    <option value="al_dia">Al día</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="mora">En mora</option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>DUI/Pasaporte</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán dinámicamente -->
            </tbody>
        </table>
    </div>
</div>

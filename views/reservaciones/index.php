<div class="page-content">
    <div class="page-header">
        <h1>Gestión de Reservaciones</h1>
        <div class="page-actions">
            <a href="index.php?controller=reservaciones&action=crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Reservación
            </a>
        </div>
    </div>

    <div class="filters-section">
        <div class="row">
            <div class="col">
                <input type="text" class="form-control" placeholder="Buscar por cliente...">
            </div>
            <div class="col">
                <select class="form-control">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="check_in">Check-in</option>
                    <option value="check_out">Check-out</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            <div class="col">
                <input type="date" class="form-control" placeholder="Fecha desde">
            </div>
            <div class="col">
                <input type="date" class="form-control" placeholder="Fecha hasta">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Habitación</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán dinámicamente -->
            </tbody>
        </table>
    </div>
</div>

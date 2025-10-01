<div class="page-content">
    <div class="page-header">
        <h1>Nueva Reservación</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?controller=reservaciones&action=crear" method="POST">
                <!-- Información del Cliente -->
                <div class="section-title">
                    <h3>Información del Cliente</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cliente">Cliente</label>
                            <select class="form-control" id="cliente" name="cliente_id" required>
                                <option value="">Seleccione un cliente</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a href="index.php?controller=clientes&action=crear" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Nuevo Cliente
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Información de la Reserva -->
                <div class="section-title">
                    <h3>Detalles de la Reservación</h3>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="check_in">Fecha de Check-in</label>
                            <input type="date" class="form-control" id="check_in" name="check_in" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="check_out">Fecha de Check-out</label>
                            <input type="date" class="form-control" id="check_out" name="check_out" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="habitacion">Habitación</label>
                            <select class="form-control" id="habitacion" name="habitacion_id" required>
                                <option value="">Seleccione una habitación</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="paquete">Paquete</label>
                            <select class="form-control" id="paquete" name="paquete_id">
                                <option value="">Seleccione un paquete (opcional)</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Información de Pago -->
                <div class="section-title">
                    <h3>Información de Pago</h3>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="plan_pago">Plan de Pago</label>
                            <select class="form-control" id="plan_pago" name="plan_pago_id" required>
                                <option value="">Seleccione un plan de pago</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="anticipo">Anticipo</label>
                            <input type="number" class="form-control" id="anticipo" name="anticipo" step="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Crear Reservación</button>
                    <a href="index.php?controller=reservaciones" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

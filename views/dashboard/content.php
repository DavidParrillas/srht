<!-- dashboard/content.php -->
<div class="dashboard-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>
    <p>Bienvenido de nuevo, <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?>.</p>
</div>

<!-- Info Cards con Bootstrap -->
<div class="row mb-4">
    <div class="col-lg col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="info-card-label">Total de Clientes</div>
            <div class="info-card-value"><?php echo htmlspecialchars($stats['total_clientes'] ?? 0); ?></div>
        </div>
    </div>
    
    <div class="col-lg col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon secondary">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="info-card-label">Total de Usuarios</div>
            <div class="info-card-value"><?php echo htmlspecialchars($stats['total_usuarios'] ?? 0); ?></div>
        </div>
    </div>
    
    <div class="col-lg col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon warning">
                <i class="fas fa-bed"></i>
            </div>
            <div class="info-card-label">Total de Habitaciones</div>
            <div class="info-card-value"><?php echo htmlspecialchars($stats['total_habitaciones'] ?? 0); ?></div>
        </div>
    </div>
    
    <div class="col-lg col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon success">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="info-card-label">Total de Paquetes</div>
            <div class="info-card-value"><?php echo htmlspecialchars($stats['total_paquetes'] ?? 0); ?></div>
        </div>
    </div>

    <div class="col-lg col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon info">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="info-card-label">Total de Reservaciones</div>
            <div class="info-card-value"><?php echo htmlspecialchars($stats['total_reservaciones'] ?? 0); ?></div>
        </div>
    </div>
</div>
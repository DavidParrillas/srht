<!-- dashboard/content.php -->
<div class="dashboard-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>
    <p>Bienvenido de nuevo, <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?>.</p>
</div>

<!-- Info Cards con Bootstrap -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="info-card-label">Check-ins para Hoy</div>
            <div class="info-card-value"><?php echo htmlspecialchars($stats['checkins_hoy'] ?? 0); ?></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="info-card-label">Ingresos del Día</div>
            <div class="info-card-value">$<?php echo number_format($stats['ingresos_dia'] ?? 0, 2); ?></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon warning">
                <i class="fas fa-bed"></i>
            </div>
            <div class="info-card-label">Habitaciones Ocupadas</div>
            <div class="info-card-value"><?php echo htmlspecialchars($stats['habitaciones_ocupadas'] ?? 0); ?> / <?php echo htmlspecialchars($stats['total_habitaciones'] ?? 0); ?></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon danger">
                <i class="fas fa-clock"></i>
            </div>
            <div class="info-card-label">Check-outs Hoy</div>
            <div class="info-card-value"><?php echo htmlspecialchars($stats['checkouts_hoy'] ?? 0); ?></div>
        </div>
    </div>
</div>

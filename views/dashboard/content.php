<!-- dashboard/content.php -->
<div class="dashboard-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>
    <p>Bienvenido de nuevo, <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?>.</p>
</div>

<!-- Info Cards con Bootstrap -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon blue">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="info-card-title">Reservas Hoy</div>
            <div class="info-card-value">24</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon green">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="info-card-title">Ingresos del Día</div>
            <div class="info-card-value">Q8,450</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon orange">
                <i class="fas fa-bed"></i>
            </div>
            <div class="info-card-title">Habitaciones Ocupadas</div>
            <div class="info-card-value">45/60</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="info-card">
            <div class="info-card-icon red">
                <i class="fas fa-clock"></i>
            </div>
            <div class="info-card-title">Check-outs Hoy</div>
            <div class="info-card-value">12</div>
        </div>
    </div>
</div>

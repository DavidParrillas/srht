<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <div class="date-filter">
            <!-- Aquí irán los filtros de fecha -->
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="row">
            <?php include_once __DIR__ . '/widgets/ocupacion.php'; ?>
            <?php include_once __DIR__ . '/widgets/ingresos.php'; ?>
        </div>
        
        <div class="row">
            <?php include_once __DIR__ . '/widgets/reservas_recientes.php'; ?>
            <?php include_once __DIR__ . '/widgets/estadisticas.php'; ?>
        </div>
    </div>
</div>
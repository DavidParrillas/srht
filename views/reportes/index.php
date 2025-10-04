<div class="page-header">
    <h1><?php echo htmlspecialchars($page_title ?? 'Reportes'); ?></h1>
    <p>Selecciona un reporte para generar y visualizar los datos.</p>
</div>

<div class="report-list">
    <div class="report-item">
        <h3><i class="fas fa-chart-pie"></i> Reporte de Ocupación</h3>
        <p>Analiza la tasa de ocupación por período, tipo de habitación, etc.</p>
        <a href="index.php?controller=reportes&action=generar&tipo=ocupacion" class="btn btn-secondary">Generar Reporte</a>
    </div>

    <div class="report-item">
        <h3><i class="fas fa-chart-bar"></i> Reporte de Ingresos</h3>
        <p>Visualiza los ingresos generados por reservaciones, paquetes y otros servicios.</p>
        <a href="index.php?controller=reportes&action=generar&tipo=ingresos" class="btn btn-secondary">Generar Reporte</a>
    </div>

    <div class="report-item">
        <h3><i class="fas fa-history"></i> Historial de Reservaciones</h3>
        <p>Consulta un historial detallado de todas las reservaciones en un rango de fechas.</p>
        <a href="index.php?controller=reportes&action=generar&tipo=historial" class="btn btn-secondary">Generar Reporte</a>
    </div>

    <!-- Se pueden agregar más tipos de reportes aquí -->

</div>
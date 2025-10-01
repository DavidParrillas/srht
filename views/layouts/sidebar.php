<aside class="main-sidebar">
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo $active_page == 'dashboard' ? 'active' : ''; ?>">
                <a href="index.php?controller=home">Dashboard</a>
            </li>
            <li class="<?php echo $active_page == 'reservaciones' ? 'active' : ''; ?>">
                <a href="index.php?controller=reservaciones">Reservaciones</a>
            </li>
            <li class="<?php echo $active_page == 'clientes' ? 'active' : ''; ?>">
                <a href="index.php?controller=clientes">Clientes</a>
            </li>
            <li class="<?php echo $active_page == 'habitaciones' ? 'active' : ''; ?>">
                <a href="index.php?controller=habitaciones">Habitaciones</a>
            </li>
            <li class="<?php echo $active_page == 'paquetes' ? 'active' : ''; ?>">
                <a href="index.php?controller=paquetes">Paquetes y Tarifas</a>
            </li>
            <li class="<?php echo $active_page == 'planes_pago' ? 'active' : ''; ?>">
                <a href="index.php?controller=planes_pago">Planes de Pago</a>
            </li>
            <li class="<?php echo $active_page == 'reportes' ? 'active' : ''; ?>">
                <a href="index.php?controller=reportes">Reportes</a>
            </li>
            <li class="<?php echo $active_page == 'usuarios' ? 'active' : ''; ?>">
                <a href="index.php?controller=usuarios">Usuarios</a>
            </li>
            <li>
                <a href="index.php?controller=auth&action=logout">Cerrar Sesión</a>
            </li>
        </ul>
    </nav>
</aside>
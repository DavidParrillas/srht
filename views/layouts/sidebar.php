<?php
function render_nav_item($iconClass, $text, $controller, $active_page) {
    $class = ($active_page == $controller) ? 'active' : '';
    $url = "index.php?controller={$controller}";
    echo "<li class=\"{$class}\">";
    echo "    <a href=\"{$url}\">";
    echo "        <i class=\"fas {$iconClass}\"></i> <span>{$text}</span>";
    echo "    </a>";
    echo "</li>";
}
?>
<aside class="main-sidebar">
    <nav class="sidebar-nav">
        <ul>
            <?php 
            // Dashboard - Todos los roles
            render_nav_item('fa-tachometer-alt', 'Dashboard', 'home', $active_page); 
            
            // Reservas - Todos los roles
            render_nav_item('fa-calendar-check', 'Reservas', 'reservaciones', $active_page); 
            
            // Clientes - Todos los roles
            render_nav_item('fa-users', 'Clientes', 'clientes', $active_page); 
            
            // Habitaciones - Todos los roles
            render_nav_item('fa-bed', 'Habitaciones', 'habitaciones', $active_page); 
            
            // Amenidades - Solo Administrador y Gerencia
            if (isset($_SESSION['usuario_rol_nombre']) && in_array($_SESSION['usuario_rol_nombre'], ['Administrador', 'Gerencia'])) {
                render_nav_item('fa-gem', 'Amenidades', 'amenidades', $active_page);
            }
            
            // Paquetes - Todos los roles
            render_nav_item('fa-box-open', 'Paquetes', 'paquetes', $active_page); 
            
            // Reportes - Solo Gerencia y Administrador
            if (isset($_SESSION['usuario_rol_nombre']) && 
                in_array($_SESSION['usuario_rol_nombre'], ['Gerencia', 'Administrador'])) {
                render_nav_item('fa-chart-line', 'Reportes', 'reportes', $active_page);
            }
            
            // Usuarios - Solo Administrador
            if (isset($_SESSION['usuario_rol_nombre']) && $_SESSION['usuario_rol_nombre'] === 'Administrador') {
                render_nav_item('fa-user-shield', 'Usuarios', 'usuarios', $active_page);
            }
            ?>

            <li>
                <a href="index.php?controller=auth&action=logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
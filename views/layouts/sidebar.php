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
            <?php render_nav_item('fa-tachometer-alt', 'Dashboard', 'home', $active_page); ?>
            <?php render_nav_item('fa-calendar-check', 'Reservaciones', 'reservaciones', $active_page); ?>
            <?php render_nav_item('fa-users', 'Clientes', 'clientes', $active_page); ?>
            <?php render_nav_item('fa-bed', 'Habitaciones', 'habitaciones', $active_page); ?>
            <?php render_nav_item('fa-gem', 'Amenidades', 'amenidades', $active_page); ?>
            <?php render_nav_item('fa-box-open', 'Paquetes', 'paquetes', $active_page); ?>
            <?php render_nav_item('fa-chart-line', 'Reportes', 'reportes', $active_page); ?>
            <?php render_nav_item('fa-user-shield', 'Usuarios', 'usuarios', $active_page); ?>
            <?php
            
            // Ejemplo de cómo mostrar un enlace solo a ciertos roles
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'recepcion') {
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
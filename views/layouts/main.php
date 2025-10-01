<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Sistema de Reservaciones' : 'Sistema de Reservaciones'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>
    <?php include_once __DIR__ . '/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <?php 
            if (isset($child_view) && file_exists($child_view)) {
                include $child_view;
            }
            ?>
        </div>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Sistema de Reservaciones' : 'Sistema de Reservaciones'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-custom.css">
</head>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>
    <?php include_once __DIR__ . '/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container-fluid">
            <?php 
            if (isset($child_view) && file_exists($child_view)) {
                include $child_view;
            }
            ?>
        </div>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript principal -->
    <script src="/assets/js/main.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Reservaciones</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
    <body class="login-page">
        <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="assets/images/logo_Blanco.png" alt="Logo" class="login-logo">
                <h1>Iniciar Sesión</h1>
            </div>
            
            <form action="index.php?controller=auth&action=login" method="POST">
                    <?php if (isset($error_message)): ?>
                        <div class="error-message" style="color:red; margin-bottom:10px;">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                <div class="form-group">
                    <label for="nombre_usuario">Usuario:</label>
                    <input type="text" name="nombre_usuario" id="nombre_usuario" required>
                </div>
                
                <div class="form-group">
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" name="contrasena" id="contrasena" required>
                </div>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
    <script src="/assets/js/main.js"></script>
</body>
</html>

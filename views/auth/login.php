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
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="assets/images/log.png" alt="Logo Hotel Torremolinos" class="login-logo">
                <h1>Iniciar Sesión</h1>
                <p class="text-muted">Sistema de Reservaciones</p>
            </div>
            
            <!-- Mensajes de error -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php 
                        echo htmlspecialchars($_SESSION['error_message']); 
                        unset($_SESSION['error_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Mensajes de éxito -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php 
                        echo htmlspecialchars($_SESSION['success_message']); 
                        unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form action="index.php?controller=auth&action=login" method="POST" id="loginForm" novalidate>
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- Campo de Correo Electrónico -->
                <div class="form-group mb-3">
                    <label for="correo" class="form-label">
                        <i class="fas fa-envelope"></i> Correo Electrónico:
                    </label>
                    <input 
                        type="email"
                        name="correo" 
                        id="correo" 
                        class="form-control" 
                        placeholder="ejemplo@hotel.com"
                        required
                        autocomplete="email"
                        value="<?php echo isset($_SESSION['login_correo']) ? htmlspecialchars($_SESSION['login_correo']) : ''; ?>"
                    >
                    <div class="invalid-feedback">
                        Por favor ingrese un correo electrónico válido.
                    </div>
                </div>
                
                <!-- Campo de Contraseña -->
                <div class="form-group mb-3">
                    <label for="contrasena" class="form-label">
                        <i class="fas fa-lock"></i> Contraseña:
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            name="contrasena" 
                            id="contrasena" 
                            class="form-control" 
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            minlength="6"
                        >
                        <button 
                            class="btn btn-outline-secondary" 
                            type="button" 
                            id="togglePassword"
                            title="Mostrar/Ocultar contraseña"
                        >
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">
                        La contraseña debe tener al menos 6 caracteres.
                    </div>
                </div>
                
                <!-- Botón de envío -->
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>

        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script personalizado para validación y mostrar contraseña -->
    <script>
        // Validación del formulario
        const form = document.getElementById('loginForm');
        
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
        
        // Toggle para mostrar/ocultar contraseña
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('contrasena');
        const toggleIcon = document.getElementById('toggleIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Cambiar icono
            if (type === 'password') {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        });
        
        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        <?php 
        // Limpiar variable temporal de correo
        if (isset($_SESSION['login_correo'])) {
            unset($_SESSION['login_correo']);
        }
        ?>
    </script>
</body>
</html>
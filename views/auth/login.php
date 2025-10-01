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
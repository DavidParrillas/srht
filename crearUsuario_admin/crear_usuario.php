<?php
/**
 * Script para crear un usuario administrador utilizando el modelo de la aplicación.
 * Esto asegura que la lógica de negocio (ej. hasheo de contraseña) sea consistente.
 */

// Definir la raíz del proyecto para poder incluir los modelos y la configuración
define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/models/Database.php';
require_once PROJECT_ROOT . '/models/Usuario.php';

// ============================================
// CONFIGURACION - EDITA ESTOS VALORES
// ============================================
$nuevoUsuario = 'admin'; // Nombre de usuario
$nuevoEmail = 'admin@torremolinos.com'; // Correo para el login
$nuevoPassword = 'admin123'; // Contraseña en texto plano
$idRolAsignar = 4;  // 4 = Administrador (según script.sql)
// ============================================

try {
    // Obtener conexión a la BD usando la clase de la aplicación
    $database = Database::getInstance();
    $conexion = $database->getConnection();
    echo "Conectado a la base de datos.\n\n";

    // Instanciar el modelo de Usuario
    $usuarioModel = new Usuario($conexion);

    // Verificar si el correo ya existe usando el método del modelo
    if ($usuarioModel->correoExiste($nuevoEmail)) {
        echo "ERROR: El correo electrónico '{$nuevoEmail}' ya está registrado.\n";
        exit(1);
    }

    // Asignar los datos al modelo. El método setContrasenaUsuario se encargará del hasheo.
    $usuarioModel->setNombreUsuario($nuevoUsuario);
    $usuarioModel->setCorreoUsuario($nuevoEmail);
    $usuarioModel->setContrasenaUsuario($nuevoPassword); // Se pasa la contraseña en texto plano
    $usuarioModel->setIdRol($idRolAsignar);

    // Intentar crear el usuario
    if ($usuarioModel->crear()) {
        $idUsuario = $usuarioModel->getIdUsuario();
        echo "Usuario creado con éxito (ID: {$idUsuario}).\n";

        // Obtener el nombre del rol para mostrarlo
        $stmt = $conexion->prepare("SELECT NombreRol FROM Rol WHERE idRol = ?");
        $stmt->execute([$idRolAsignar]);
        $nombreRol = $stmt->fetchColumn();

        echo "==========================================\n";
        echo "Usuario: {$nuevoUsuario}\n";
        echo "Email: {$nuevoEmail}\n";
        echo "Contraseña: {$nuevoPassword}\n";
        echo "Rol: {$nombreRol}\n";
        echo "==========================================\n";
    } else {
        echo "ERROR: No se pudo crear el usuario.\n";
        exit(1);
    }

} catch (PDOException $e) {
    echo "ERROR DE CONEXIÓN A LA BASE DE DATOS: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "ERROR GENERAL: " . $e->getMessage() . "\n";
    exit(1);
}
?>

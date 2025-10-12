<?php
/**
 * Script completo para crear usuario con rol asignado
 */

// ============================================
// CONFIGURACION - EDITA ESTOS VALORES
// ============================================
$host = '127.0.0.1';
$dbname = 'hoteltorremolinos';
$username = 'root';
$password = '1234';

$nuevoUsuario = 'user';
$nuevoEmail = 'user@gmail.com';
$nuevoPassword = '1234';
$idRolAsignar = 4;  // ID del rol
// ============================================

try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado a la base de datos\n\n";
    
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("SELECT idUsuario FROM Usuario WHERE NombreUsuario = ? OR CorreoUsuario = ?");
    $stmt->execute([$nuevoUsuario, $nuevoEmail]);
    
    if ($stmt->fetch()) {
        $pdo->rollBack();
        echo "ERROR: Ya existe un usuario con ese nombre o email\n";
        exit(1);
    }
    
    $hash = password_hash($nuevoPassword, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO Usuario (NombreUsuario, CorreoUsuario, ContrasenaUsuario, idRol) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nuevoUsuario, $nuevoEmail, $hash, $idRolAsignar]);
    $idUsuario = $pdo->lastInsertId();
    
    echo "Usuario creado (ID: $idUsuario)\n";
    
    $stmt = $pdo->prepare("SELECT NombreRol FROM Rol WHERE idRol = ?");
    $stmt->execute([$idRolAsignar]);
    $nombreRol = $stmt->fetchColumn();
    
    if (!$nombreRol) {
        $pdo->rollBack();
        echo "ERROR: No existe el rol con ID '$idRolAsignar'\n";
        echo "\nRoles disponibles:\n";
        $stmt = $pdo->query("SELECT idRol, NombreRol FROM Rol");
        while ($row = $stmt->fetch()) {
            echo "- {$row['NombreRol']} (ID: {$row['idRol']})\n";
        }
        exit(1);
    }
    
    echo "Rol asignado: $nombreRol (ID: $idRolAsignar)\n\n";
    
    $pdo->commit();
    
    echo "¡Usuario creado exitosamente!\n";
    echo "==========================================\n";
    echo "Usuario: $nuevoUsuario\n";
    echo "Email: $nuevoEmail\n";
    echo "Contraseña: $nuevoPassword\n";
    echo "Rol: $nombreRol\n";
    echo "==========================================\n";
    
} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>

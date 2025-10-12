<?php
$host = '127.0.0.1';
$dbname = 'hoteltorremolinos';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== USUARIOS EN LA BASE DE DATOS ===\n\n";
    
    $stmt = $pdo->query("SELECT u.idUsuario, u.NombreUsuario, u.CorreoUsuario, u.ContrasenaUsuario, u.idRol, r.NombreRol 
        FROM Usuario u 
        LEFT JOIN Rol r ON u.idRol = r.idRol");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['idUsuario']}\n";
        echo "Usuario: {$row['NombreUsuario']}\n";
        echo "Email: {$row['CorreoUsuario']}\n";
        echo "Hash: " . substr($row['ContrasenaUsuario'], 0, 30) . "...\n";
        echo "Rol ID: {$row['idRol']}\n";
        echo "Rol Nombre: {$row['NombreRol']}\n";
        echo "Hash comienza con \$2y\$: " . (strpos($row['ContrasenaUsuario'], '$2y$') === 0 ? 'SI' : 'NO') . "\n";
        echo "---\n\n";
    }
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
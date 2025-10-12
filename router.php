<?php
session_start();

// Incluir la clase de la base de datos para la inyección de dependencias
require_once __DIR__ . '/models/Database.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = __DIR__ . "/controllers/$controllerName.php";

if (!file_exists($controllerFile)) {
    die("Error: El controlador '$controllerName' no existe.");
}

require_once $controllerFile;

// Usar Reflection para instanciar el controlador de forma inteligente
$reflectionClass = new ReflectionClass($controllerName);
$constructor = $reflectionClass->getConstructor();

if ($constructor && count($constructor->getParameters()) > 0) {
    // El constructor espera parámetros, asumimos que es la conexión a la BD
    $database = Database::getInstance();
    $conexion = $database->getConnection();
    $controllerInstance = new $controllerName($conexion);
} else {
    // El constructor no espera parámetros o no existe
    $controllerInstance = new $controllerName();
}

if (!method_exists($controllerInstance, $action)) {
    die("Error: La acción '$action' no existe en el controlador '$controllerName'.");
}

$controllerInstance->$action();
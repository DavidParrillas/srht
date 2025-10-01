<?php
session_start();

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = "controllers/$controllerName.php";

if (!file_exists($controllerFile)) {
    die("Error: El controlador '$controllerName' no existe.");
}

require_once $controllerFile;

$controllerInstance = new $controllerName();
if (!method_exists($controllerInstance, $action)) {
    die("Error: La acción '$action' no existe en el controlador '$controllerName'.");
}

$controllerInstance->$action();
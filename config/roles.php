<?php
// Definición de roles y permisos
return [
    'admin' => ['*'],
    'gerente' => ['reservas', 'reportes'],
    'recepcion' => ['clientes', 'reservas'],
];

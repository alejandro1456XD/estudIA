<?php

// AUMENTAR LÍMITES DINÁMICAMENTE
// Forzamos la configuración de PHP en tiempo de ejecución para esta aplicación.
// Esto sobrescribe la configuración del php.ini para las solicitudes que pasan por este index.php.
ini_set('upload_max_filesize', '50M'); // Tamaño máximo de un archivo subido
ini_set('post_max_size', '50M');       // Tamaño máximo de toda la petición POST
ini_set('memory_limit', '256M');       // Memoria máxima que puede usar el script
ini_set('max_execution_time', '300');  // Tiempo máximo de ejecución en segundos (5 minutos)

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
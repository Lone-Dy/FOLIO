<?php

// ROUTEUR dynamique

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\{HomeController, E404Controller, LoginController};
use App\Service\{DatabaseFactory};

$request = trim($_SERVER['REQUEST_URI'], '/'); // folio.local/..../....
$params = explode('/', $request); // ['folio', 'detail', '4']
$controller = array_shift($params); // 'folio' - ['detail', '4']
if ($controller == '') {
    $controller = 'Home';
}

$method = array_shift($params);
if ($method == '') {
    $method = 'index';
}

$controllerClass = 'App\\Controller\\' . ucfirst($controller) . 'Controller'; // App\Controller\HomeController
if (!class_exists($controllerClass)) {
    $controllerClass = E404Controller::class; // App\Controller\E404Controller
}

try {
    $envPath = __DIR__ . '/../.env';

    if (!file_exists($envPath)) {
        throw new Exception("Configuration file (.env) is missing at project root.");
    }

    $config = parse_ini_file($envPath);
    $pdo = DatabaseFactory::create($config);
} catch (Exception $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Une erreur technique est survenue. Veuillez réessayer plus tard.");
}

$container = [
    HomeController::class => function ($pdo) {
        return new HomeController();
    },
    E404Controller::class => function ($pdo) {
        return new E404Controller();
    },
    LoginController::class => function ($pdo) {
        $repo = new \App\Repository\UserRepository($pdo);
        return new LoginController($repo);
    }
];

$controllerInstance = $container[$controllerClass]($pdo);
if (!method_exists($controllerInstance, $method)) {
    $method = 'index';
}
$controllerInstance->$method($params);

?>
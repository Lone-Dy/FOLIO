<?php

// ROUTEUR dynamique
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\{HomeController, E404Controller, LoginController, ConditionController, ProfileController};
use App\Service\{DatabaseFactory};

$request = trim($_SERVER['REQUEST_URI'], '/'); // folio.local/..../....
$params = explode('/', $request); // ['folio', 'detail', '4']
$controllerName = array_shift($params) ?: 'Home';


$controller = array_shift($params);

// Mapping pour les pages statiques/liens spéciaux
$map = [
    'condition'        => 'Condition',
    'mentions-legales' => 'Condition',
    'politique'        => 'Condition'
];
if (isset($map[$controllerName])) {
    $controllerName = $map[$controllerName];
}

$method = array_shift($params) ?: 'index';

$controllerClass = 'App\\Controller\\' . ucfirst($controllerName) . 'Controller';
if (!class_exists($controllerClass)) {
    $controllerClass = E404Controller::class;
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
    },
    ConditionController::class => function ($pdo) {
        return new ConditionController();
    },

    ProfileController::class => function ($pdo) {
        $repo = new \App\Repository\UserRepository($pdo);
        $service = new \App\Service\UserService($repo); // On crée le service
        return new ProfileController($service, $repo); // On injecte le SERVICE et non le repo
    }
];

$controllerInstance = $container[$controllerClass]($pdo);
if (!method_exists($controllerInstance, $method)) {
    $method = 'index';
}
$controllerInstance->$method($params);

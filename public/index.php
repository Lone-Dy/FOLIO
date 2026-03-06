<?php

// ROUTEUR dynamique

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\{HomeController, E404Controller, LoginController, ConditionController, ProfileController};
use App\Repository\{UserRepository, ProjetRepository, FolioRepository};
use App\Service\{DatabaseFactory, UserService};

session_start();

$request = trim($_SERVER['REQUEST_URI'], '/'); //$request devient profile/edit/5
$params = explode('/', $request); //$params devient un tableau ['profile', 'edit', '5']
$controllerSlug = array_shift($params) ?: 'Home';
$method = array_shift($params) ?: 'index';
$controllerName = ucfirst($controllerSlug) . 'Controller'; //reconstruction du nom de la classe. 
$controllerClass = "App\\Controller\\" . $controllerName;

// 1. on se connecte à PDO

try {
    $envPath = __DIR__ . '/../.env';
    // si pas de fichier .env on leve une exception
    if (!file_exists($envPath)) {
        throw new Exception("Le fichier .env est manquant.");
    }
    // on lit le .env
    $config = parse_ini_file($envPath); 
    // La factory DatabaseFactory crée le $pdo à partir du contenu de .env
    $pdo = DatabaseFactory::create($config);
} catch (Exception $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    die("Une erreur technique est survenue. Veuillez réessayer plus tard.");
}

// 2. Configuration des dépendances

$container = [
    HomeController:: class => function ($pdo) {
        return new HomeController();
    },
    LoginController::class => function ($pdo) {
        return new LoginController(new UserRepository($pdo));
    },
    ProfileController::class => function ($pdo) {
        $repo = new UserRepository($pdo);
        $service = new UserService($repo);
        return new ProfileController($service, $repo);
    },

    E404Controller::class => function () {
        return new E404Controller();
    }
];

// Est-ce que le contrôleur existe dans mon container ?
if (!isset($container[$controllerClass])) {
    $controllerClass = E404Controller::class;
    $method = 'index';
}

// Est-ce que la méthode (l'action) existe dans cet objet ?
$controllerInstance = $container[$controllerClass]($pdo);
if (!method_exists($controllerInstance, $method)) {
    $method = 'index'; // Méthode par défaut
}

// Je lance l'action avec les paramètres restants
$controllerInstance->$method($params);

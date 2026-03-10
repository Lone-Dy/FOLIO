<?php

// ROUTEUR dynamique

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\{HomeController, E404Controller, LoginController, ConditionController, ProfileController, ProjetController};
use App\Repository\{UserRepository, ProjetRepository, FolioRepository};
use App\Service\{DatabaseFactory, ProjetService, UserService};

session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = trim($uri, '/');
$params = explode('/', $request);

$controllerSlug = array_shift($params) ?: 'Home';
$method = array_shift($params) ?: 'index';

$controllerName = ucfirst($controllerSlug) . 'Controller';
$controllerClass = "App\\Controller\\" . $controllerName;

// 1. Connexion à PDO

try {
    $envPath = __DIR__ . '/../.env';
    // si pas de fichier .env, je leve une exception
    if (!file_exists($envPath)) {
        throw new Exception("Le fichier .env est manquant.");
    }
    // lecture du .env
    $config = parse_ini_file($envPath); 
    // La factory DatabaseFactory crée le $pdo à partir du contenu de .env
    $pdo = DatabaseFactory::create($config);
} catch (Exception $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    die("Une erreur technique est survenue. Veuillez réessayer plus tard.");
}

// 2. Configuration des dépendances

$container = [

    HomeController::class => function ($pdo) {
        return new HomeController();
    },

    E404Controller::class => function ($pdo) {
        return new E404Controller();
    },

    ConditionController::class => function ($pdo) {
        return new ConditionController();
    },

    LoginController::class => function ($pdo) {
        return new LoginController(new UserRepository($pdo));
    },

    ProfileController::class => function ($pdo) {
        $repo = new UserRepository($pdo);
        $service = new UserService($repo);
        $projetRepo = new ProjetRepository($pdo);
        $projetService = new ProjetService($projetRepo);

        return new ProfileController($service, $repo, $projetService);
    },

    ProjetController::class=> function ($pdo) {
        $repo = new ProjetRepository($pdo);
        $service = new ProjetService($repo);
        return new ProjetController($service);
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

$controllerInstance->$method(...$params);
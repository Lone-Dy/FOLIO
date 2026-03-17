<?php

// ROUTEUR dynamique

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\{HomeController, E404Controller, LoginController, ConditionController, ProfileController, ProjetController};
use App\Repository\{UserRepository, ProjetRepository, FolioRepository, MediaRepository};
use App\Service\{DatabaseFactory, PortfolioService, UserService};

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

        $folioRepo = new FolioRepository($pdo);
        $projetRepo = new ProjetRepository($pdo);
        $mediaRepo = new MediaRepository($pdo);
        $portfolioService = new PortfolioService($pdo, $folioRepo, $projetRepo, $mediaRepo);
        return new HomeController($portfolioService);
    },

    E404Controller::class => function ($pdo) {
        return new E404Controller();
    },

    ConditionController::class => function ($pdo) {
        return new ConditionController();
    },

    LoginController::class => function ($pdo) {

        $userRepo = new UserRepository($pdo);
        $userService = new UserService($userRepo);
        return new LoginController($userRepo, $userService);
    },

    ProfileController::class => function ($pdo) {

        $userRepo = new UserRepository($pdo);
        $folioRepo = new FolioRepository($pdo);
        $projetRepo = new ProjetRepository($pdo);
        $mediaRepo = new MediaRepository($pdo);
        $userService = new UserService($userRepo);
        $portfolioService = new PortfolioService($pdo, $folioRepo, $projetRepo, $mediaRepo);
        return new ProfileController($userService, $userRepo, $portfolioService);
    },

    ProjetController::class=> function ($pdo) {
        
        $folioRepo = new FolioRepository($pdo);
        $projetRepo = new ProjetRepository($pdo);
        $mediaRepo = new MediaRepository($pdo);
        $portfolioService = new PortfolioService($pdo, $folioRepo, $projetRepo, $mediaRepo);
        return new ProjetController($portfolioService);
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
<?php

// ROUTEUR dynamique

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\{
HomeController, 
E404Controller, 
LoginController, 
ConditionController, 
ProfileController, 
ProjetController, 
GalerieController};

use App\Service\{
DatabaseFactory, 
GalerieService,  
FlashService, 
MediaService, 
FolioService,
TemplateService,
AuthService,
ProfileService};

use App\Repository\{
UserRepository, 
ProjetRepository, 
FolioRepository, 
MediaRepository};


session_set_cookie_params([
    'lifetime'  => 0,
    'path'      => '/',
    'domain'    => '',
    'secure'    => false,
    'httponly'  => true,
    'samesite'  => 'Strict'
]);
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
        $flashService = new FlashService();
        $templateService = new TemplateService();
        $mediaService = new MediaService($mediaRepo);
        $folioService = new FolioService($pdo, $folioRepo, $projetRepo, $mediaService, $flashService, $templateService);
        $galerieService = new GalerieService($folioRepo, $projetRepo, $mediaRepo);

        return new HomeController($folioService, $galerieService);
    },

    E404Controller::class => function () {
        return new E404Controller();
    },

    ConditionController::class => function () {
        $templateService = new TemplateService();
        return new ConditionController($templateService);
    },

    GalerieController::class => function ($pdo) {
        $folioRepo = new FolioRepository($pdo);
        $projetRepo = new ProjetRepository($pdo);
        $mediaRepo = new MediaRepository($pdo);
        $flashService = new FlashService();
        $templateService = new TemplateService();
        $mediaService = new MediaService($mediaRepo);
        $folioService = new FolioService($pdo, $folioRepo, $projetRepo, $mediaService, $flashService, $templateService);

        return new GalerieController($folioService);
    },

    LoginController::class => function () {
        $userRepo = new UserRepository($pdo);
        $flashService = new FlashService();
        $authService = new AuthService();

        return new LoginController($flashService, $userRepo);
    },

    ProfileController::class => function ($pdo) {
        $userRepo = new UserRepository($pdo);
        $flashService = new FlashService();
        $authService = new AuthService();

        return new ProfileController($flashService, $userRepo);
    },

    ProjetController::class => function ($pdo) {
        $folioRepo = new FolioRepository($pdo);
        $projetRepo = new ProjetRepository($pdo);
        $mediaRepo = new MediaRepository($pdo);

        $flashService = new FlashService();
        $templateService = new TemplateService();
        $mediaService = new MediaService($mediaRepo);
        $folioService = new FolioService($pdo, $folioRepo, $projetRepo, $mediaService, $flashService, $templateService);

        return new ProjetController($folioService, $flashService, $templateService);
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
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
GalerieController,
SecurityController};

use App\Exception\{
ServiceException, 
RenderException};

use App\Service\{
DatabaseFactory, 
GalerieService,  
FlashService, 
MediaService, 
FolioService,
AuthService,
ProfileService,
SecurityService,
Renderer,
Request};

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

// Instancie le SecurityService, Renderer et Request
    $securityService = new SecurityService(new UserRepository($pdo));
    $renderer = new Renderer(__DIR__ . '/../template', $securityService);
    $request = new Request();

// 2. Configuration des dépendances

$container = [

HomeController::class => function ($pdo) use ($renderer)
    {
        $folioRepo = new FolioRepository($pdo);
        $projetRepo = new ProjetRepository($pdo);
        $mediaRepo = new MediaRepository($pdo);    

        $galerieService = new GalerieService($folioRepo, $projetRepo, $mediaRepo);

        return new HomeController($renderer, $galerieService);
    },

    E404Controller::class => function () use ($renderer)
    {
    
        return new E404Controller($renderer);
    },

    ConditionController::class => function ($pdo) use ($renderer) 
    {
    
        return new ConditionController($renderer);
    },

    GalerieController::class => function ($pdo) use ($renderer)
    {
        $folioRepo = new FolioRepository($pdo);
        $projetRepo = new ProjetRepository($pdo);
        $mediaRepo = new MediaRepository($pdo);
        $galerieService = new GalerieService($folioRepo, $projetRepo, $mediaRepo);

        return new GalerieController($renderer, $galerieService);
    },

    LoginController::class => function ($pdo) use ($renderer)
    {
        $userRepo = new UserRepository($pdo);
        $flashService = new FlashService();
        $authService = new AuthService($userRepo, $flashService);

        return new LoginController($renderer, $authService, $flashService);
    },

    ProfileController::class => function ($pdo) use ($renderer)
    {
        $userRepo = new UserRepository($pdo);
        $flashService = new FlashService();
        $authService = new AuthService($userRepo, $flashService);
        $profileService = new ProfileService($userRepo, $flashService);

        return new ProfileController($renderer, $profileService, $flashService, $authService);
    },

    ProjetController::class => function ($pdo) use ($renderer)
    {
        $folioRepo = new FolioRepository($pdo);
        $projetRepo = new ProjetRepository($pdo);
        $mediaRepo = new MediaRepository($pdo);
        $flashService = new FlashService();
        $mediaService = new MediaService($mediaRepo);
        $folioService = new FolioService($pdo, $folioRepo, $projetRepo, $mediaService, $flashService);

        return new ProjetController($renderer, $folioService, $flashService);
    },

    SecurityController::class => function($pdo) 
    {
        $userRepo = new UserRepository($pdo);
        return new SecurityController($userRepo);
    },
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
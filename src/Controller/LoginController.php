<?php

namespace App\Controller;

use App\Service\UserService;
use App\Service\GalerieService;
use App\Service\FlashService;
use App\Service\FolioService;
use App\Service\ProjetService;
use App\Repository\UserRepository;

class LoginController
{
    private UserService $userService;
    private GalerieService $galerieService;
    private FlashService $flashService;
    private FolioService $folioService;
    private ProjetService $projetService;
    private UserRepository $userRepository;

public function __construct( 

        UserService $userService, 
        GalerieService $galerieService,
        FlashService $flashService,
        FolioService $folioService,
        ProjetService $projetService,
        UserRepository $userRepository
        )

    {

        $this->userService = $userService;
        $this->galerieService = $galerieService;
        $this->flashService = $flashService;
        $this->folioService = $folioService;
        $this->projetService = $projetService;
        $this->userRepository = $userRepository;

    }

    // Affiche la page de connexion/inscription
    public function index(?array $params = null)
    {
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }

        include __DIR__ . '/../../template/login_page.php';
    }

    // Traite le formulaire de connexion
    public function handleLogin(?array $params = null) // La vérification (Connexion)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $authResult = $this->userService->authenticate($email, $password);

            if ($authResult instanceof \App\Entity\User) {

                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id' => $authResult->getIdUtilisateur(),
                    'email' => $authResult->getEmail()
                ];

                // Redirection vers le home
                header('Location: /');
                exit;
            } else {

                $errorCode = strtolower($authResult);
                header("Location: /login?error=$errorCode");
                exit;
            }
        }
    }
    
    // Traite l'inscription d'un nouvel utilisateur
    public function handleRegister(FlashService $flashService)  // La création (Inscription)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $flashService->addError("Méthode non autorisée.");
        header('Location: /login');
        exit;
    }

    try {
        $this->userService->registerUser($_POST, $_FILES['avatar'] ?? null);
        $flashService->addSuccess("Inscription réussie ! Bienvenue sur Folio.");
        header('Location: /profile');
        
    } catch (\Exception $e) {
        $flashService->addError($e->getMessage());
        header('Location: /login#register-section');
    }
    exit;
}

    // Détruit la session et redirige vers l'accueil
    public function logout()
    {
        session_destroy();
        unset($_SESSION);
        header('Location: /home'); // ou /login
        exit;
    }
}
?>
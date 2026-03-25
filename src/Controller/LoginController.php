<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\FlashService;

class LoginController {
    private AuthService $authService;
    private FlashService $flashService;

    public function __construct(
        AuthService $authService,
        FlashService $flashService
    ) {
        $this->authService = $authService;
        $this->flashService = $flashService;
    }  

    // Affiche la page de login/inscription
    public function index()
    {
        if (isset($_SESSION['user'])) {
            header('Location: /profile');
            exit;
        }
        require __DIR__ . '/../../template/login_page.php';
    }

    // Gère la connexion
    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->flashService->addError("Méthode non autorisée.");
            header('Location: /login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->authService->login($email, $password);

        if ($user) {
            if ($user->getStatutCompte() !== 'actif') {
                $this->flashService->addError("Votre compte est désactivé.");
                header('Location: /login');
                exit;
            }

            $_SESSION['user'] = [
                'id' => $user->getIdUtilisateur(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()
            ];
            $this->flashService->addSuccess("Connexion réussie !");
            header('Location: /profile');
        } else {
            $this->flashService->addError("Email ou mot de passe incorrect.");
            header('Location: /login');
        }
        exit;
    }

    // Gère l'inscription
    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->flashService->addError("Méthode non autorisée.");
            header('Location: /login');
            exit;
        }

        if ($this->authService->register($_POST, $_FILES)) {
            $this->flashService->addSuccess("Inscription réussie ! Bienvenue sur Folio.");
            header('Location: /profile');
        } else {
            header('Location: /login#register-section');
        }
        exit;
    }

    // Gère la déconnexion
    public function logout(): void
    {
        $this->authService->logout();
        header('Location: /login');
        exit;
    }
}
?>
<?php

namespace App\Controller;

use App\Service\Renderer;
use App\Service\AuthService;
use App\Service\FlashService;

class LoginController {

    private Renderer $renderer;
    private AuthService $authService;
    private FlashService $flashService;

    public function __construct(
        Renderer $renderer,
        AuthService $authService,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->authService = $authService;
        $this->flashService = $flashService;
    }  

    // Affiche la page de login/inscription
    public function index()
    {
        // Préparation des données pour le template
        $data = [
            'title' => 'Accueil - Folio',
            'description' => 'Plateforme de partage de portfolios créatifs.',
            'author' => 'Nom de l’auteur',
            'copyright' => 'Propriétaire du copyright et année',
            'robots' => 'index, follow',
        ];

        // Récupération des messages flash via le service
        $flashMessages = $this->flashService->getMessages();
        $ob_modal = '';

        if (!empty($flashMessages)) {
            ob_start();
            foreach ($flashMessages as $type => $messages) {
                foreach ($messages as $message) {
                    $success = ($type === 'success');
                    $url = '/login';
                    
                    include __DIR__ . '/../../template/message_modal.php'; 
                }
            }
            $ob_modal = ob_get_clean();
        }

        // La modale générée passe les données du template
        $data['ob_modal'] = $ob_modal;

        if (isset($_SESSION['user'])) {
            header('Location: /profile');
            exit;
        }
        $this->renderer->render('login', $data);
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

            $_SESSION['user'] = [
                'id' => $user->getIdUtilisateur(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()
            ];
            header('Location: /profile');
        } else {
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

        // Récupération des données du formulaire
        $userData = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'age' => intval($_POST['age'] ?? 0),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
            'accept_conditions' => isset($_POST['accept_conditions'])
        ];

        // Validation des données
        $errors = [];
        if (empty($userData['nom'])) $errors[] = "Le nom est requis.";
        if (empty($userData['prenom'])) $errors[] = "Le prénom est requis.";
        if (empty($userData['email']) || !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if ($userData['age'] < 18) $errors[] = "Vous devez avoir au moins 18 ans.";
        if (strlen($userData['password']) < 12) $errors[] = "Le mot de passe doit contenir au moins 12 caractères.";
        if ($userData['password'] !== $userData['password_confirmation']) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (!$userData['accept_conditions']) $errors[] = "Vous devez accepter les conditions.";

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flashService->addError($error);
            }
            $_SESSION['form_data'] = [
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'email' => $userData['email'],
                'age' => $userData['age']
            ];
            header('Location: /login#register-section');
            exit;
        }

        // Appel du service d'authentification
        if ($this->authService->register($userData, $_FILES)) {
            $this->flashService->addSuccess("Inscription réussie ! Bienvenue sur Folio.");
            header('Location: /profile?new=1');
        } else {
            $this->flashService->addError("Une erreur est survenue lors de l'inscription.");
            header('Location: /login');
            exit;
        }
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
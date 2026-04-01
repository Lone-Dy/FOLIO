<?php

namespace App\Controller;

use App\Service\Renderer;
use App\Service\ProfileService;
use App\Service\FlashService;
use App\Service\AuthService;
use App\Exception\ServiceException;

class ProfileController {

    private Renderer $renderer;
    private ProfileService $profileService;
    private FlashService $flashService;
    private AuthService $authService;

    public function __construct(

        Renderer $renderer,
        ProfileService $profileService,
        FlashService $flashService,
        AuthService $authService,
    ) {

        $this->renderer = $renderer;
        $this->profileService = $profileService;
        $this->flashService = $flashService;
        $this->authService = $authService;
    }

    // Affiche la page profile
    public function index()
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $data = [
            'title' => 'Accueil - Folio',
            'description' => 'Plateforme de partage de portfolios créatifs.',
            'author' => 'Nom de l’auteur',
            'copyright' => 'Propriétaire du copyright et année',
            'robots' => 'index, follow',
            'user' => $user,
        ];

        $passwordRequirements = $this->profileService->getPasswordRequirements();
        
        $this->renderer->render('profile', $data);

    }

    // Gère la mise à jour du profil
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->flashService->addError("Méthode non autorisée.");
            header('Location: /profile');
            exit;
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        if ($this->profileService->updateProfile($user, $_POST, $_FILES)) {
            $this->flashService->addSuccess("Profil mis à jour avec succès.");
        }

        header('Location: /profile');
        exit;
    }

    // Gère le changement de mot de passe
    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->flashService->addError("Méthode non autorisée.");
            header('Location: /profile');
            exit;
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $oldPassword = $_POST['ancien_mdp'] ?? '';
        $newPassword = $_POST['nouveau_mdp'] ?? '';
        $confirmPassword = $_POST['confirmation_mdp'] ?? '';

        if ($this->profileService->changePassword($user, $oldPassword, $newPassword, $confirmPassword)) {
            $this->flashService->addSuccess("Mot de passe mis à jour avec succès.");
        }

        header('Location: /profile');
        exit;
    }

    // Gère la suppression du compte
    public function deleteAccount()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->flashService->addError("Méthode non autorisée.");
            header('Location: /profile');
            exit;
        }

        $user = $this->authService->getCurrentUser();
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        try {
            
            if ($this->profileService->accountDeletion($user, $passwordConfirmation)) {
                $this->authService->logout();
                $this->flashService->addSuccess("Votre compte a été supprimé.");
                header('Location: /login');
                exit;
            }
        
        } catch (\App\Exception\ServiceException $e) {
            
            $this->flashService->addError($e->getMessage());
            header('Location: /profile');
            exit;
        }
    }
}
?>
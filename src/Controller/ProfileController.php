<?php

namespace App\Controller;

use App\Service\ProfileService;
use App\Service\FlashService;
use App\Service\AuthService;

class ProfileController {

    private ProfileService $profileService;
    private FlashService $flashService;
    private AuthService $authService;

    public function __construct(

        ProfileService $profileService,
        FlashService $flashService,
        AuthService $authService,
    ) {

        $this->profileService = $profileService;
        $this->flashService = $flashService;
        $this->authService = $authService;
    }

    public function index()
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $passwordRequirements = $this->profileService->getPasswordRequirements();
        require __DIR__ . '/../../template/profile_page.php';
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
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        if ($this->profileService->accountDeletion($user, $passwordConfirmation)) {
            $this->authService->logout();
            header('Location: /');
            exit;
        }

        header('Location: /profile');
        exit;
    }
}
?>
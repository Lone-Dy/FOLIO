<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\FlashService;

class ProfileController {
    private AuthService $authService;
    private FlashService $flashService;

    public function __construct(
        AuthService $authService,
        FlashService $flashService
    ) {
        $this->authService = $authService;
        $this->flashService = $flashService;
    }

    // Affiche la page de profil
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $userId = $this->authService->getUserById($userId); // à ajouter dans AuthService. 

        require __DIR__ . '/../../template/profile.php';
    }

    // Met à jour le profil
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->flashService->addError("Méthode non autorisée.");
            header('Location: /profile');
            exit;
        }

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        if ($this->authService->updateProfile($userId, $_POST, $_FILES)) {
            $this->flashService->addSuccess("Profil mis à jour avec succès.");
        } else {
            $this->flashService->addError("Erreur lors de la mise à jour du profil.");
        }

        header('Location: /profile');
        exit;
    }

    // Met à jour le mot de passe
    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->flashService->addError("Méthode non autorisée.");
            header('Location: /profile');
            exit;
        }

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $oldPassword = $_POST['ancien_mdp'] ?? '';
        $newPassword = $_POST['nouveau_mdp'] ?? '';

        if ($this->authService->changePassword($userId, $oldPassword, $newPassword)) {
            $this->flashService->addSuccess("Mot de passe mis à jour avec succès.");
        } else {
            $this->flashService->addError("Erreur lors de la mise à jour du mot de passe.");
        }

        header('Location: /profile');
        exit;
    }    
}
?>
<?php

namespace App\Controller;

use App\Service\UserService;
use App\Repository\UserRepository;
use App\Service\ProjetService;

class ProfileController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private ProjetService $projetService;

    public function __construct(UserService $userService, UserRepository $userRepository, ProjetService $projetService)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->projetService = $projetService;
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            header('Location: /login');
            exit;
        }

        // Utilisation du nom de méthode correct défini dans ProjetService
        $projets = [];

        include __DIR__ . '/../../template/profile.php';
    }

    public function updatePassword()
    {
        session_start();
        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
            try {
                $this->userService->changeUserPassword($userId, $_POST['ancien_mdp'], $_POST['nouveau_mdp']);
                header('Location: /profile?success=1');
            } catch (\Exception $e) {
                header('Location: /profile?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }
}

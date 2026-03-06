<?php

namespace App\Controller;

use App\Service\UserService;
use App\Repository\UserRepository;
use Exception;

class ProfileController
{
    private UserService $userService;
    private UserRepository $userRepository;

    public function __construct(UserService $userService, UserRepository $userRepository) // On injecte le SERVICE
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
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

<?php

namespace App\Controller;

use App\Service\UserService;
use App\Repository\UserRepository;
use App\Service\PortfolioService;

class ProfileController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private PortfolioService $portfolioService;

    public function __construct(UserService $userService, UserRepository $userRepository, PortfolioService $portfolioService)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->portfolioService = $portfolioService;
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['id'];
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $projets = $this->portfolioService->getUserPortfolio($userId);

        include __DIR__ . '/../../template/profile.php';
    }

    public function updatePassword()
    {
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
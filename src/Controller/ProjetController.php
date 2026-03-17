<?php

namespace App\Controller;

use App\Service\PortfolioService;

class ProjetController
{
    private PortfolioService $portfolioService;


    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    public function index(?array $params = null)
    {
        $userId = $_SESSION['user']['id'] ?? null;

        $projets = $this->portfolioService->getUserPortfolio($userId);

        include __DIR__ . '/../../template/portfolio.php';
    }

    public function handleCreatePortfolio()
    {
        
        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
            try {
                // Utilisation du PortfolioService avec les 3 arguments requis
                // Attention : on passe $_POST['projets'] pour correspondre au formulaire HTML
                $this->portfolioService->createFullPortfolio(
                    $userId,
                    $_POST,   // Contient 'projects' et 'status'
                    $_FILES   // Contient les images
                );

                header('Location: /profile?success=1');
            } catch (\Exception $e) {
                header('Location: /portfolio?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }
}
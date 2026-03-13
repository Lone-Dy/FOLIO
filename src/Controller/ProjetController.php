<?php

namespace App\Controller;

use App\Service\PortfolioService;

class ProjetController
{
    private PortfolioService $PortfolioService;


    public function __construct(PortfolioService $PortfolioService)
    {
        $this->PortfolioService = $PortfolioService;
    }

    public function index(?array $params = null)
    {

        $projets = $this->PortfolioService->getUserPortfolio();

        include __DIR__ . '/../../template/portfolio.php';
    }

    public function handleCreatePortfolio()
    {
        
        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
            try {
                // Utilisation du PortfolioService avec les 3 arguments requis
                // Attention : on passe $_POST['projets'] pour correspondre au formulaire HTML
                $this->PortfolioService->createFullPortfolio(
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
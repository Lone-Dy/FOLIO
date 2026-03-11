<?php

namespace App\Controller;

use App\Service\ProjetService;

class ProjetController 
{
    private ProjetService $projetService;


    public function __construct(ProjetService $projetService)
    {
        $this->projetService = $projetService;
    }

    public function index(?array $params = null)
    {
   
        $projets = $this->projetService->getUserPortfolio();

        include __DIR__ . '/../../template/portfolio.php';
    }

    public function handleCreatePortfolio()
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
            $projetsData = $_POST['projets'] ?? [];

            $success = $this->projetService->createFullPortfolio($userId, $projetsData);

            if($success) {
                header('Location: /profile?success=portfolio_created');
            } else {
                header('Location: /portfolio?error=fail');
            }
            exit;
        }
    }
}
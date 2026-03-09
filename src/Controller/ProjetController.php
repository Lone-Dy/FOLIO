<?php

namespace App\Controller;

use App\Repository\ProjetRepository;

class ProjetController 
{
    private ProjetRepository $projetRepository;
    public function __construct(ProjetRepository $projetRepository)
    {
        $this->projetRepository = $projetRepository;
    }

    public function index(?array $params = null)
    {
        $projets = $this->projetService->getUserPortfolio();

        include __DIR__ . '/../../template/portfolio.php';
    }

}

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
}
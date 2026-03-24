<?php
namespace App\Controller;

use App\Service\PortfolioService;

Class GalerieController {

    private PortfolioService $portfolioService;

    public function __construct(PortfolioService $portfolioService) 
    {
        $this->portfolioService = $portfolioService;
    }

    public function index(?array $params = null) {

        // Récupèration de tous les portfolios publiés
        $galleryFeed = $this->portfolioService->getFullGallery();

        include __DIR__.'/../../template/galerie.php';
    }
}
?>
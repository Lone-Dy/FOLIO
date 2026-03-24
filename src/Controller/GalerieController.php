<?php
namespace App\Controller;

use App\Service\PortfolioService;

Class GalerieController {

    private GalerieController $portfolioService;

    public function __construct(GalerieController $portfolioService) 
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
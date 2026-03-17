<?php
namespace App\Controller;

use App\Service\PortfolioService;

class HomeController {

    private PortfolioService $portfolioService;

    public function __construct(PortfolioService $portfolioService) 
    {
        $this->portfolioService = $portfolioService;
    }

    // J'ajoute un null pour donner un valeur par défaut. 
    public function index(?array $params = null) {

        // Récupèration de tous les portfolios publiés
        $galleryFeed = $this->portfolioService->getGalleryFeed();

        include __DIR__.'/../../template/home_page.php';
    }
}
?>
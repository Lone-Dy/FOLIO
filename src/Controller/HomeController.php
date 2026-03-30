<?php
namespace App\Controller;

use App\Service\FolioService;
use App\Service\GalerieService;

class HomeController {

    private FolioService $folioService;
    private GalerieService $galerieService;

    public function __construct(FolioService $folioService, GalerieService $galerieService) 
    {
        $this->folioService = $folioService;
        $this->galerieService = $galerieService;
    }

    // J'ajoute un null pour donner un valeur par défaut. 
    public function index(?array $params = null) {

        // Récupèration de tous les portfolios publiés
        $galleryFeed = $this->galerieService->getGalleryFeed();
        include __DIR__.'/../../template/home_page.php';
    }
}
?>
<?php
namespace App\Controller;

use App\Service\GalerieService;

Class GalerieController {

    private GalerieService $galerieService;

    public function __construct(GalerieService $galerieService) 
    {
        $this->galerieService = $galerieService;
    }

    public function index(?array $params = null) {

        // Récupèration de tous les portfolios publiés
        $galleryFeed = $this->galerieService->getFullGallery();

        include __DIR__.'/../../template/galerie.php';
    }
}
?>
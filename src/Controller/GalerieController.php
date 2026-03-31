<?php
namespace App\Controller;

use App\Service\Renderer;
use App\Service\GalerieService;

Class GalerieController {

    private Renderer $renderer;
    private GalerieService $galerieService;

    public function __construct(Renderer $renderer, GalerieService $galerieService) 
    {
        $this->renderer = $renderer;
        $this->galerieService = $galerieService;
    }

    public function index(?array $params = null) {

        // Récupèration de tous les portfolios publiés
        $galleryFeed = $this->galerieService->getFullGallery();

        $this->renderer->render('home', $data);
    }
}
?>
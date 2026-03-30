<?php
namespace App\Controller;

use App\Service\Renderer;
use App\Service\GalerieService;

class HomeController {

    private Renderer $renderer;
    private GalerieService $galerieService;

    public function __construct(Renderer $renderer, GalerieService $galerieService) 
    {
        $this->renderer = $renderer;
        $this->galerieService = $galerieService;
    }

    // J'ajoute un null pour donner un valeur par défaut. 
    public function index(?array $params = null) {

        // Récupèration de tous les portfolios publiés
        $galleryFeed = $this->galerieService->getGalleryFeed();

            // Préparation des données pour le template
        $data = [
            'title' => 'Accueil - Folio',
            'description' => 'Plateforme de partage de portfolios créatifs.',
            'author' => 'Nom de l’auteur',
            'copyright' => 'Propriétaire du copyright et année',
            'robots' => 'index, follow',
            'galleryFeed' => $galleryFeed
    ];

    // Utilisation du Renderer pour afficher le template
    $this->renderer->render('home', $data);
    }
}
?>
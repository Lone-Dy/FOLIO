<?php
namespace App\Controller;

use App\Service\Renderer;

class E404Controller {

    private Renderer $renderer;

    public function __construct(Renderer $renderer) {
        
        $this->renderer = $renderer;
    }

    public function index(?array $params = null) {

        // Préparation des données pour le template
        $data = [
            'title' => '404 - Folio',
            'description' => 'Plateforme de partage de portfolios créatifs.',
            'author' => 'Nom de l’auteur',
            'copyright' => 'Propriétaire du copyright et année',
            'robots' => 'index, follow',
        ];

        // Utilisation du Renderer pour afficher le template
        $this->renderer->render('home', $data);
    }
}

?>
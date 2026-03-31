<?php

namespace App\Controller;

use App\Service\Renderer;

class ConditionController {

    private Renderer $renderer;

    public function __construct(Renderer $renderer) 
    {
        $this->renderer = $renderer;
    }

    public function index(?array $params = null) 
    {

        $data = [
            'title' => 'Conditions - Folio',
            'description' => 'Partie conditions utilisation du site Folio.',
            'author' => 'Nom de l’auteur',
            'copyright' => 'Propriétaire du copyright et année',
            'robots' => 'index, follow',
        ];

        $this->renderer->render('conditions-utilisation', $data);
    }

    public function mentions(?array $params = null) 
    {
        $data = [
            'title' => 'Mentions Légales - Folio',
            'description' => 'Mentions légales du site Folio.',
            'author' => 'Nom de l’auteur',
            'copyright' => 'Propriétaire du copyright et année',
            'robots' => 'index, follow',
        ];

        $this->renderer->render('mentions-legales', $data);
    }

    public function privacy(?array $params = null) 
    {
        $data = [
            'title' => 'Politique de Confidentialité - Folio',
            'description' => 'Politique de confidentialité du site Folio.',
            'author' => 'Nom de l’auteur',
            'copyright' => 'Propriétaire du copyright et année',
            'robots' => 'index, follow',
            
        ];

        $this->renderer->render('politique-confidentialite', $data);
    }
    
    }
?>
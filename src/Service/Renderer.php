<?php

namespace App\Service;

use App\Service\SecurityService;
use App\Exception\RenderException;

class Renderer {
    private string $templateDir;
    private SecurityService $securityService;

    public function __construct(string $templateDir, SecurityService $securityService) {
        $this->templateDir = $templateDir;
        $this->securityService = $securityService;
    }

    public function render(string $template, array $data = []): void {
        // Extraction des variables pour les rendre disponibles dans le template
        extract($data); // Convertit les clés du tableau  $data

        // Démarrage de la temporisation de sortie
        ob_start();

        // Inclusion du contenu spécifique au template
        include $this->templateDir . '/' . $template . '.php';

        // Récupération du contenu généré
        $ob_content = ob_get_clean();

        //Initialisation de $ob_modal pour éviter un warning PHP si la variable n'est pas passée dans $data
        $ob_modal = $ob_modal ?? '';

        // Inclusion du template
        include $this->templateDir . '/layout.php';
    }
}
<?php

namespace App\Controller;

use App\Service\Renderer;
use App\Service\FolioService;
use App\Service\FlashService;

class ProjetController {

    private Renderer $renderer;
    private FolioService $folioService;
    private FlashService $flashService;

    public function __construct(
        Renderer $renderer,
        FolioService $folioService,
        FlashService $flashService,
    ) {
        $this->renderer = $renderer;
        $this->folioService = $folioService;
        $this->flashService = $flashService;
    }

    // Affiche la page profile
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->flashService->addError("Vous devez être connecté pour créer un portfolio.");
            header('Location: /login');
            exit;
        }

        $data = [
            'title' => 'Accueil - Folio',
            'description' => 'Plateforme de partage de portfolios créatifs.',
            'author' => 'Nom de l’auteur',
            'copyright' => 'Propriétaire du copyright et année',
            'robots' => 'index, follow',
        ];

        $this->renderer->render('portfolio', $data);

        if (isset($_SESSION['user'])) {
            header('Location: /profile');
            exit;
        }

    }

    public function handleCreateFolio(): void 
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$userId) {
            $this->flashService->addError("Requête invalide.");
            header('Location: /portfolio');
            exit;
        }

        try {
            $this->folioService->createFullFolio($userId, $_POST, $_FILES);
            $this->flashService->addSuccess("Portfolio créé avec succès !");
            header('Location: /profile');
        } catch (\Exception $e) {
            $this->flashService->addError($e->getMessage());
            header('Location: /portfolio');
        }
        exit;
    }
}
?>
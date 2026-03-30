<?php

namespace App\Controller;

use App\Service\FolioService;
use App\Service\FlashService;

class ProjetController {
    private FolioService $folioService;
    private FlashService $flashService;

    public function __construct(
        FolioService $folioService,
        FlashService $flashService,
    ) {
        $this->folioService = $folioService;
        $this->flashService = $flashService;
    }

        // Affiche la page profile
    public function index(): void
    {
        if (isset($_SESSION['user'])) {
            header('Location: /profile');
            exit;
        }
        require __DIR__ . '/../../template/portfolio.php';
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
            header('Location: /projet');
        } catch (\Exception $e) {
            $this->flashService->addError($e->getMessage());
            header('Location: /portfolio');
        }
        exit;
    }
}
?>
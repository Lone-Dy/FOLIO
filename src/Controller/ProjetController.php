<?php

namespace App\Controller;

use App\Service\FolioService;
use App\Service\FlashService;
use App\Service\TemplateService;

class ProjetController {
    private FolioService $folioService;
    private FlashService $flashService;
    private TemplateService $templateService;

    public function __construct(
        FolioService $folioService,
        FlashService $flashService,
        TemplateService $templateService
    ) {
        $this->folioService = $folioService;
        $this->flashService = $flashService;
        $this->templateService = $templateService;
    }

    public function showPortfolio(): void {
        $this->templateService->render('portfolio.php');
    }

    public function handleCreateFolio(): void {
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
<?php

namespace App\Controller;

use App\Service\UserService;
use App\Service\PortfolioService;
use App\Service\FlashService;
use App\Service\FolioService;
use App\Service\ProjetService;
use App\Repository\UserRepository;


class ProjetController
{
    private UserService $userService;
    private PortfolioService $portfolioService;
    private FlashService $flashService;
    private FolioService $folioService;
    private ProjetService $projetService;
    private UserRepository $userRepository;

public function __construct( 

        UserService $userService, 
        PortfolioService $portfolioService,
        FlashService $flashService,
        FolioService $folioService,
        ProjetService $projetService,
        UserRepository $userRepository
        )

    {

        $this->userService = $userService;
        $this->portfolioService = $portfolioService;
        $this->flashService = $flashService;
        $this->folioService = $folioService;
        $this->projetService = $projetService;
        $this->userRepository = $userRepository;

    }

    // Affiche la liste des projets de l'utilisateur connecté
    public function index(?array $params = null)
    {
        $userId = $_SESSION['user']['id'] ?? null;

        $projets = $this->portfolioService->getUserPortfolio($userId);

        include __DIR__ . '/../../template/portfolio.php';
    }

    // Récupère les données $_POST et $_FILES pour lancer la création globale d'un portfolio via le service dédié
    public function handleCreatePortfolio(FlashService $flashService)
    {
            $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$userId) {
            $flashService->addError("Requête invalide.");
            header('Location: /portfolio');
            exit;
        }

        try {
            $this->portfolioService->createFullPortfolio($userId, $_POST, $_FILES);
            $flashService->addSuccess("Portfolio créé avec succès !");
            header('Location: /projet');
        } catch (\Exception $e) {
            $flashService->addError($e->getMessage());
            header('Location: /portfolio');
        }
        exit;
    }

    // Gère la soumission du formulaire d'édition d'un projet
    public function handleEditProjet(FlashService $flashService) 
    {
        
        $userId = $_SESSION['user']['id'] ?? null;
        $idProjet = $_POST['id_projet'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId && $idProjet) {
            try {
                $this->projetService->updateProjet($idProjet, $_POST, $_FILES);
                $flashService->addSuccess("Projet mis à jour avec succès !");
                header('Location: /projet');
            } catch (\Exception $e) {
                $flashService->addError($e->getMessage());
                header('Location: /projet/edit/' . $idProjet);
            }
            exit;
        }

        $flashService->addError("Requête invalide.");
        header('Location: /projet');
        exit;
    }

    // Gère l'exposition dans la galerie
    public function togglePublic(array $params)
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = "Action non autorisée.";
            header('Location: /projet');
            exit;
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, $_SERVER['SERVER_NAME']) === false) {
            die("Sécurité : Requête d'origine inconnue.");
        }

        $idFolio = isset($params[0]) ? (int)$params[0] : null;
        $userId = $_SESSION['user']['id'] ?? null;

        if(!$userId || !$idFolio) {
            header('Location: /login');
            exit;
        }

            try {
            $this->portfolioService->toggleProjectVisibility($idFolio, $userId);
                header('Location: /portfolio?success=status_updated');

            } catch (\Exception $e) {
                header('Location: /portfolio?error=' . urlencode($e->getMessage()));
            }
            exit;

        if ($idFolio && $userId) {

            try {
                $this->portfolioService->publishPortfolio($idFolio, $userId);
                header('Location: /projet?success=visibility_updated');
            } catch (\Exception $e) {
                header('Location: /profile?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
        header('Location: /projet');
        exit;
    }

    // Gère la suppression du portfolio
    public function delete(array $params)
    {
        // Interdit le GET. L'action doit venir d'un formulaire <form method="POST">
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = "Action non autorisée.";
            header('Location: /projet');
            exit;
        }

        // Vérifie que la requête vient bien du site
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, $_SERVER['SERVER_NAME']) === false) {
            die("Sécurité : Requête d'origine inconnue.");
        }

        $idFolio = isset($params[0]) ? (int)$params[0] : null;
        $userId = $_SESSION['user']['id'] ?? null;

        if ($idFolio && $userId) {

            $this->portfolioService->deletePortfolio($idFolio, $userId); 
            header('Location: /profile?success=deleted');
        } else {
            header('Location: /projet');
        }
        exit;
    }
}
?>
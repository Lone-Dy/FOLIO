<?php

namespace App\Controller;

use App\Service\PortfolioService;

class ProjetController
{
    private PortfolioService $portfolioService;


    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    // Affiche la liste des projets de l'utilisateur connecté
    public function index(?array $params = null)
    {
        $userId = $_SESSION['user']['id'] ?? null;

        $projets = $this->portfolioService->getUserPortfolio($userId);

        include __DIR__ . '/../../template/portfolio.php';
    }

    // Récupère les données $_POST et $_FILES pour lancer la création globale d'un portfolio via le service dédié
    public function handleCreatePortfolio()
    {
        
        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {

            try {
                $this->portfolioService->createFullPortfolio(
                    $userId,
                    $_POST,   // Contient 'projets' et 'status'
                    $_FILES   // Contient les images
                );

                header('Location: /projet?success=folio_created');
            } catch (\Exception $e) {
                header('Location: /portfolio?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }

    // Gère la soumission du formulaire d'édition d'un projet
    public function handleEditProjet()
    {
        $userId = $_SESSION['user']['id'] ?? null;
        $idProjet = $_POST['id_projet'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId && $idProjet) {

            try {
                $this->portfolioService->updateProjet((int)$idProjet, $_POST);
                header('Location: /profile?success=project_updated');
            } catch (\Exception $e) {
                header('Location: /profile?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }

    // Gère l'exposition dans la galerie
    public function togglePublic(array $params)
    {
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
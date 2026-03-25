<?php

namespace App\Service;

use App\Entity\Folio;
use App\Entity\Projet;

use App\Repository\FolioRepository;
use App\Repository\ProjetRepository;

use App\Service\MediaService;
use App\Service\FlashService;
use App\Service\TemplateService;

use PDO;
use Exception;

class FolioService {
    
    private PDO $pdo;
    private FolioRepository $folioRepo;
    private ProjetRepository $projetRepo;

    private MediaService $mediaService;
    private FlashService $flashService;
    private TemplateService $templateService;

    public function __construct(
        PDO $pdo,
        FolioRepository $folioRepo,
        ProjetRepository $projetRepo,
        MediaService $mediaService,
        FlashService $flashService,
        TemplateService $templateService,
    ) {
        $this->pdo = $pdo;
        $this->folioRepo = $folioRepo;
        $this->projetRepo = $projetRepo;
        $this->mediaService = $mediaService;
        $this->flashService = $flashService;
        $this->templateService = $templateService;
    }

    // Gère la création complète d'un portfolio
    public function createFullFolio(int $userId, array $data, array $files): void 
    {
        $this->validateFolioData($data);

        try {
            $this->pdo->beginTransaction();

            // Création du Folio
            $folio = new Folio();
            $folio->setTitre($data['titre'] ?? "Mon Portfolio")
                ->setDescription($data['description'] ?? "")
                ->setUserId($userId);

            $idFolio = $this->folioRepo->create($folio);

            // Création des projets et médias via MediaService
            foreach ($data['projets'] as $index => $projetData) {
                $projet = new Projet();
                $projet->setTitre($projetData['title'])
                    ->setType($projetData['type'])
                    ->setFolioId($idFolio);

                $idProjet = $this->projetRepo->create($projet);
                $this->mediaService->uploadProjectMedia($idProjet, $files, $index);
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
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
            $this->createFullFolio($userId, $_POST, $_FILES);
            $this->flashService->addSuccess("Portfolio créé avec succès !");
            header('Location: /projet');
        } catch (\Exception $e) {
            $this->flashService->addError($e->getMessage());
            header('Location: /portfolio');
        }
        exit;
    }

    private function validateFolioData(array $data): void 
    {

        if (!isset($data['projets']) || count($data['projets']) !== 3) {
            throw new \InvalidArgumentException("Un portfolio doit contenir exactement 3 projets.");
        }

        foreach ($data['projets'] as $projet) {
            if (empty($projet['type']) || empty($projet['title'])) {
                throw new \InvalidArgumentException("Tous les projets doivent avoir un type et un titre.");
            }
        }
    }

    public function getUserFolio(?int $userId = null)
    {
        if (!$userId) {
            return [];
        }

        // Récupère les folios de base
        $folios = $this->folioRepo->findByUser($userId); 

        // Pour chaque folio, recherche ses projets et les attache
        foreach ($folios as &$folio) {
            $folio['projets_lies'] = $this->projetRepo->findByFolio((int)$folio['id_folio']);
        }

        return $folios;
    }

    // Bouton "Publier mon portfolio"
    public function publishFolio(int $idFolio, int $userId): bool
    {
        $folio = $this->folioRepo->findById($idFolio);

        if (!$folio) {
            throw new Exception("Portfolio introuvable.");
        }

        // Vérification du portfolio s'il appartient bien à l'utilisateur
        $folio->setIsPublished(!$folio->getIsPublished());
        return $this->folioRepo->update($folio);
    }

    public function toggleFolioVisibility(int $folioId, int $userId): bool 
    {
        $folio = $this->folioRepo->findById($folioId);
        if (!$folio) throw new Exception("Folio introuvable.");

        $folio->setIsPublished(!$folio->getIsPublished());
        return $this->folioRepo->update($folio);
    }

    // Suppression du portfolio
    public function deleteFolio(int $idFolio, int $userId): bool
    {   

        // Suppression en BDD
        return $this->folioRepo->delete($idFolio);
    }
}
?>
<?php

namespace App\Service;

use App\Entity\Folio;
use App\Entity\Projet;
use App\Entity\Media;
use App\Entity\User;

use App\Repository\FolioRepository;
use App\Repository\ProjetRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;

use App\Service\MediaService;

use PDO;
use Exception;

class FolioService {
    
    private PDO $pdo;
    private FolioRepository $folioRepo;
    private ProjetRepository $projetRepo;
    private MediaRepository $mediaRepo;
    private MediaService $mediaService;

    public function __construct(
        PDO $pdo,
        FolioRepository $folioRepo,
        ProjetRepository $projetRepo,
        MediaRepository $mediaRepo,
        MediaService $mediaService
    ) {
        $this->pdo = $pdo;
        $this->folioRepo = $folioRepo;
        $this->projetRepo = $projetRepo;
        $this->mediaRepo = $mediaRepo;
        $this->mediaService = $mediaService;
    }

    // Gère la création complète d'un portfolio
    public function createFullFolio(int $userId, array $data, array $files): void
    {
        $this->validateFolioData($data);

        try {
            $this->pdo->beginTransaction();

            // Création du Folio
            $folio = new Folio();
            $folio->setTitre("Mon Portfolio")
                ->setDescription("Description par défaut")
                ->setCategorieFolio("Général");

            // Vérification du statut choisi dans le formulaire
            $isPublished = (isset($data['status']) && $data['status'] === 'published');
            $folio->setIsPublished($isPublished);

            // Adaptation du repo pour l'userId
            $this->folioRepo->createWithUser($folio, $userId);
            $idFolio = (int)$this->pdo->lastInsertId();

            // Création des projets
            foreach ($data['projets'] as $index => $projData) {
                $projet = new Projet();
                $type = $projData['type'] ?? ''; // Sécurise le bon nom de variable
                $projet->setType($projData['type']) // Utilise la variable $type sécurisée
                    ->setContenu($projData['title'] ?? '')
                    ->setOrdreAffichage((string)$index);

                // Adaptation du repo pour l'idFolio
                $this->projetRepo->createWithFolio($projet, $idFolio);
                $idProjet = (int)$this->pdo->lastInsertId();

                // Gestion des médias du projet
                $fileKey = "projet_" . $index . "_files";
                if (isset($files[$fileKey])) 
                {
                    $this->mediaService->uploadProjectMedia($idProjet, $files[$fileKey]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
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
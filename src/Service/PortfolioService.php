<?php

namespace App\Service;

use App\Entity\Folio;
use App\Entity\Projet;
use App\Entity\Media;

use App\Repository\FolioRepository;
use App\Repository\ProjetRepository;
use App\Repository\MediaRepository;

use PDO;
use Exception;

class PortfolioService
{
    private PDO $pdo;
    private FolioRepository $folioRepo;
    private ProjetRepository $projetRepo;
    private MediaRepository $mediaRepo;

    // INJECTION DE DÉPENDANCES

    public function __construct(
        PDO $pdo,
        FolioRepository $folioRepo,
        ProjetRepository $projetRepo,
        MediaRepository $mediaRepo
    ) {
        $this->pdo = $pdo;
        $this->folioRepo = $folioRepo;
        $this->projetRepo = $projetRepo;
        $this->mediaRepo = $mediaRepo;
    }

    // Gère la création complète d'un portfolio
    public function createFullPortfolio(int $userId, array $data, array $files): bool
    {

        // Contrôle de la règle des 3 projets
        if(!isset($data['projets']) || count($data['projets']) !== 3)
            {
                throw new Exception("Un portfolio doit contenir exactement 3 projets.");
            }

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
                if (isset($files[$fileKey])) {
                    $this->uploadProjectMedia($idProjet, $files[$fileKey]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // S'occupe du traitement des fichiers
    private function uploadProjectMedia(int $idProjet, array $fileArray): void
    {
        $uploadDir = __DIR__ . '/../../public/uploads/projets';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($fileArray['name'] as $k => $name) {
            if ($fileArray['error'][$k] === UPLOAD_ERR_OK) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = uniqid('media_') . '.' . $ext;

                if (move_uploaded_file($fileArray['tmp_name'][$k], $uploadDir . '/' . $newName)) {

                    $this->mediaRepo->create(
                        $idProjet,
                        'uploads/projets/' . $newName,
                        $fileArray['type'][$k],
                        $k,
                        $fileArray['size'][$k]
                    );
                }
            }
        }
    }

    public function updateProjet(int $idProjet, array $data): bool
    {
        $projet = $this->projetRepo->findById($idProjet);

        if (!$projet) {
            throw new Exception("Projet introuvable.");
        }

        if (isset($data['type'])) {
            $projet->setType($data['type']);
        }

        if(isset($data['title'])) {
            $projet->setContenu($data['title']);
        }

        return $this->projetRepo->update($projet);
    }
 
    public function getGalleryFeed(): array
    {
        return $this->folioRepo->findPublishedForGallery();
    }

    public function getFullGallery(): array 
    {
        // Récupère tous les folios publiés
        $folios = $this->folioRepo->findAllPublished();
        if (empty($folios)) return [];

        // Indexe par ID pour un accès rapide
        $indexedFolios = [];
        foreach ($folios as $f) {
            $f['projets'] = [];
            $indexedFolios[$f['id_folio']] = $f;
        }

        // Récupère TOUS les projets de ces folios en une seule requête
        $folioIds = array_keys($indexedFolios);
        $allProjets = $this->projetRepo->findByFolioIds($folioIds); // Nécessite une nouvelle méthode au repo

        $indexedProjets = [];
        foreach ($allProjets as $p) {
            $p['medias'] = [];
            $indexedProjets[$p['id_projet']] = $p;
        }

        // Récupère TOUS les médias de ces projets en une seule requête
        $projetIds = array_keys($indexedProjets);
        if (!empty($projetIds)) {
            $allMedias = $this->mediaRepo->findByProjetIds($projetIds); // Nécessite une nouvelle méthode au repo
            foreach ($allMedias as $m) {
                $indexedProjets[$m['id_projet']]['medias'][] = $m;
            }
        }

        // Réassemble le tout
        foreach ($indexedProjets as $p) {
            $indexedFolios[$p['id_folio']]['projets'][] = $p;
        }

        return array_values($indexedFolios);
    }

    public function toggleProjectVisibility($projetId, $userId)
    {
        return $this->folioRepo->toggleStatus($projetId);
    }

    // Récupère l'ensemble des données du portfolio (folio et projets) associé à un identifiant d'utilisateur
    public function getUserPortfolio(?int $userId = null)
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
    public function publishPortfolio(int $idFolio, int $userId): bool
    {
        $folio = $this->folioRepo->findById($idFolio);

        if (!$folio) {
            throw new Exception("Portfolio introuvable.");
        }

        // Vérification du portfolio s'il appartient bien à l'utilisateur
        $folio->setIsPublished(!$folio->getIsPublished());
        return $this->folioRepo->update($folio);
    }

    // Suppression du portfolio
    public function deletePortfolio(int $idFolio, int $userId): bool
    {   

        // Suppression en BDD
        return $this->folioRepo->delete($idFolio);
    }
}
?>
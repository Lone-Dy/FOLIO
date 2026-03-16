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

    // 1. INJECTION DE DÉPENDANCES
    
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

    public function createFullPortfolio(int $userId, array $data, array $files): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Création du Folio
            $folio = new Folio();
            $folio->setTitre("Mon Portfolio")
                ->setDescription("Description par défaut")
                ->setCategorieFolio("Général");

            // Adaptation du repo pour l'userId
            $this->folioRepo->createWithUser($folio, $userId);
            $idFolio = (int)$this->pdo->lastInsertId();

            // Création des projets
            foreach ($data['projets'] as $index => $projData) {
                $projet = new Projet();
                $projet->setType($projData['type'])
                    ->setContenu($projData['title'])
                    ->setOrdreAffichage((string)$index);
            
            // Adaptation du repo pour l'idFolio
            $this->projetRepo->createWithFolio($projet, $idFolio);
            $idProjet = (int)$this->pdo->lastInsertId();

            // Gestion des médias du projet
            $fileKey = "projet_" . $index . "_files";
            if (isset($files[$fileKey])){
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
    private function uploadProjectMedia(int $idProjet, array $fileArray): void
    {
        $uploadDir = __DIR__ . '/../../public/uploads/projets';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($fileArray['name'] as $k => $name) {
            if ($fileArray['error'][$k] === UPLOAD_ERR_OK) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = uniqid('media_') . '.' . $ext;
                
                if (move_uploaded_file($fileArray['tmp_name'][$k], $uploadDir . $newName)) {
                    
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
}


<?php

namespace App\Service;

use App\Entity\Folio;
use App\Entity\Projet;
use App\Repository\FolioRepository;
use App\Repository\ProjetRepository;
use PDO;
use Exception;

class PortfolioService
{
    private PDO $pdo;
    private FolioRepository $folioRepo;
    private ProjetRepository $projetRepo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->folioRepo = new FolioRepository($pdo);
        $this->projetRepo = new ProjetRepository($pdo);
    }

    /**
     * Crée le portfolio complet : Folio + Projets + Médias
     */
    public function createFullPortfolio(int $userId, array $data, array $files): bool
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Création du Folio (lié à l'utilisateur)
            $folio = new Folio();
            $folio->setTitre("Mon Portfolio")
                  ->setDescription("Description par défaut")
                  ->setCategorieFolio("Général");

            $this->folioRepo->createWithUser($folio, $userId);
            $idFolio = (int)$this->pdo->lastInsertId();

            // 2. Traitement des projets (Clé 'projects' envoyée par le formulaire)
            $projectsData = $data['projects'] ?? [];

            foreach ($projectsData as $index => $projData) {
                $projet = new Projet();
                $projet->setType($projData['type'] ?? 'web')
                       ->setContenu($projData['title'] ?? "Projet $index") // Titre stocké dans $contenu
                       ->setOrdreAffichage((string)$index);

                // Sauvegarde du projet lié au folio
                $this->projetRepo->createWithFolio($projet, $idFolio);
                $idProjet = (int)$this->pdo->lastInsertId();

                // 3. Gestion des fichiers pour ce projet
                $fileKey = "project_" . $index . "_files";
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

    /**
     * Gère l'upload et l'insertion en BDD des médias
     */
    private function uploadProjectMedia(int $idProjet, array $fileArray): void
    {
        // Chemin absolu vers le dossier public/uploads
        $uploadDir = __DIR__ . '/../../public/uploads/projets/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($fileArray['name'] as $k => $name) {
            if ($fileArray['error'][$k] === UPLOAD_ERR_OK) {
                
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $newName = uniqid('media_' . $idProjet . '_') . '.' . $extension;
                
                if (move_uploaded_file($fileArray['tmp_name'][$k], $uploadDir . $newName)) {
                    $sql = "INSERT INTO media (id_projet, cheminFichier, mediaType, ordreAffichage, poidsFichier) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        $idProjet,
                        'uploads/projets/' . $newName,
                        $fileArray['type'][$k],
                        $k,
                        $fileArray['size'][$k]
                    ]);
                }
            }
        }
    }
}
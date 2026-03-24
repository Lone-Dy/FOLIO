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

use PDO;
use Exception;

class MediaService {
    
    private PDO $pdo;
    private FolioRepository $folioRepo;
    private ProjetRepository $projetRepo;
    private MediaRepository $mediaRepo;

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


    // S'occupe du traitement des fichiers
    public function uploadProjectMedia(int $idProjet, array $fileArray): void
    {
        $uploadDir = __DIR__ . '/../../public/uploads/projets';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($fileArray['name'] as $k => $name) {
            if ($fileArray['error'][$k] === UPLOAD_ERR_OK) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = uniqid('media_') . '.' . $ext;

                if (move_uploaded_file($fileArray['tmp_name'][$k], $uploadDir . '/' . $newName)) 
                {

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
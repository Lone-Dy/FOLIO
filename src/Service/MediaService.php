<?php

namespace App\Service;

use App\Repository\MediaRepository;


use PDO;
use Exception;

class MediaService {
    
    private MediaRepository $mediaRepo;

    public function __construct(MediaRepository $mediaRepo) {
        
        $this->mediaRepo = $mediaRepo;
    }


    // S'occupe du traitement des fichiers
    public function uploadProjectMedia(int $idProjet, array $fileArray, array $files, int $index): void
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2 Mo

        if (!in_array($files['type'][$index], $allowedMimes)) {
            throw new \Exception("Type de fichier non autorisé.");
        }

        if ($files['size'][$index] > $maxSize) {
            throw new \Exception("Fichier trop volumineux (max 2 Mo).");
        }

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
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

class ProjetService {

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

}
?>
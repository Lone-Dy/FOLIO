<?php

namespace App\Service;


use App\Repository\FolioRepository;
use App\Repository\ProjetRepository;
use App\Repository\MediaRepository;

use PDO;
use Exception;

class GalerieService
{

    private FolioRepository $folioRepo;
    private ProjetRepository $projetRepo;
    private MediaRepository $mediaRepo;

    // INJECTION DE DÉPENDANCES

    public function __construct(
        FolioRepository $folioRepo,
        ProjetRepository $projetRepo,
        MediaRepository $mediaRepo
        ) {
            $this->folioRepo = $folioRepo;
            $this->projetRepo = $projetRepo;
            $this->mediaRepo = $mediaRepo;
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

}
?>
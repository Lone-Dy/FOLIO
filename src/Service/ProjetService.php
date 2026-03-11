<?php

namespace App\Service;

use App\Repository\ProjetRepository;
use App\Entity\Projet;

class ProjetService
{
    private ProjetRepository $projetRepository;
    public function __construct(ProjetRepository $projetRepository)
    {
        $this->projetRepository = $projetRepository;
    }

    // Récupération du portfolio complet de l'utilisateur

    public function getUserPortfolio(): array
    {
        $projets = $this->projetRepository->findAll();
        // Logique métier :
        // trier les projets / formater les images
        return $projets;
    }

    // Ajouter un nouveau projet

    public function addProjet(array $data): bool
    {
        $projet = new Projet();
        $projet->setType($data['type'] ?? 'image')
                ->setContenu($data['contenu'] ?? '')
                ->setOrdreAffichage($data['ordre'] ?? '0');
                
        return $this->projetRepository->create($projet);
    }

    // Limite des projets

    public function canAddProjet(int $userId): bool
    {
        $projets = $this->projetRepository->findAll();
        $count = $this->projetRepository->countByUserId($userId);
        return count($projets) < 3;
    }

    public function getPortfolio(): array
    {
        return $this->projetRepository->findAll();
    }

    // Création du portfolio

    public function createFullPortfolio(int $userId, array $projetsData): bool
    {
        // Vérification du nombre de projet actuel
        $currentCount = $this->projetRepository->countByUserId($userId);

        // Calcule si le rajout est possible
        $remainingSlots = 3 - $currentCount;

        if ($remainingSlots <= 0) {
            return false; // Si déjà 3 projets en plus
        }
        // Compte rendu de la vérification
        $limitedProjets = array_slice($projetsData, 0, $remainingSlots);

        foreach ($limitedProjets as $index => $data) {
            if (!empty($data['contenu'])) {
                $projet = new Projet();
                $projet->setType($data['type'] ?? 'image')
                ->setContenu($data['contenu'])
                ->setOrdreAffichage((string)$currentCount + $index);

                $this->projetRepository->create($projet, $userId);
            }
        }
        return true;
    }
}

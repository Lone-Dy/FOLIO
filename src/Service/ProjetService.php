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

    public function CreateProjet(array $data): bool
    {
        $projet = new Projet();
        $projet->setType($data['type'])
            ->setContenu($data['contenu'])
            ->setOrdreAffichage($data['ordre']);

        return $this->projetRepository->create($projet);
    }

    // Prepare et enregistre un nouveau projet

    public function addProjet(array $data): bool
    {
        $projet = new Projet();
        $projet->setType($data['type'] ?? 'image')
                ->setContenu($data['contenu'] ?? '')
                ->setOrdreAffichage($data['ordre'] ?? '0');
                
        return $this->projetRepository->create($projet);
    }

    public function getPortfolio(): array
    {
        return $this->projetRepository->findAll();
    }
}

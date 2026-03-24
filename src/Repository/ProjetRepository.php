<?php

namespace App\Repository;

use App\Entity\Projet;
use \PDO;

class ProjetRepository
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CRUD - CREATE

    public function create(Projet $projet, int $idFolio): bool
    {
        $sql = "INSERT INTO projet (type, contenu, ordre_affichage, id_folio)
                VALUES (:type, :contenu, :ordre, :id_folio)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'type' => $projet->getType(),
            'contenu' => $projet->getContenu(),
            'ordre' => $projet->getOrdreAffichage(),
            'id_folio' => $idFolio
        ]);
    }

    public function createWithFolio(Projet $projet, int $idFolio): bool
    {
        $sql = "INSERT INTO projet (type, contenu, ordre_affichage, id_folio)
                VALUES (:type, :contenu, :ordre, :id_folio)";
        return $this->pdo->prepare($sql)->execute([
            'type' => $projet->getType(),
            'contenu' => $projet->getContenu(),
            'ordre' => $projet->getOrdreAffichage(),
            'id_folio' => $idFolio
        ]);
    }
  
    // CRUD - READ

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM projet ORDER BY ordre_affichage ASC");
        $projets = [];
        while ($row = $stmt->fetch()) {
            $projet = new Projet();
            $projet->setIdProjet($row['id_projet'])
                ->setType($row['type'])
                ->setContenu($row['contenu'])
                ->setOrdreAffichage($row['ordre_affichage']);
            $projets [] = $projet;
        }
        return $projets;
    }

    public function findById(int $id): ?Projet 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projet WHERE id_projet = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return (new Projet())
            ->setIdProjet($row['id_projet'])
            ->setType($row['type'])
            ->setContenu($row['contenu'])
            ->setOrdreAffichage($row['ordre_affichage']);
    }

    // Ajout de la récupération par folio
    public function findByFolio(int $idFolio): array 
    {
        $sql = "SELECT * FROM projet WHERE id_folio = :id ORDER BY ordre_affichage ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idFolio]);
        $projets = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $projet = new \App\Entity\Projet();
            $projet->setIdProjet($row['id_projet'])
                ->setType($row['type'])
                ->setContenu($row['contenu'])
                ->setOrdreAffichage($row['ordre_affichage']);
            $projets[] = $projet;
        }
        return $projets; 

    }

    // Récupère tous les projets pour une liste d'IDs de folios
    public function findByFolioIds(array $folioIds): array
    {
        if (empty($folioIds)) return [];

        // Crée une chaîne de points d'interrogation (?,?,?) pour le IN
        $placeholders = implode(',', array_fill(0, count($folioIds), '?'));
        
        $sql = "SELECT * FROM projet WHERE id_folio IN ($placeholders) ORDER BY ordre_affichage ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($folioIds);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    // Compter les projets d'un utilisateur spécifique
    public function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(p.id_projet) FROM projet p 
                JOIN folio f ON p.id_folio = f.id_folio 
                WHERE f.id_user = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    // CRUD - UPDATE

    public function update(Projet $projet): bool
    {
        $sql = "UPDATE projet SET type = :type, contenu = :contenu, 
                ordre_affichage = :ordre WHERE id_projet = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'type'    => $projet->getType(),
            'contenu' => $projet->getContenu(),
            'ordre'   => $projet->getOrdreAffichage(),
            'id'      => $projet->getIdProjet()
        ]);
    }

    // CRUD - DELETE

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM projet WHERE id_projet = ?");
        return $stmt->execute([$id]);
    }
}
?>
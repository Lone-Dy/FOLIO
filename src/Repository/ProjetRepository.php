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

    public function create(Projet $projet): bool
    {
        $sql = "INSERT INTO projet (type, contenu, ordre_affichage)
                VALUES (:type, :contenu, :ordre)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'type' => $projet->getType(),
            'contenu' => $projet->getContenu(),
            'ordre' => $projet->getOrdreAffichage()
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
            $projet->setType($row['type'])
                ->setContenu($row['contenu'])
                ->setOrdreAffichage($row['ordre_affichage']);
            $projets [] = $projet;
        }
        return $projets;
    }

    // Compter les projets d'un utilisateur spécifique

    public function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM projet WHERE id_user = :userId";
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
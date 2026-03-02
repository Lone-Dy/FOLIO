<?php

namespace App\Repository;

use App\Entity\Folio;
use \PDO;

class FolioRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CRUD - READ

    public function create(Folio $folio): bool
    {
        $sql = "INSERT INTO folio (titre, description, categorie_folio) 
                VALUES (:titre, :description, :categorie)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre'       => $folio->getTitre(),
            'description' => $folio->getDescription(),
            'categorie'   => $folio->getCategorieFolio()
        ]);
    }

    // CRUD - READ

    public function findById(int $id): ?Folio
    {
        $stmt = $this->pdo->prepare("SELECT * FROM folio WHERE id_folio = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        $folio = new Folio();
        // Note: Il faudrait un setter pour l'id dans l'entité si vous voulez le réhydrater
        return $folio->setTitre($row['titre'])
                     ->setDescription($row['description'])
                     ->setCategorieFolio($row['categorie_folio']);
    }

// CRUD - UPDATE

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM folio");
        $folios = [];
        while ($row = $stmt->fetch()) {
            $folio = new Folio();
            $folio->setTitre($row['titre'])
                  ->setDescription($row['description'])
                  ->setCategorieFolio($row['categorie_folio']);
            $folios[] = $folio;
        }
        return $folios;
    }

    public function update(Folio $folio): bool
    {
        $sql = "UPDATE folio SET titre = :titre, description = :description, 
                categorie_folio = :categorie WHERE id_folio = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre'       => $folio->getTitre(),
            'description' => $folio->getDescription(),
            'categorie'   => $folio->getCategorieFolio(),
            'id'          => $folio->getIdFolio()
        ]);
    }

// CRUD - DELETE

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM folio WHERE id_folio = ?");
        return $stmt->execute([$id]);
    }
}

?>
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

    // CRUD - CREATE

    public function create(Folio $folio, int $userId): bool
    {
        $sql = "INSERT INTO folio (titre, description, categorie_folio, id_user) 
                VALUES (:titre, :description, :categorie, :user)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre'       => $folio->getTitre(),
            'description' => $folio->getDescription(),
            'categorie'   => $folio->getCategorieFolio(),
            'user'        => $userId
        ]);
    }

    public function createWithUser(Folio $folio, int $userId): bool {
        $sql = "INSERT INTO folio (titre, description, categorie_folio, id_user)
                VALUES (:titre, :description, :categorie, :user)";

        return $this->pdo->prepare($sql)->execute([
            'titre'       => $folio->getTitre(),
            'description' => $folio->getDescription(),
            'categorie'   => $folio->getCategorieFolio(),
            'user'        => $userId
        ]);        
    }

    // CRUD - READ

    public function findAll(): array 
    {
            $stmt = $this->pdo->query("SELECT * FROM folio");
            $folios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $folio = (new Folio())->setIdFolio($row['id_folio'])
                                      ->setTitre($row['titre'])
                                      ->setDescription($row['description'])
                                      ->setCategorieFolio($row['categorie']);
                $folios[] = $folio;
            }
            return $folios;
        
    }

    // Méthode pour récuperer le portfolio d'un utilisateur
    public function findByUser(int $userId): array 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM folio WHERE id_user = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?Folio
    {
        $stmt = $this->pdo->prepare("SELECT * FROM folio WHERE id_folio = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        $folio = new Folio();
        
        return $folio->setIdFolio($row['id_folio'])
                     ->setTitre($row['titre'])
                     ->setDescription($row['description'])
                     ->setCategorieFolio($row['categorie']);
    }

    // recherche du nom de l'utilisateur ou le titre du portfolio
    public function searchFolios(string $query): array {
    $sql = "SELECT * FROM folio 
            JOIN user ON id_folio = id_user 
            WHERE user.nom LIKE :q 
            OR user.prenom LIKE :q 
            OR titre LIKE :q";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':q', '%' . $query . '%');
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // CRUD - UPDATE

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
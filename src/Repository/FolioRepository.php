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

    // Insère une nouvelle ligne dans la table folio
    public function create(Folio $folio, int $userId): bool
    {
        $sql = "INSERT INTO folio (titre, description, categorie_folio, id_user, is_published) 
                VALUES (:titre, :description, :categorie, :user, :published)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre'       => $folio->getTitre(),
            'description' => $folio->getDescription(),
            'categorie'   => $folio->getCategorieFolio(),
            'user'        => $userId,
            'published'   => $folio->getIsPublished() ? 1 : 0
        ]);
    }

    public function createWithUser(Folio $folio, int $userId): bool 
    {
        $sql = "INSERT INTO folio (titre, description, categorie_folio, id_user, is_published)
                VALUES (:titre, :description, :categorie, :user, :published)";

        return $this->pdo->prepare($sql)->execute([
            'titre'       => $folio->getTitre(),
            'description' => $folio->getDescription(),
            'categorie'   => $folio->getCategorieFolio(),
            'published'   => $folio->getIsPublished() ? 1 : 0,
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
                                      ->setCategorieFolio($row['categorie_folio']);
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

    // Récupère un folio spécifique en fonction de son identifiant
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
                     ->setCategorieFolio($row['categorie_folio'])
                     ->setIsPublished((bool)$row['is_published']);
    }

    // Publication du portfolio dans la galerie
    public function findPublishedForGallery(): array
    {
        $sql = "SELECT f.*, u.nom, u.prenom, NULL as cover_image
            FROM folio f 
            JOIN user u ON f.id_user = u.id_user 
            WHERE f.is_published = 1 
            ORDER BY f.id_folio DESC"; // les plus récents en premier

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleStatus(int $id): bool
    {
        $sql = "UPDATE portfolio SET is_published = 1 - is_published WHERE id_folio = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    // Recherche le nom de l'utilisateur ou le titre du portfolio
    public function searchFolios(string $query): array 
    {
        $sql = "SELECT * FROM folio 
                JOIN user ON folio.id_user = user.id_user 
                WHERE user.nom LIKE :q 
                OR user.prenom LIKE :q 
                OR folio.titre LIKE :q";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':q', '%' . $query . '%'); // '%' cible les portfolios demandés
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CRUD - UPDATE

    // Met à jour les colonnes titre, description, la publication et catégorie d'un folio existant
    public function update(Folio $folio): bool
    {
        $sql = "UPDATE folio SET titre = :titre, description = :description, 
                categorie_folio = :categorie, is_published = :published WHERE id_folio = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre'       => $folio->getTitre(),
            'description' => $folio->getDescription(),
            'categorie'   => $folio->getCategorieFolio(),
            'published'   => $folio->getIsPublished() ? 1 : 0,
            'id'          => $folio->getIdFolio()
        ]);
    }

    // CRUD - DELETE

    // Modifie les informations d'un projet déjà enregistré
    public function delete(int $idFolio): bool
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Supprimer les médias associés aux projets de ce folio
            $sqlMedia = "DELETE m FROM media m 
                        INNER JOIN projet p ON m.id_projet = p.id_projet 
                        WHERE p.id_folio = :id";
            $this->pdo->prepare($sqlMedia)->execute(['id' => $idFolio]);

            // 2. Supprimer les projets liés au folio
            $sqlProjets = "DELETE FROM projet WHERE id_folio = :id";
            $this->pdo->prepare($sqlProjets)->execute(['id' => $idFolio]);

            // 3. Supprimer le folio lui-même
            $sqlFolio = "DELETE FROM folio WHERE id_folio = :id";
            $stmt = $this->pdo->prepare($sqlFolio);
            $result = $stmt->execute(['id' => $idFolio]);

            $this->pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}
?>
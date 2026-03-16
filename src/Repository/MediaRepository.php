<?php
namespace App\Repository;

use App\Entity\Media;
use \PDO;

class MediaRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CREATE

    public function create(int $idProjet, string $chemin, string $type, int $ordre, int $poids): bool 
    {
        $sql = "INSERT INTO media (id_projet, cheminFichier, mediaType, ordreAffichage, poidsFichier)
                VALUES (?, ?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([$idProjet, $chemin, $type, $ordre, $poids]);
    }

    // READ

    public function findByProjet(int $idProjet): array {
        $stmt = $this->pdo->prepare("SELECT * FROM media WHERE id_projet = ? ORDER BY ordreAffichage");
        $stmt->execute([$idProjet]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete(int $id): bool {
        return $this->pdo->prepare("DELETE FROM media WHERE id_media = ?")->execute([$id]);
    }

}
?>
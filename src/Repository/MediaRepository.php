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

    // Insère les métadonnées d'un fichier (chemin, type, poids, ordre) liées à un projet
    public function create(int $idProjet, string $chemin, string $type, int $ordre, int $poids): bool 
    {
        $sql = "INSERT INTO media (id_projet, cheminFichier, mediaType, ordreAffichage, poidsFichier)
                VALUES (?, ?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([$idProjet, $chemin, $type, $ordre, $poids]);
    }

    // READ

    // Liste tous les médias associés à un projet spécifique, triés par ordre d'affichage
    public function findByProjet(int $idProjet): array 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM media WHERE id_projet = ? ORDER BY ordreAffichage");
        $stmt->execute([$idProjet]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id) 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM media WHERE id_media = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE

    // Modifie les informations d'un média existant
    public function update(int $idMedia, string $chemin, int $ordre): bool 
    {
        $sql = "UPDATE media SET cheminFichier = ?, ordreAffichage = ? WHERE id_media = ?";
        return $this->pdo->prepare($sql)->execute([$chemin, $ordre, $idMedia]);
    }

    // DELETE
    public function delete(int $id): bool 
    {
        return $this->pdo->prepare("DELETE FROM media WHERE id_media = ?")->execute([$id]);
    }

}
?>
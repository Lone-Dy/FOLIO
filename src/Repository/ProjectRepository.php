<?php

namespace App\Repository;

use App\Entity\Project;
use \PDO;

class ProjectRepository
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CRUD - READ

    public function create(Project $project): bool
    {
        $sql = "INSERT INTO project (type, contenu, ordre_affichage)
    VALUES (:type, :contenu, :ordre)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'type' => $project->getType(),
            'contenu' => $project->getContenu(),
            'ordre' => $project->getOrdreAffichage()
        ]);
    }

    // CRUD - READ

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM project ORDER BY ordre_affichage ASC");
        $projects = [];
        while ($row = $stmt->fetch()) {
            $project = new Project();
            $project->setType($row['type'])
                ->setContenu($row['contenu'])
                ->setOrdreAffichage($row['ordre_affichage']);
            $projects[] = $project;
        }
        return $projects;
    }

    // CRUD - UPDATE

    public function update(Project $project): bool
    {
        $sql = "UPDATE project SET type = :type, contenu = :contenu, 
                ordre_affichage = :ordre WHERE id_project = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'type'    => $project->getType(),
            'contenu' => $project->getContenu(),
            'ordre'   => $project->getOrdreAffichage(),
            'id'      => $project->getIdProject()
        ]);
    }

    // CRUD - DELETE

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM project WHERE id_project = ?");
        return $stmt->execute([$id]);
    }
}
?>
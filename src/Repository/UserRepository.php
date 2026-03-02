<?php

namespace App\Repository;

use App\Entity\User;
use \PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CRUD - CREATE

    public function create(User $user): bool
    {
        $sql = "INSERT INTO user (nom, prenom, email, password, statut_compte, role) 
    VALUES (:nom, :prenom, :email, :password, :statut_compte, :role)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":nom", $user->getNom());
        $stmt->bindValue(":prenom", $user->getPrenom());
        $stmt->bindValue(":email", $user->getEmail());
        $stmt->bindValue(":password", $user->getPassword());
        $stmt->bindValue(":statut_compte", $user->getStatutCompte() ?? 'actif');
        $stmt->bindValue(":role", $user->getRole() ?? 'user');
        $result = $stmt->execute();
        return $result;
    }

    // CRUD - READ

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE id_user = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * from user");
        $users = [];
        while ($row = $stmt->fetch()) {
            $user = new User();
            $user->setNom($row['nom'])
                ->setPrenom($row['prenom'])
                ->setEmail($row['email'])
                ->setRole($row['role'])
                ->setStatutCompte($row['statut_compte']);

            $users[] = $user;
        }
        return $users;
    }

    // CRUD - UPDATE

public function update(User $user): bool {
        $sql = "UPDATE user SET 
                nom = :nom, 
                prenom = :prenom, 
                email = :email, 
                statut_compte = :statut, 
                role = :role 
                WHERE id_user = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom'    => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email'  => $user->getEmail(),
            'statut' => $user->getStatutCompte(),
            'role'   => $user->getRole(),
            'id'     => $user->getIdUser()
        ]);
    }

    // CRUD - DELETE

public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE id_user = ?");
        return $stmt->execute([$id]);
    }
}

?>
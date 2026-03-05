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

    /**
     * Centralise la création d'un objet User à partir d'un tableau SQL
     */
    private function hydrater(array $donnees): User
    {
        $user = new User();
        $user->setIdUtilisateur($donnees['id_user'] ?? null)
             ->setNom($donnees['nom'])
             ->setPrenom($donnees['prenom'])
             ->setEmail($donnees['email'])
             ->setAge((int)$donnees['age'])
             ->setMotDePasse($donnees['password'] ?? '')
             ->setStatutCompte($donnees['statut_compte'])
             ->setRole($donnees['role']);
        return $user;
    }

    // --- CREATE ---
    public function create(User $user): bool
    {
        $sql = "INSERT INTO user (nom, prenom, email, age, password, statut_compte, role) 
                VALUES (:nom, :prenom, :email, :age, :mdp, :statut, :role)";
    
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom'    => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':email'  => $user->getEmail(),
            ':age'    => $user->getAge(),
            ':mdp'    => $user->getMotDePasse(),
            ':statut' => $user->getStatutCompte() ?? 'actif',
            ':role'   => $user->getRole() ?? 'user'
        ]);
    }

    // --- READ ---
    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE id_user = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrater($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrater($row) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM user");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->hydrater($row);
        }
        return $users;
    }

    // --- UPDATE ---
    public function update(User $user): bool 
    {
        $sql = "UPDATE user SET nom = :nom, prenom = :prenom, email = :email, 
                statut_compte = :statut, role = :role WHERE id_user = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom'    => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email'  => $user->getEmail(),
            'statut' => $user->getStatutCompte(),
            'role'   => $user->getRole(),
            'id'     => $user->getIdUtilisateur()
        ]);
    }

    // --- DELETE ---
    public function delete(int $id): bool 
    {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE id_user = ?");
        return $stmt->execute([$id]);
    }
}
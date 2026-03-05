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

private function hydrater(array $donnees): User
{
    $user = new User();
    $user->setIdUtilisateur($donnees['id_user'] ?? null)
         ->setNom($donnees['nom'])
         ->setPrenom($donnees['prenom'])
         ->setEmail($donnees['email'])
         ->setAge((int)($donnees['age'] ?? 0))
         ->setPassword($donnees['password'] ?? '')
         ->setStatutCompte($donnees['statut_compte'] ?? 'actif')
         ->setRole($donnees['role'] ?? 'user');
    return $user;
}

public function create(User $user): bool
{
    $sql = "INSERT INTO user (nom, prenom, email, age, password, statut_compte, role) 
            VALUES (:nom, :prenom, :email, :age, :password, :statut, :role)";

    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        'nom'      => $user->getNom(),
        'prenom'   => $user->getPrenom(),
        'email'    => $user->getEmail(),
        'age'      => $user->getAge(),
        'password' => $user->getPassword(), // <--- On utilise 'password'
        'statut'   => $user->getStatutCompte() ?? 'actif',
        'role'     => $user->getRole() ?? 'user'
    ]);
}
    public function findById(int $id): ?User
    {
        // Correction : id_user (selon ton SQL)
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
}
?>
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

    // Méthode interne qui transforme un tableau de résultats SQL en un objet de l'entité User
    private function hydrater(array $donnees): User
    {
        $user = new User();
        $user->setIdUtilisateur($donnees['id_user'] ?? null)
             ->setNom($donnees['nom'])
             ->setPrenom($donnees['prenom'])
             ->setEmail($donnees['email'])
             ->setAge((int)($donnees['age'] ?? 0))
             ->setMotDePasse($donnees['mot_de_passe'] ?? '')
             ->setStatutCompte($donnees['statut_compte'] ?? 'actif')
             ->setRole($donnees['role'] ?? 'user')
             ->setBiographie($donnees['biographie'] ?? null)
             ->setPhotoProfil($donnees['photo_profile'] ?? 'default-avatar.png');
        return $user;
    }

    // CRUD - CREATE

    // Insère un nouvel utilisateur (inscription) dans la table user
    public function create(User $user): bool
    {
        $sql = "INSERT INTO user (nom, prenom, email, age, mot_de_passe, statut_compte, role)
                VALUES (:nom, :prenom, :email, :age, :mot_de_passe, :statut_compte, :role)";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':nom', $user->getNom(), PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $user->getPrenom(), PDO::PARAM_STR);
            $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(':age', $user->getAge(), PDO::PARAM_INT);
            $stmt->bindValue(':mot_de_passe', $user->getMotDePasse(), PDO::PARAM_STR);
            $stmt->bindValue(':statut_compte', $user->getStatutCompte(), PDO::PARAM_STR);
            $stmt->bindValue(':role', $user->getRole(), PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            error_log("Requête SQL: " . $sql);
            error_log("Données utilisateur: " . print_r([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'age' => $user->getAge(),
                'mot_de_passe' => $user->getMotDePasse(),
                'statut_compte' => $user->getStatutCompte(),
                'role' => $user->getRole()
            ], true));
            return false;
        }
    }

    // CRUD - READ

    // Recherche un utilisateur par son identifiant unique ou son adresse email
    public function findAll(): array 
    {
        $stmt = $this->pdo->query("SELECT * FROM user");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->hydrater($row);
        }
        return $users;
    }

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

    public function emailExists(string $email): bool // Doublon email
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;

    }
    
    // CRUD - UPDATE

    // Met à jour l'ensemble des informations de profil d'un utilisateur
    public function update(User $user): bool
    {
        $sql = "UPDATE user SET 
            nom = :nom, 
            prenom = :prenom, 
            email = :email, 
            age = :age, 
            mot_de_passe = :mot_de_passe, 
            statut_compte = :statut, 
            role = :role,
            biographie = :bio,
            photo_profile = :photo 
            WHERE id_user = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom'      => $user->getNom(),
            'prenom'   => $user->getPrenom(),
            'email'    => $user->getEmail(),
            'age'      => $user->getAge(),
            'mot_de_passe' => $user->getMotDePasse(),
            'statut'   => $user->getStatutCompte(),
            'role'     => $user->getRole(),
            'bio'      => $user->getBiographie(),
            'photo'    => $user->getPhotoProfil(),
            'id'       => $user->getIdUtilisateur()
        ]);
    }

    public function updatePassword(int $userId, string $newPassword): bool
    {
        $sql = "UPDATE user SET mot_de_passe = :password WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':password' => $newPassword,
            ':id' => $userId
        ]);
    }

    // DELETE 

    public function delete(int $userId): bool 
    {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE id_user = :id");

        return $stmt->execute([':id' => $userId]);
    }
}
?>
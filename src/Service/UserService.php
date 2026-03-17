<?php

namespace App\Service;

use App\Repository\UserRepository;
use Exception;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // Vérifie les informations de connexion
    public function authenticate(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);

        // L'utilisateur n'a pas de compte
        if(!$user) {
            return 'USER_NOT_FOUND';
        }

        // L'utilisateur a un compte mais le mot de passe est mauvais
        if (!password_verify($password, $user->getPassword())) {
            return 'INVALID_PASSWORD';
        }

        return $user;
    }

    // Permet la mise à jour sécurisée du mot de passe en vérifiant d'abord l'ancien avant de hacher et d'enregistrer le nouveau
    public function changeUserPassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user || !password_verify($oldPassword, $user->getPassword())) {
            throw new \Exception("L'ancien mot de passe est incorrect.");
        }

        $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));

        return $this->userRepository->update($user); // Utilise ta méthode CRUD update
    }

    // Met à jour l'adresse email de l'utilisateur après une vérification de sécurité par mot de passe
    public function updateUserEmail(int $userId, string $password, string $newEmail) : bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new \Exception("Mot de passe incorrect.");
        }

        $user->setEmail($newEmail);
        return $this->userRepository->update($user);
    }
}

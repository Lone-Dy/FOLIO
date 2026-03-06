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

    public function changeUserPassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user || !password_verify($oldPassword, $user->getPassword())) {
            throw new \Exception("L'ancien mot de passe est incorrect.");
        }

        $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));

        return $this->userRepository->update($user); // Utilise ta méthode CRUD update
    }
}

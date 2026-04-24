<?php
namespace App\Service;

use App\Repository\UserRepository;

class SecurityService {

    private UserRepository $userRepo;

    public function __construct(UserRepository $repo) {
        $this->userRepo = $repo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $username, string $password): bool {
        $user = $this->userRepo->findById($username);
        
        if ($user && password_verify($password, $user->getMotDePasse())) {
            $_SESSION['user'] = [
                'id' => $user->getIdUtilisateur(),
                'name' => $user->getNom(),
                'role' => $user->getRole()
            ];
            return true;
        }
        return false;
    }

    public function logout(): void {
        unset($_SESSION['user']);
        session_destroy();
    }

    public function getUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public function isAdmin(): bool {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 0;
    }
}
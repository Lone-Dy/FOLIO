<?php

namespace App\Controller;

use App\Repository\UserRepository;

class ProfileController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index() 
    {
        if (session_status() === PHP_SESSION_NONE) { 
            session_start(); 
        }

        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $user = $this->userRepository->findById($userId);

        if (!$user) {
            session_destroy();
            header('Location: /login');
            exit;
        } // <--- CETTE ACCOLADE ÉTAIT MANQUANTE

        // Le chemin vers ton fichier template
        include __DIR__ . '/../../template/profile.php';
    }
}
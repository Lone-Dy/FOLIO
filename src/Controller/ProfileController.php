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

        // On récupère l'ID stocké dans la session lors du login
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $user = $this->userRepository->findById($userId);

        if (!$user) {
            // Si l'utilisateur n'est pas trouvé, on nettoie la session
            session_destroy();
            header('Location: /login');
            exit;
        }

        // Le contrôleur charge la vue (le fichier HTML)
        include __DIR__ . '/../../template/profile.php';
    }
}
?>
<?php

namespace App\Controller;

use App\Service\UserService;
use App\Service\FlashService;
use App\Service\UserProfileService;

use App\Repository\UserRepository;


class ProfileController
{
    private UserProfileService $userProfileService;
    private FlashService $flashService;

    public function __construct(
        UserProfileService $userProfileService, 
        FlashService $flashService) 
        
    {
        $this->userProfileService = $userProfileService;
        $this->flashService = $flashService;
    }

    public function index() {
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $projets = $this->userProfileService->getUserFolio($userId);
        include __DIR__ . '/../../template/profile.php';
    }
}

?>
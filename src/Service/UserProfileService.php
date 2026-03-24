<?php

namespace App\Service;

use App\Entity\User;

use App\Service\GalerieService;
use App\Service\FlashService;
use App\Service\FolioService;
use App\Service\ProjetService;

use App\Repository\UserRepository;

use Exception;

class UserProfileService
{
    private FolioService $folioService;
    private ProjetService $projetService;

    public function __construct(FolioService $folioService, ProjetService $projetService) {
        $this->folioService = $folioService;
        $this->projetService = $projetService;
    }

    public function getUserFolio(int $userId): array {
        return $this->folioService->getUserFolio($userId);
    }
}

<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FlashService;

Class ProfileService {

    private UserRepository $userRepo;
    private FlashService $flashService;

    public function __construct(

        UserRepository $userRepo,
        FlashService $flashService
    ) 
    {
        $this->userRepo = $userRepo;
        $this->flashService = $flashService;
    }

    public function updateProfile(int $userId, array $data, array $files = []): bool
    {
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            $this->flashService->addError("Utilisateur introuvable.");
            return false;
        }

        // Mise à jour des champs
        $user->setNom($data['nom'] ?? $user->getNom())
             ->setPrenom($data['prenom'] ?? $user->getPrenom())
             ->setEmail($data['email'] ?? $user->getEmail())
             ->setBiographie($data['biographie'] ?? $user->getBiographie());

        // Gestion de la photo de profil
        if (!empty($files['photo_profil']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            $filename = uniqid() . '_' . basename($files['photo_profil']['name']);
            if (move_uploaded_file($files['photo_profil']['tmp_name'], $uploadDir . $filename)) {
                $user->setPhotoProfil($filename);
            }
        }

        return $this->userRepo->update($user);
    }

    public function changePassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        $user = $this->userRepo->findById($userId);
        if (!$user || !password_verify($oldPassword, $user->getMotDePasse())) {
            $this->flashService->addError("Ancien mot de passe incorrect.");
            return false;
        }

        $user->setMotDePasse(password_hash($newPassword, PASSWORD_BCRYPT));
        return $this->userRepo->updatePassword($userId, $user->getMotDePasse());
    }
}
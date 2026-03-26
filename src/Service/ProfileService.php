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

    // Retourne les exigences pour le mot de passe
    public function getPasswordRequirements(): array
    {
        return [
            'min_length' => 12,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_number' => true,
            'require_special' => true,
            'special_chars' => '!@#$%^&*()_+-=[]{}|;:,.<>?'
        ];
    }

    // Met à jour le profil utilisateur
    public function updateProfile(User $user, array $userData, array $files): bool
    {
        // Validation des données
        if (isset($userData['email']) && !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->flashService->addError("L'adresse email n'est pas valide.");
            return false;
        }

        // Mise à jour des champs
        $user->setNom(htmlspecialchars(trim($userData['nom'] ?? $user->getNom()), ENT_QUOTES, 'UTF-8'))
             ->setPrenom(htmlspecialchars(trim($userData['prenom'] ?? $user->getPrenom()), ENT_QUOTES, 'UTF-8'))
             ->setEmail($userData['email'] ?? $user->getEmail())
             ->setAge((int)($userData['age'] ?? $user->getAge()))
             ->setBiographie(htmlspecialchars(trim($userData['biographie'] ?? $user->getBiographie()), ENT_QUOTES, 'UTF-8'));

        // Gestion de la photo de profil
        if (!empty($files['photo_profil']) && $files['photo_profil']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($files['photo_profil']['name']));

            if (move_uploaded_file($files['photo_profil']['tmp_name'], $uploadDir . $filename)) {
                // Suppression de l'ancienne photo si elle existe
                if ($user->getPhotoProfil()) {
                    $oldFile = $uploadDir . $user->getPhotoProfil();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $user->setPhotoProfil($filename);
            }
        }

        return $this->userRepo->update($user);
    }

    // Change le mot de passe de l'utilisateur
    public function changePassword(User $user, string $currentPassword, string $newPassword, string $confirmPassword): bool 
    {
        $requirements = $this->getPasswordRequirements();

        // Vérification du mot de passe actuel
        if (!password_verify($currentPassword, $user->getMotDePasse())) {
            $this->flashService->addError("Le mot de passe actuel est incorrect.");
            return false;
        }

        // Vérification de la confirmation
        if ($newPassword !== $confirmPassword) {
            $this->flashService->addError("Les nouveaux mots de passe ne correspondent pas.");
            return false;
        }

        // Validation du nouveau mot de passe
        if (strlen($newPassword) < $requirements['min_length']) {
            $this->flashService->addError("Le mot de passe doit contenir au moins {$requirements['min_length']} caractères.");
            return false;
        }

        if ($requirements['require_uppercase'] && !preg_match('/[A-Z]/', $newPassword)) {
            $this->flashService->addError("Le mot de passe doit contenir au moins une majuscule.");
            return false;
        }

        if ($requirements['require_lowercase'] && !preg_match('/[a-z]/', $newPassword)) {
            $this->flashService->addError("Le mot de passe doit contenir au moins une minuscule.");
            return false;
        }

        if ($requirements['require_number'] && !preg_match('/[0-9]/', $newPassword)) {
            $this->flashService->addError("Le mot de passe doit contenir au moins un chiffre.");
            return false;
        }

        if ($requirements['require_special'] && !preg_match('/['.preg_quote($requirements['special_chars'], '/').']/', $newPassword)) {
            $this->flashService->addError("Le mot de passe doit contenir au moins un caractère spécial ({$requirements['special_chars']}).");
            return false;
        }

        // Mise à jour du mot de passe
        $user->setMotDePasse(password_hash($newPassword, PASSWORD_DEFAULT));
        return $this->userRepo->update($user);
    }

    // Supprime le compte utilisateur
    public function accountDeletion(User $user, string $passwordConfirmation): bool
    {
        // Vérification du mot de passe
        if (!password_verify($passwordConfirmation, $user->getMotDePasse())) {
            $this->flashService->addError("Mot de passe incorrect. La suppression a été annulée.");
            return false;
        }

        // Suppression de l'utilisateur
        if ($this->userRepo->delete($user->getId())) {
            $this->flashService->addSuccess("Votre compte a été supprimé avec succès.");
            return true;
        }

        $this->flashService->addError("Une erreur est survenue lors de la suppression.");
        return false;
    }  
}
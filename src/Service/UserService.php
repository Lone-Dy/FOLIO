<?php

namespace App\Service;

use App\Entity\User;

use App\Service\FlashService;
use App\Service\UserProfileService;

use App\Repository\UserRepository;

use Exception;

class UserService
{
    private UserService $userService;
    private UserProfileService $userProfileService;
    private FlashService $flashService;
    private UserRepository $userRepository;

    public function __construct(
        UserService $userService, 
        FlashService $flashService,
        UserRepository $userRepository
        )    

    {
        $this->userService = $userService;
        $this->flashService = $flashService;
        $this->userRepository = $userRepository;
    }

    // Vérifie les informations de connexion
    public function authenticate(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception("Utilisateur introuvable.");
        }

        if (!password_verify($password, $user->getMotDePasse())) {
            throw new \Exception("Mot de passe incorrect.");
        }

        return $user;
    }

    public function update()
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$userId) {
            $flashService->addError("Requête invalide.");
            header('Location: /profile');
            exit;
        }

        try {
            $this->userProfileService->updateProfile($_SESSION['user']['id'], $_POST, $_FILES['avatar'] ?? null);
            $this->flashService->addSuccess("Profil mis à jour !");
        } catch (\Exception $e) {
            $this->flashService->addError($e->getMessage());
        }
        header('Location: /profile');
        exit;
    }

    public function updateAvatar()
    {

        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
            $user = $this->userRepository->findById($userId);

            if ($user && isset($_FILES['avatar'])) {
                $this->userService->updateProfile($user, [], $_FILES['avatar']);
                header('Location: /profile?success=1');
                exit;
            }
        }
        
        header('Location: /profile?error=avatar_failed');
        exit;
    }

    public function updatePassword()
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
            try {
                $this->userService->changeUserPassword($userId, $_POST['ancien_mdp'], $_POST['nouveau_mdp']);
                header('Location: /profile?success=1');
            } catch (\Exception $e) {
                header('Location: /profile?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }

    // Met à jour l'adresse email de l'utilisateur après une vérification de sécurité par mot de passe
    public function updateUserEmail(int $userId, string $password, string $newEmail): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user || !password_verify($password, $user->getMotDePasse())) {
            throw new \Exception("Mot de passe incorrect.");
        }

        $user->setEmail($newEmail);
        return $this->userRepository->update($user);
    }

    public function updateUserProfile(User $user, array $data, ?array $avatarFile): bool
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) throw new Exception("Utilisateur introuvable.");

        if (isset($data['nom'])) $user->setNom($data['nom']);
        if (isset($data['prenom'])) $user->setPrenom($data['prenom']);
        if (isset($data['email'])) $user->setEmail($data['email']);

        if ($avatarFile) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $mimeType = mime_content_type($avatarFile['tmp_name']);

            if (in_array($mimeType, $allowedMimeTypes)) {
                $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $extension_securisee = $extensions[$mimeType];
                $newName = bin2hex(random_bytes(8)) . '.' . $extension_securisee;
                $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
                $destination = $uploadDir . $newName;

                if (move_uploaded_file($avatarFile['tmp_name'], $destination)) {
                    $oldPhoto = $user->getPhotoProfil();
                    if ($oldPhoto && $oldPhoto !== 'default-avatar.png') {
                        $oldFilePath = $uploadDir . $oldPhoto;
                        if (file_exists($oldFilePath)) unlink($oldFilePath);
                    }
                    $user->setPhotoProfil($newName);
                }
            }
        }

        return $this->userRepository->update($user);
    }

    // Permet la mise à jour sécurisée du mot de passe en vérifiant d'abord l'ancien avant de hacher et d'enregistrer le nouveau
    public function changeUserPassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user || !password_verify($oldPassword, $user->getMotDePasse())) {
            throw new \Exception("L'ancien mot de passe est incorrect.");
        }

        $user->setMotDePasse(password_hash($newPassword, PASSWORD_BCRYPT));

        return $this->userRepository->update($user);
    }

}

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
        if (!$user) {
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

        return $this->userRepository->update($user);
    }

    // Met à jour l'adresse email de l'utilisateur après une vérification de sécurité par mot de passe
    public function updateUserEmail(int $userId, string $password, string $newEmail): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new \Exception("Mot de passe incorrect.");
        }

        $user->setEmail($newEmail);
        return $this->userRepository->update($user);
    }

    public function updateProfile(User $user, array $data, ?array $avatarFile): bool
    {
        $user->setNom($data['nom'] ?? $user->getNom())
            ->setPrenom($data['prenom'] ?? $user->getPrenom())
            ->setBiographie(!empty($data['biographie']) ? trim($data['biographie']) : null);

        if ($avatarFile && $avatarFile['error'] === UPLOAD_ERR_OK) {

            // Vérification de sécurité
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($avatarFile['tmp_name']);

            if (in_array($mimeType, $allowedMimeTypes)) {
                $extension = pathinfo($avatarFile['name'], PATHINFO_EXTENSION);
                $newName = bin2hex(random_bytes(8)) . '.' . $extension; // Nom plus sécurisé et unique
                $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
                $destination = $uploadDir . $newName;

                if (move_uploaded_file($avatarFile['tmp_name'], $destination)) {

                    // upprimer l'ancienne photo pour ne pas encombrer le serveur
                    $oldPhoto = $user->getPhotoProfil();
                    if ($oldPhoto && $oldPhoto !== 'default-avatar.png') {
                        $oldFilePath = $uploadDir . $oldPhoto;
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    $user->setPhotoProfil($newName);
                }
            }
        }
    }
}

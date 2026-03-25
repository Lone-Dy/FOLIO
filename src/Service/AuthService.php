<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FlashService;


Class AuthService {

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

    // --- INSCRIPTION ---
    public function register(array $userData, array $files): bool
    {
        // 1. Validation des données
        if ($this->userRepo->emailExists($userData['email'])) {
            $this->flashService->addError("Cet email est déjà utilisé.");
            return false;
        }

        if ($userData['password'] !== $userData['confirm_password']) {
            $this->flashService->addError("Les mots de passe ne correspondent pas.");
            return false;
        }

        // 2. Création de l'utilisateur
        $user = new User();
        $user->setNom($userData['nom'])
            ->setPrenom($userData['prenom'])
            ->setEmail($userData['email'])
            ->setAge((int)$userData['age'])
            ->setMotDePasse(password_hash($userData['password'], PASSWORD_BCRYPT))
            ->setRole('user')
            ->setStatutCompte('actif');

        // 3. Gestion de la photo de profil
        if (isset($files['photo_profil']) && $files['photo_profil']['error'] === UPLOAD_ERR_OK) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($fileInfo, $files['photo_profil']['tmp_name']);
            finfo_close($fileInfo);

            if (!in_array($mime, $allowedMimes)) {
                $this->flashService->addError("Type de fichier non autorisé.");
                return false;
            }

            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($files['photo_profil']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;

            if (!move_uploaded_file($files['photo_profil']['tmp_name'], $destination)) {
                $this->flashService->addError("Erreur lors de l'upload de la photo.");
                return false;
            }

            $user->setPhotoProfil($filename);
        }

        // 4. Sauvegarde en base
        return $this->userRepo->create($user);
    }

    // --- CONNEXION ---
    public function login(string $email, string $password): ?User
    {
        $user = $this->userRepo->findByEmail($email);

        if ($user->getStatutCompte() !== 'actif') {
            $this->flashService->addError("Votre compte est désactivé.");
            return null;
        }

        if (!$user || !password_verify($password, $user->getMotDePasse())) {
            $this->flashService->addError("Email ou mot de passe incorrect.");
            return null;
        }

        return $user;
    }

    // --- DÉCONNEXION ---
    public function logout(): void
    {
        session_destroy();
        unset($_SESSION);
    }

    // --- MISE À JOUR DU PROFIL ---
    public function updateProfile(int $userId, array $userData, array $files): bool
    {
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            return false;
        }

        // Mise à jour des champs
        $user->setNom($userData['nom'] ?? $user->getNom())
            ->setPrenom($userData['prenom'] ?? $user->getPrenom())
            ->setEmail($userData['email'] ?? $user->getEmail())
            ->setAge((int)($userData['age'] ?? $user->getAge()))
            ->setBiographie($userData['biographie'] ?? $user->getBiographie());

        // Gestion de la photo de profil
        if (isset($files['photo_profil']) && $files['photo_profil']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            $filename = uniqid() . '_' . basename($files['photo_profil']['name']);
            move_uploaded_file($files['photo_profil']['tmp_name'], $uploadDir . $filename);
            $user->setPhotoProfil($filename);
        }

        return $this->userRepo->update($user);
    }

    // --- MISE À JOUR DU MOT DE PASSE ---
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

    // --- AFFICHE LES INFORMATIONS DE L'UTILISATEUR ---
    public function getUserById(int $userId): ?User
    {
        return $this->userRepo->findById($userId);
    }
}
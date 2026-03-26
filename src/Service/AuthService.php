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

    // Gére l'inscription
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

        // 3. Sauvegarde en base
        return $this->userRepo->create($user);
    }

    // Vérification des mots de passe
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

    // Gére la connexion
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
        
        $this->flashService->addSuccess("Connexion réussie !");
        return $user;
    }

    // Gére la reconnexion
    public function getCurrentUser(): ?User 
    {
        if (!isset($_SESSION['user']['id']))
            {
                return null;
            }
        return $this->userRepo->findById($_SESSION['user']['id']);
    }

    // Gére la déconnexion
    public function logout(): void
    {
        session_destroy();
        unset($_SESSION);
    }

}
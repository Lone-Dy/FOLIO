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
    public function register(array $userData): bool
    {
        // 1. Validation des données
        $errors = [];

        // Validation du nom et prénom
        if (!isset($userData['nom']) || !ctype_alpha(str_replace(['-', ' '], '', $userData['nom']))) {
            $errors[] = "Le nom contient des caractères non autorisés."; //ctype_alpha = retourne TRUE si tous les caractères de la chaîne text sont des lettres
        }

        if (!isset($userData['prenom']) || !ctype_alpha(str_replace(['-', ' '], '', $userData['prenom']))) {
            $errors[] = "Le prénom contient des caractères non autorisés.";
        }

        // Validation de l'email
        if (!isset($userData['email']) || !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) { //filter_var = vérifie la présence de chaînes de caractères telles que des adresses e-mail
            $errors[] = "L'adresse email n'est pas valide.";
        } elseif ($this->userRepo->emailExists($userData['email'])) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        // Validation de l'âge
        if (!isset($userData['age']) || !is_numeric($userData['age']) || $userData['age'] < 18) {
            $errors[] = "L'âge doit être un nombre valide (minimum 18 ans).";
        }

        // Validation du mot de passe
        if (!isset($userData['password']) || strlen($userData['password']) < 12) {
            $errors[] = "Le mot de passe doit contenir au moins 12 caractères.";
        } elseif ($userData['password'] !== ($userData['password_confirmation'] ?? '')) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        // Si des erreurs existent, les enregistrer en session
        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode('<br>', $errors);
            return false;
        }

        // 2. Création de l'utilisateur
        try {
            $user = new User();
            $user->setNom(htmlspecialchars($userData['nom']))
                 ->setPrenom(htmlspecialchars($userData['prenom']))
                 ->setEmail(htmlspecialchars($userData['email']))
                 ->setAge((int)$userData['age'])
                 ->setMotDePasse(password_hash($userData['password'], PASSWORD_BCRYPT))
                 ->setRole('user')
                 ->setStatutCompte('actif');

            // 3. Sauvegarde en base
            return $this->userRepo->create($user);
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Une erreur est survenue lors de la création du compte.";
            error_log("Erreur lors de l'inscription: " . $e->getMessage());
            return false;
        }
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

            // Vérification si l'utilisateur existe
            if (!$user) {
                $this->flashService->addError("Email ou mot de passe incorrect.");
                return null;
            }

            // Vérification du statut
            if ($user->getStatutCompte() !== 'actif') {
                $this->flashService->addError("Votre compte est désactivé.");
                return null;
            }

            // Vérification du mot de passe
            if (!password_verify($password, $user->getMotDePasse())) {
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
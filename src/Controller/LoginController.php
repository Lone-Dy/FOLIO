<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;

class LoginController
{

    private $userRepository;
    private $userService;

    // le Repository au constructeur
    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    // Affiche la page de connexion/inscription
    public function index(?array $params = null)
    {
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }
        include __DIR__ . '/../../template/login_page.php';
    }

    // Traite le formulaire de connexion
    public function handleLogin(?array $params = null) // La vérification (Connexion)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $authResult = $this->userService->authenticate($email, $password);

            if ($authResult instanceof \App\Entity\User) {

                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id' => $authResult->getIdUtilisateur(),
                    'email' => $authResult->getEmail()
                ];

                // Redirection vers le home
                header('Location: /');
                exit;
            } else {

                $errorCode = strtolower($authResult);
                header("Location: /login?error=$errorCode");
                exit;
            }
        }
    }
    
    // Traite l'inscription d'un nouvel utilisateur
    public function handleRegister()  // La création (Inscription)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];

        if(strlen($password) < 12 ||                   // 12 caractères minimum
            !preg_match('@[A-Z]@', $password) ||        // Au moins une majuscule
            !preg_match('@[a-z]@', $password) ||        // Au moins une minuscule
            !preg_match('@[0-9]@', $password) ||        // Au moins un chiffre
            !preg_match('@[^\w]@', $password)           // Au moins un caractère spécial
        ) {
            header('Location: /login?error=weak_password#register-section');
            exit;
        }

            // Vérification de la case RGPD
        if (!isset($_POST['accept_conditions'])) {
            header('Location: /login?error=rgpd#register-section');
            exit;
        }

            // Stopper l'inscription des deux personnes utilisant la même adresse mail
            $existingUser = $this->userRepository->findByEmail($_POST['email']);
            if ($existingUser) {
                header('Location: /login?error=email_exists#register-section');
                exit;
            }

            // Création de l'entité User
            $user = new User();
            $user->setNom($_POST['nom'])
                ->setPrenom($_POST['prenom'])
                ->setEmail($_POST['email'])
                ->setAge((int)$_POST['age'])
                // On hache le mot de passe pour la sécurité
                ->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT))
                ->setRole('user')
                ->setStatutCompte('actif');

            // Appel au Repository pour l'insertion
            $success = $this->userRepository->create($user);

            if ($success) {

                $newUser = $this->userRepository->findByEmail($user->getEmail());

                $_SESSION['user'] = [
                    'id' => $newUser->getIdUtilisateur(),
                    'email' => $newUser->getEmail()
                ];

                // Redirection vers la page profile
                header('Location: /profile?success=welcome&new=1');
                exit;
            } else {
                header('Location: /login?error=reg_fail#register-section'); // Le #register-section à la fin de l'URL permet de faire descendre la page directement sur le formulaire d'inscription après le rechargement.
                exit;
            }
        }
    }

    // Détruit la session et redirige vers l'accueil
    public function logout()
    {
        session_destroy();
        unset($_SESSION);
        header('Location: /home'); // ou /login
        exit;
    }
}
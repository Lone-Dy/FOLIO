<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

class LoginController
{

    private $userRepository;

    // le Repository au constructeur
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(?array $params)
    {
        // Affiche la page de connexion/inscription
        include __DIR__ . '/../../template/login_page.php';
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // 1. je cherche l'utilisateur par son email
            $user = $this->userRepository->findByEmail($email);

            // 2. je vérifie si l'utilisateur existe et si le mot de passe correspond au hash en BDD
            if ($user && password_verify($password, $user->getPassword())) {

                // 3. Authentification réussie : j'ouvre la session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
$_SESSION['user'] = [
    'id' => $user->getIdUtilisateur(), // On récupère l'id_user de la BDD
    'email' => $user->getEmail()
];

                // Redirection vers le profile
                header('Location: /profile');
                exit;
            } else {
                // 4. Erreur : je redirige avec un message d'erreur
                header('Location: /login?error=1');
                exit;
            }
        }
    }

    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification de la case RGPD
            if (!isset($_POST['accept_conditions'])) {
                die("Vous devez accepter les conditions d'utilisation.");
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
                // Redirection vers la page profile
                header('Location: /profile');
                exit;
            } else {
                echo "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>
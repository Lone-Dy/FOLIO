<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

class LoginController {

    private $userRepository;

    // On passe le Repository au constructeur
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function index(?array $params) {
        // Affiche la page de connexion/inscription
        include __DIR__.'/../../template/login_page.php';
    }

    public function handleRegister() {
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
                // Redirection vers la page d'accueil ou de succès
                header('Location: /home');
                exit;
            } else {
                echo "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>
<?php
// Initialisation
session_start();
require_once(__DIR__ . '/../src/Entity/User.php');
require_once(__DIR__ . '/../src/Repository/UserRepository.php');

$repository = new \App\Repository\UserRepository($pdo);

// On suppose que l'ID est stocké en session après la connexion
$id_session = $_SESSION['user'] ?? null;

if (!$id_session) {
    header('Location: /login.php');
    exit;
}

$repository = new \App\Repository\UserRepository($pdo);
$user = $repository->findById($id_session);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}

include_once(__DIR__ . '/view/header-login.php');
?>

<main>
    <h1>Détail de mon compte</h1>
    
    <div class="utilisateur-info">
        <p><strong>Nom :</strong> <?= $user->getNom() . ' ' . $user->getPrenom() ?></p>
        <p><strong>Email :</strong> <?= $user->getEmail() ?></p>
    </div>

    <button type="button" id="ouvreDialog">Modifier mon mot de passe</button>

    <dialog id="mdpDialog">
        <form method="POST" action="/update-password">
            <h2>Changer le mot de passe</h2>
            
            <label for="ancien_mdp">Ancien mot de passe</label>
            <input type="password" name="ancien_mdp" id="ancien_mdp" required>

            <label for="nouveau_mdp">Nouveau mot de passe</label>
            <input type="password" name="nouveau_mdp" id="nouveau_mdp" required>

            <div class="dialog-actions">
                <button type="submit">Valider</button>
                <button type="button" id="fermeDialog">Annuler</button>
            </div>
        </form>
    </dialog>
</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
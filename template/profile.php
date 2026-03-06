<?php
include_once(__DIR__ . '/view/header.php');
?>

<main>

    <?php if (isset($_GET['success'])): ?>
        <p style="color: green; background: #eaffea; padding: 10px; border: 1px solid green;">
            Votre mot de passe a été modifié avec succès.
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red; background: #ffeaea; padding: 10px; border: 1px solid red;">
            Erreur : <?= htmlspecialchars($_GET['error']) ?>
        </p>
    <?php endif; ?>

    <h1>Détail de mon compte</h1>

    <div class="utilisateur-info">
        <p><strong>Nom :</strong> <?= $user->getNom() . ' ' . $user->getPrenom() ?></p>
        <p><strong>Email :</strong> <?= $user->getEmail() ?></p>
    </div>

    <button type="button" id="ouvreDialog">Modifier mon mot de passe</button>

    <dialog id="mdpDialog">
        <form method="POST" action="/profile/updatePassword">
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
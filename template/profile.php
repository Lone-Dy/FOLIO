<?php
include_once(__DIR__ . '/view/header.php');
?>

<main>
    <header class="profile-header">
        <div class="profile-avatar">
            <img src="/assets/img/default-avatar.png" alt="Avatar">
        </div>
        <h1 class="profile-name"><?= htmlspecialchars($user->getNom() . ' ' . $user->getPrenom()) ?></h1>
        <p class="profile-bio">Directeur Artistique & Designer</p>
        
        <div class="profile-actions">
            <button class="btn-apple" onclick="mdpDialog.showModal()">Paramètres</button>
        </div>
    </header>

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
    <section class="pinterest-grid">
        <?php foreach ($projects as $project): ?>
            <article class="project-card">
                <div class="project-media">
                    <img src="<?= htmlspecialchars($project->getContenu()) ?>" alt="Projet">
                </div>
                <div class="project-info">
                    <span><?= htmlspecialchars($project->getType()) ?></span>
                </div>
            </article>
        <?php endforeach; ?>
        
        <a href="/project/add" class="project-card add-card">
            <div class="add-content">
                <span class="plus-icon">+</span>
                <p>Ajouter un projet</p>
            </div>
        </a>
    </section>
</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
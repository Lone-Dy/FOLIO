<?php
include_once(__DIR__ . '/view/header-profil.php');
?>

<main class="profile-container">
    <aside class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar">
                <img src="/assets/img/default-avatar.png" alt="Avatar de <?= htmlspecialchars($user->getPrenom()) ?>">
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?></h1>

            <div class="profile-details">
                <div class="detail-item">
                    <span class="label">Email</span>
                    <span class="value"><?= htmlspecialchars($user->getEmail()) ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn-secondary" id="ouvreDialog">Sécurité & Compte</button>
            </div>
        </div>
    </aside>

    <section class="portfolio-section">
        <header class="section-header">
            <div class="header-text">
                <h2>Mes Travaux</h2>
                <p>Gérez vos projets exposés.</p>
            </div>
            <a href="/project/add" class="btn-primary">+ Ajouter</a>
        </header>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Mise à jour effectuée.</div>
        <?php endif; ?>

        <div class="pinterest-grid">
            <?php if (!empty($projets)): ?>
                <?php foreach ($projets as $project): ?>
                    <article class="project-card">
                        <div class="project-media">
                            <img src="<?= htmlspecialchars($project->getContenu()) ?>" alt="Projet">
                            <div class="project-overlay">
                                <div class="overlay-actions">
                                    <button class="btn-action">Éditer</button>
                                </div>
                            </div>
                        </div>
                        <div class="project-info">
                            <h3><?= htmlspecialchars($project->getType()) ?></h3>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>Votre portfolio est vide.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<dialog id="mdpDialog" class="modern-dialog">
    <form method="POST" action="/profile/updatePassword">
        <h2>Paramètres de sécurité</h2>
        <div class="field">
            <label for="ancien_mdp">Ancien mot de passe</label>
            <input type="password" name="ancien_mdp" id="ancien_mdp" required>
        </div>
        <div class="field">
            <label for="nouveau_mdp">Nouveau mot de passe</label>
            <input type="password" name="nouveau_mdp" id="nouveau_mdp" required>
        </div>
        <div class="dialog-actions">
            <button type="submit" class="btn-submit">Valider</button>
            <button type="button" id="fermeDialog" class="btn-text">Fermer</button>
        </div>
    </form>
    <form method="POST" action="/profile/updateEmail">
        <h3>Changer l'adresse mail</h3>
        <div class="field">
            <label>Nouvel email</label>
            <input type="email" name="nouveau_email" required>
        </div>
        <div class="field">
            <label>Confirmez avec votre mot de passe</label>
            <input type="password" name="password_confirm" required>
        </div>
        <button type="submit" class="btn-submit">Mettre à jour l'email</button>
    </form>
</dialog>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
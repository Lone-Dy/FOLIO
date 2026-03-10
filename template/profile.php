<?php
include_once(__DIR__ . '/view/header.php');
?>

<main class="profile-container">
    <aside class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar">
                <img src="/assets/img/default-avatar.png" alt="Avatar">
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?></h1>
            <p class="profile-role">Directeur Artistique & Designer</p>
            
            <div class="profile-details">
                <div class="detail-item">
                    <span>Email</span>
                    <strong><?= htmlspecialchars($user->getEmail()) ?></strong>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn-secondary" id="ouvreDialog">Modifier le mot de passe</button>
            </div>
        </div>
    </aside>

    <section class="portfolio-section">
        <div class="section-header">
            <h2>Mon Portfolio</h2>
            <a href="/project/add" class="btn-primary">+ Ajouter un projet</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Mot de passe modifié avec succès.</div>
        <?php endif; ?>

        <div class="pinterest-grid">
            <?php if (!empty($projets)): ?>
                <?php foreach ($projets as $project): ?>
                    <article class="project-card">
                        <div class="project-media">
                            <img src="<?= htmlspecialchars($project->getContenu()) ?>" alt="Projet">
                            <div class="project-overlay">
                                <button class="btn-view">Voir</button>
                            </div>
                        </div>
                        <div class="project-info">
                            <h3><?= htmlspecialchars($project->getType()) ?></h3>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <dialog id="mdpDialog" class="modern-dialog">
        <form method="POST" action="/profile/updatePassword">
            <h2>Sécurité du compte</h2>
            <div class="field">
                <label for="ancien_mdp">Ancien mot de passe</label>
                <input type="password" name="ancien_mdp" id="ancien_mdp" required>
            </div>
            <div class="field">
                <label for="nouveau_mdp">Nouveau mot de passe</label>
                <input type="password" name="nouveau_mdp" id="nouveau_mdp" required>
            </div>
            <div class="dialog-actions">
                <button type="submit" class="btn-primary">Enregistrer</button>
                <button type="button" id="fermeDialog" class="btn-text">Annuler</button>
            </div>
        </form>
    </dialog>
</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
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
                <button class="btn-submit" onclick="document.getElementById('mdpDialog').showModal()">Modifier le mot de passe</button>
            </div>
        </div>
    </aside>
    <section class="portfolio-section">
        <header class="section-header">
            <div class="header-text">
                <h2>Mon Portfolio</h2>
                <button class="btn-folio" onclick="window.location.href='/projet'">Gérez vos projets exposés</button>
            </div>
        </header>
    <form action="/projet/handleCreatePortfolio" method="POST" class="portfolio-form" enctype="multipart/form-data" id="portfolioForm">
        <h2>Configurer mon Portfolio</h2>
        <p class="subtitle">Importez vos 3 meilleurs travaux pour pouvoir publier votre portfolio.</p>

        <div class="projects-grid">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="project-card-edit" data-project-index="<?= $i ?>">
                    <header class="project-header">
                        <span>Projet #<?= $i ?></span>
                        <input type="text" name="projets[<?= $i ?>][title]" class="project-title-input" placeholder="Titre du projet..." required>

                        <select name="projets[<?= $i ?>][type]" class="project-type-select">
                            <option value="web">Développement Web</option>
                            <option value="design">Design Graphique</option>
                            <option value="photo">Photographie</option>
                            <option value="video">Vidéo</option>
                        </select>
                    </header>

                    <div class="drop-zone" id="drop-zone-<?= $i ?>">
                        <span class="drop-zone-prompt">Glissez vos fichiers ou cliquez ici (Max 5)</span>
                        <input type="file" name="projet_<?= $i ?>_files[]" class="drop-zone-input" accept="image/*,video/*" multiple>
                        <div class="media-list" id="mediaList<?= $i ?>"></div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="form-actions">
            <button type="submit" name="status" value="draft" class="btn-secondary" class="btn-submit">
                Enregistrer le brouillon
            </button>
            <button type="submit" name="status" value="published" id="publishBtn" class="btn-submit" disabled>
                Publier le Portfolio
            </button>
        </div>
        <section class="my-portfolios">

            <?php
            if (isset($projets) && !empty($projets)):
            ?>

                <h3>Votre Portfolio Actuel</h3>
                <div class="portfolio-list">
                    <?php foreach ($projets as $item): ?>
                        <div class="portfolio-item">
                            <span><strong>Folio #<?= htmlspecialchars($item['id_folio']) ?></strong> : <?= htmlspecialchars($item['titre'] ?? 'Sans titre') ?></span>

                            <a href="/projet/delete/<?= $item['id_folio'] ?>"
                                class="btn-delete"
                                onclick="return confirm('Attention : cela supprimera tous les projets et images associés. Confirmer ?');"
                                style="color:red; margin-left:15px; text-decoration:none;">
                                Supprimer définitivement
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p><strong>Info :</strong> Vous n'avez pas encore de portfolio créé. Utilisez le formulaire ci-dessus pour commencer.</p>
                </div>
            <?php endif; ?>
        </section>
    </section>
</main>

<?php if (isset($_GET['new']) && $_GET['new'] == '1'): ?>
    <dialog id="welcomeDialog" class="welcome-modal">
        <div class="modal-content">
            <h2>Bienvenue, <?= htmlspecialchars($user->getPrenom()) ?> !</h2>
            <p>Votre compte a été créé avec succès.<br>Votre aventure sur FOLIO commence maintenant.</p>
            <div class="next-steps">
                <button class="btn-submit" onclick="closeWelcome()">C'est parti !</button>
            </div>
        </div>
    </dialog>
<?php endif; ?>

<dialog id="mdpDialog" class="modern-dialog">
    <form method="POST" action="/profile/updatePassword">
        <h2>Modifier le mot de passe</h2>
        <div class="field">
            <label for="ancien_mdp">Ancien mot de passe</label>
            <input type="password" name="ancien_mdp" id="ancien_mdp" required>
        </div>
        <div class="field">
            <label for="nouveau_mdp">Nouveau mot de passe</label>
            <input type="password" name="nouveau_mdp" id="nouveau_mdp" required>
        </div>
        <div class="dialog-actions">
            <button type="submit" class="btn-submit">Mettre à jour</button>
            <button type="button" id="fermeDialog" class="btn-text" onclick="this.closest('dialog').close()">Fermer</button>
        </div>
    </form>
</dialog>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
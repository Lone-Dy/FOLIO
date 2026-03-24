<?php
include_once(__DIR__ . '/view/header-profil.php');
?>

<?php if (isset($_SESSION['flash_messages']['success'])): ?>
    <div class="alert alert-success">
        <?php foreach ($_SESSION['flash_messages']['success'] as $message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['flash_messages']['error'])): ?>
    <div class="alert alert-danger">
        <?php foreach ($_SESSION['flash_messages']['error'] as $message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main class="profile-container">
    <aside class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar">
                <img src="/assets/img/default-avatar.jpg" alt="Avatar de <?= htmlspecialchars($user->getPrenom()) ?>">
                <button class="btn-edit" onclick="document.getElementById('avatarDialog').showModal()">Modifier la photo</button>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?></h1>

            <div class="profile-details">
                <div class="detail-item">
                    <span class="label">Email</span>
                    <span class="value"><?= htmlspecialchars($user->getEmail()) ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Ma Bio</span>
                    <p class="bio-text">
                        <?= $user->getBiographie() ? nl2br(htmlspecialchars($user->getBiographie())) : "<i>Aucune biographie rédigée.</i>" ?>
                    </p>
            </div>
            </div>

            <div class="profile-actions">
                <button class="btn-folio" onclick="document.getElementById('editProfileDialog').showModal()">Modifier mon profil</button>
                <button class="btn-folio" onclick="document.getElementById('mdpDialog').showModal()">Modifier le mot de passe</button>
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
            <button type="submit" name="status" value="draft" class="btn-folio">
                Enregistrer le brouillon
            </button>
            <button type="submit" name="status" value="published" id="publishBtn" class="btn-submit" disabled>
                Publier le Portfolio
            </button>
        </div>
    </form>
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

<dialog id="editProfileDialog" class="modern-dialog">
    <form method="POST" action="/profile/update" enctype="multipart/form-data">
        <h2>Modifier mes informations</h2>
        
        <div class="field">
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($user->getPrenom()) ?>" required>
        </div>

        <div class="field">
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user->getNom()) ?>" required>
        </div>

        <div class="field">
            <label for="biographie">Biographie</label>
            <textarea name="biographie" id="biographie" rows="5" placeholder="Parlez-nous de vous..."><?= htmlspecialchars($user->getBiographie() ?? '') ?></textarea>
        </div>

        <div class="dialog-actions">
            <button type="submit" class="btn-submit">Enregistrer les modifications</button>
            <button type="button" class="btn-folio" onclick="this.closest('dialog').close()">Annuler</button>
        </div>
    </form>
</dialog>

<dialog id="avatarDialog" class="modern-dialog">
    <form method="POST" action="/profile/updateAvatar" enctype="multipart/form-data">
        <h2>Changer ma photo de profil</h2>
        
        <div class="field">
            <input type="file" name="avatar" accept="image/*" required>
        </div>

        <div class="dialog-actions">
            <button type="submit" class="btn-submit">Enregistrer</button>
            <button type="button" class="btn-folio" onclick="this.closest('dialog').close()">Annuler</button>
        </div>
    </form>
</dialog>

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
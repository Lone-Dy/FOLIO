
    <?php if (isset($_SESSION['flash_messages']['success'])): ?>
        <div class="alert alert-success">
            <?php foreach ($_SESSION['flash_messages']['success'] as $message): ?>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endforeach; unset($_SESSION['flash_messages']['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_messages']['error'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($_SESSION['flash_messages']['error'] as $message): ?>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endforeach; unset($_SESSION['flash_messages']['error']); ?>
        </div>
    <?php endif; ?>

<div class="profile-container">
 
    <!-- PARTIE INFORMATIONS PROFIL -->

    <aside class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar"> 
                <img src="/assets/img/default-avatar.jpg" alt="Avatar de <?= htmlspecialchars($user->getPrenom()) ?>">
                <button class="btn-edit" onclick="document.getElementById('avatarDialog').showModal()">Modifier la photo</button>
            </div>
            <h2 class="profile-name"><?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?></h2>

            <div class="profile-details">
                <div class="detail-item">
                    <span class="label">Email</span>
                    <span class="value"><?= htmlspecialchars($user->getEmail()) ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Ma Bio</span>
                    <p class="bio-text">
                        <span class="value"><?= nl2br(htmlspecialchars($user->getBiographie() ?? '')) ?></span>
                    </p>
            </div>
            </div>

            <div class="profile-actions">
                <button class="btn-folio" onclick="document.getElementById('editProfileDialog').showModal()">Modifier mon profil</button>
                <button class="btn-folio" onclick="document.getElementById('mdpDialog').showModal()">Modifier le mot de passe</button>
                <button class="btn-folio" onclick="document.getElementById('accountDialog').showModal()">Supprimer mon compte</button>
            </div>

        </div>
    </aside>

    <!-- PARTIE DASHBOARD -->

    <header class="portfolio-header">
        <h2>Gestion de mon Portfolio</h2>
        <p class="subtitle">Organisez et publiez vos projets pour la galerie.</p>

    <?php if (empty($projets)): ?>
        <div class="empty-state">
            <p>Vous n'avez pas encore de portfolio.</p>
            <button class="btn-submit" onclick="window.location.href='/projet'">Créez-en un ici</button>
        </div>
    <?php else: ?>

    <?php if (isset($projets) && !empty($projets)): ?>

    <?php foreach ($projets as $folio): ?>

        <div class="portfolio-item card">
            <div class="portfolio-projects-edit">
                <h3>Modifier les projets de ce portfolio :</h3>

                <?php if (!empty($folio['projets_lies'])): ?>
                    <div class="edit-projects-grid" style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($folio['projets_lies'] as $projet): ?>

                <form action="/projet/handleEditProjet" method="POST" class="edit-projet-form" style="display: flex; gap: 10px; align-items: center;">
                    <input type="hidden" name="id_projet" value="<?= $projet->getIdProjet() ?>">

                    <span class="project-order">Projet #<?= htmlspecialchars($projet->getOrdreAffichage()) ?></span>

                    <input type="text" name="title" value="<?= htmlspecialchars($projet->getContenu()) ?>" required placeholder="Titre du projet">

                    <select name="type">
                        <option value="web" <?= $projet->getType() === 'web' ? 'selected' : '' ?>>Développement Web</option>
                        <option value="design" <?= $projet->getType() === 'design' ? 'selected' : '' ?>>Design Graphique</option>
                        <option value="photo" <?= $projet->getType() === 'photo' ? 'selected' : '' ?>>Photographie</option>
                        <option value="video" <?= $projet->getType() === 'video' ? 'selected' : '' ?>>Vidéo</option>
                    </select>

                    <button type="submit" class="btn-submit btn-small">Mettre à jour</button>

                </form>

                <?php endforeach; ?>
            </div>

                <?php else: ?>
                    <p><em>Aucun projet n'est lié à ce portfolio.</em></p>
                <?php endif; ?>

        </div> 
                
    <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-info">
            <p><strong>Info :</strong> Vous n'avez pas encore de portfolio créé. Utilisez le formulaire ci-dessus pour commencer.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($projets as $folio): ?>
        <div class="portfolio-card">
            <div class="card-image">

                <?php if (!empty($folio['image_url'])): ?>
                    <img src="<?= htmlspecialchars($folio['image_url']) ?>" alt="Aperçu de <?= htmlspecialchars($folio['titre']) ?>">
                <?php else: ?>
                    <div class="placeholder-image">Pas d'aperçu disponible</div>
                <?php endif; ?>

            </div>
            <h3><?= htmlspecialchars($folio['titre']) ?></h3>
            <p class="card-description">
                <?= mb_strimwidth(htmlspecialchars($folio['description']), 0, 80, "...") ?>
            </p>

            <div class="card-content">
                <span class="status-badge <?= $folio['is_published'] ? 'status-public' : 'status-draft' ?>">
                <?= $folio['is_published'] ? '● Public' : '○ Brouillon' ?>
                </span>
            </div>

            <div class="card-actions">
                <button href="/projet/togglePublic/<?= $folio['id_folio'] ?>" class="btn-secondary">
                    <?= $folio['is_published'] ? 'Retirer' : 'Publier' ?>
                </button>

                <button href="/projet/delete/<?= $folio['id_folio'] ?>" class="btn-secondary" onclick="return confirm('Supprimer définitivement ce portfolio ?')">
                    Supprimer
                </button>
            </div>
        </div>
    <?php endforeach; ?>

    <?php endif; ?>
    </header>
</div>

<!-- Message de bienvenue -->

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

<!-- Dialogue pour modifier les informations du profil -->

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

<!-- Dialogue pour modifier la photo de profil -->

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

<!-- Dialogue pour modifier le mot de passe -->

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
            <button type="button" id="fermeDialog" class="btn-folio" onclick="this.closest('dialog').close()">Fermer</button>
        </div>
    </form>
</dialog>

<!-- Dialogue pour suppression du compte utilisateur -->

<dialog id="accountDialog" class="modern-dialog">
    <div class="account-deletion">
        <h2>Supprimer mon compte</h2>
        <p>Cette action est irréversible. Toutes vos données seront perdues.</p>

        <form action="/profile/delete" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?');">
            <div class="form-group">
                <label for="password_confirmation">Confirmez votre mot de passe :</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
                <button type="submit" class="btn-submit">Supprimer définitivement mon compte</button>
                <button type="button" class="btn-folio" onclick="this.closest('dialog').close()">Annuler</button>
        </form>
    </div>
</dialog>

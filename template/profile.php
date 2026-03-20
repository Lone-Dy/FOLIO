<?php
include_once(__DIR__ . '/view/header-profil.php');
?>

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
            <?php foreach ($projets as $item): ?>
                <article class="project-card-edit">
                    <header class="project-header project-header-top">
                        <div>
                            <span><?= htmlspecialchars($item['categorie_folio']) ?></span>
                            <h3><?= htmlspecialchars($item['titre']) ?></h3>
                        </div>
                        
                        <div class="status-badge <?= $item['is_published'] ? 'status-published' : 'status-draft' ?>">
                            <?php if ($item['is_published']): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-small"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                PUBLIÉ
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-small"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                BROUILLON
                            <?php endif; ?>
                        </div>
                    </header>

                    <div class="portfolio-inner-projects portfolio-divider">
                        <?php foreach ($item['projets_lies'] as $p): ?>
                            <div class="sub-project-row">
                                <div class="sub-project-info">
                                    <small class="sub-project-type"><?= htmlspecialchars($p->getType()) ?></small>
                                    <p class="sub-project-preview"><?= htmlspecialchars(mb_strimwidth($p->getContenu(), 0, 80, "...")) ?></p>
                                </div>
                                <button class="btn-edit" onclick="openEditProjectModal(<?= $p->getIdProjet() ?>, '<?= addslashes($p->getType()) ?>', '<?= addslashes($p->getContenu()) ?>')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-small"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    Éditer
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="portfolio-footer-actions">
                        <a href="/projet/togglePublish/<?= $item['id_folio'] ?>" class="btn-folio btn-folio-action">
                            <?php if ($item['is_published']): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-small"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                Masquer
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-small"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                Exposer
                            <?php endif; ?>
                        </a>
                        <a href="/projet/delete/<?= $item['id_folio'] ?>" class="btn-delete-link" 
                        onclick="return confirm('Supprimer définitivement ce portfolio ?');">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-small"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>
                    </div>
                </article>
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
            <button type="button" class="btn-text" onclick="this.closest('dialog').close()">Annuler</button>
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
            <button type="button" class="btn-text" onclick="this.closest('dialog').close()">Annuler</button>
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

<dialog id="editProjectDialog" class="modern-dialog">
    <form method="POST" action="/projet/handleEditProjet">
        <h2>Modifier le projet</h2>

        <input type="hidden" name="id_projet" id="edit_id_projet">

        <div class="field">
            <label for="edit_type">Type de projet</label>
            <input type="text" name="type" id="edit_type" required>
        </div>

        <div class="field">
            <label for="edit_title">Titre / Contenu</label>
            <textarea name="title" id="edit_title" rows="4" required></textarea>
        </div>

        <div class="dialog-actions">
            <button type="submit" class="btn-submit">Mettre à jour</button>
            <button type="button" class="btn-text" onclick="this.closest('dialog').close()">Annuler</button>
        </div>
    </form>
</dialog>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
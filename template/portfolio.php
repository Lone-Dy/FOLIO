<?php
include_once(__DIR__ . '/view/header-profil.php');
?>

<main class="portfolio-management-container">
    <header class="portfolio-header">
        <h2>Gestion de mon Portfolio</h2>
        <p class="subtitle">Organisez et publiez vos projets pour la galerie.</p>
    </header>

    <?php if (empty($projets)): ?>
        <div class="empty-state">
            <p>Vous n'avez pas encore de portfolio.</p>
            <button class="btn-submit" onclick="window.location.href='/profile'">Créez-en un ici</button>
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
</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
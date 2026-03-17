<?php
include_once(__DIR__ . '/view/header-profil.php');
?>

<main class="portfolio-edit-container">
    <form action="/projet/handleCreatePortfolio" method="POST" class="portfolio-form" enctype="multipart/form-data" id="portfolioForm">
        <h2>Configurer mon Portfolio</h2>
        <p class="subtitle">Importez vos 3 meilleurs travaux pour pouvoir publier votre portfolio.</p>

        <div class="projects-grid">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="project-card-edit" data-project-index="<?= $i ?>">
                    <header class="project-header">
                        <span>Projet #<?= $i ?></span>
                        <input type="text" name="projets[<?= $i ?>][title]" class="project-title-input" placeholder="Titre du projet..." required>

                        <select name="projects[<?= $i ?>][type]" class="project-type-select">
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
</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
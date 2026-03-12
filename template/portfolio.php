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
                        <select name="projects[<?= $i ?>][type]" class="project-type-select">
                            <option value="web">Développement Web</option>
                            <option value="design">Design Graphique</option>
                            <option value="photo">Photographie</option>
                            <option value="video">Vidéo</option>
                        </select>
                    </header>

                    <div class="drop-zone" id="drop-zone-<?= $i ?>">
                        <span class="drop-zone-prompt">Glissez vos fichiers ou cliquez ici (Max 5)</span>
                        <input type="file" name="project_<?= $i ?>_files[]" class="drop-zone-input" accept="image/*,video/*" multiple>

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
    </form>
</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>
<?php
include_once(__DIR__ . '/view/header-profil.php');
?>
<main class="portfolio-edit-container">
    <form action="/projet/handleCreatePortfolio" method="POST" class="portfolio-form">
        <h2>Configurer mon Portfolio</h2>
        <p class="subtitle">Présentez vos 3 meilleurs travaux (Maximum 3).</p>

        <div class="projects-grid">
            <?php for($i=1; $i<=3; $i++): ?>
            <div class="project-card-edit">
                <span>Projet #<?= $i ?></span>
                <select name="projects[<?= $i ?>][type]">
                    <option value="web">Développement Web</option>
                    <option value="design">Design Graphique</option>
                    <option value="photo">Photographie</option>
                </select>
                <input type="text" name="projects[<?= $i ?>][contenu]" placeholder="Lien vers l'image ou GitHub">
            </div>
            <?php endfor; ?>
        </div>

        <button type="submit" class="btn-save">Enregistrer mon Portfolio</button>
    </form>
</main>
<?php
include_once(__DIR__ . '/view/footer.php');
?>
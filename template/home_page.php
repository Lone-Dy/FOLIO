<?php
include_once(__DIR__ . '/view/header.php');
?>
<main class="home-content">

    <div class="text text0">Glissez.</div>
    <div class="text text1">Déposez.</div>
    <div class="text text2">Publiez.</div>
    <div class="text text3">Rayonnez.</div>

    <?php
    $destination = isset($_SESSION['user_id']) ? '/projet' : '/login#register-section';
    $label = isset($_SESSION['user_id']) ? 'Accéder à mon portfolio' : 'Créer mon portfolio gratuitement';
    ?>

    <div class="cta-home">
        <p class="cta-text">Prêt à partager votre savoir ?</p>
        <a href="<?= $destination ?>" class="btn-main"><?= $label ?></a>
    </div>

    <section class="gallery-section">
    <h2 class="gallery-title">Découvrez les derniers portfolios</h2>
    
    <div class="pinterest-grid">
    <?php foreach ($galleryFeed as $folio): ?>
        <div class="portfolio-card">
            <?php if (!empty($folio['cover_image'])): ?>
                <img src="/<?= htmlspecialchars($folio['cover_image']) ?>" alt="Image Portfolio">
            <?php endif; ?>
            
            <div class="card-info">
                <h3><?= htmlspecialchars($folio['titre']) ?></h3>
                <p>Par <?= htmlspecialchars($folio['prenom'] . ' ' . $folio['nom']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    </section>

    <div id="dummy">
        <div id="back">RETOUR EN HAUT</div>
    </div>
</main>
<?php
include_once(__DIR__ . '/view/footer.php');
?>
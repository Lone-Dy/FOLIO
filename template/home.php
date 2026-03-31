<section class="home-content">

    <h1>Plateforme de partage de portfolios créatifs.</h1>

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
</section>
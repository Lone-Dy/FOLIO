<?php
include_once(__DIR__ . '/view/header.php');
?>

<?php foreach ($galleryData as $folio): ?>
    <section class="portfolio-item">
        <h2><?= htmlspecialchars($folio['titre']) ?> par <?= htmlspecialchars($folio['prenom']) ?></h2>
        
        <div class="projects-grid">
            <?php foreach ($folio['projets'] as $projet): ?>
                <div class="project-card">
                    <h3><?= htmlspecialchars($projet['type']) ?></h3>
                    
                    <div class="media-container">
                        <?php foreach ($projet['medias'] as $media): ?>
                            <?php if ($media['mediaType'] === 'video'): ?>
                                <video src="/uploads/<?= $media['cheminFichier'] ?>" controls></video>
                            <?php else: ?>
                                <img src="/uploads/<?= $media['cheminFichier'] ?>" alt="Image du projet">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>


<?php
include_once(__DIR__ . '/view/footer.php');
?>
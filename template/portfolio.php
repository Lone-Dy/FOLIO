<?php
include_once(__DIR__ . '/view/header-profil.php');
?>

<main class="portfolio-management">
    <h2>Gestion de mon Portfolio</h2>

    <?php if (empty($projets)): ?>
        <p>Vous n'avez pas encore de portfolio.</p>
            <button class="btn-folio" onclick="window.location.href='/profile'">Créez-en un ici</button>
    <?php else: ?>

    <?php foreach ($projets as $folio): ?>
        <div class="portfolio-card">
            <h3><?= htmlspecialchars($folio['titre']) ?></h3>
            
                <span class="status-badge <?= $folio['is_published'] ? 'status-public' : 'status-draft' ?>">
                    <?= $folio['is_published'] ? '● Public' : '○ Brouillon' ?>
                </span>

                <div class="actions">
                    <a href="/projet/togglePublic/<?= $folio['id_folio'] ?>" class="btn-toggle">
                        <?= $folio['is_published'] ? 'Retirer de la galerie' : 'Publier sur la galerie' ?>
                    </a>

                    <a href="/projet/delete/<?= $folio['id_folio'] ?>" 
                        class="btn-delete" 
                        onclick="return confirm('Supprimer définitivement ce portfolio ?')">
                        Supprimer
                    </a>
                </div>
        </div>
    <?php endforeach; ?>

    <?php endif; ?>    
</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>